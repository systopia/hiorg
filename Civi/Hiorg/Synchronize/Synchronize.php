<?php
/*-------------------------------------------------------+
| SYSTOPIA HiOrg-Server API                              |
| Copyright (C) 2023 SYSTOPIA                            |
| Author: J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

namespace Civi\Hiorg\Synchronize;

use Civi\Api4\Activity;
use Civi\Api4\ActivityContact;
use Civi\Api4\CustomField;
use Civi\Api4\EckEntity;
use Civi\Api4\Hiorg;
use Civi\Api4\OptionValue;
use Civi\Api4\Relationship;
use Civi\Core\Event\GenericHookEvent;
use Civi\Funding\Permission\ContactRelation\Types\Contact;
use Civi\Hiorg\Event\SynchronizeContactsEvent;
use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;
use Civi\Hiorg\HiorgApi\DTO\HiorgVerificationDTO;
use Civi\Hiorg\HiorgApi\DTO\HiorgVolunteerHoursDTO;
use Civi\Hiorg\ConfigProfiles\ConfigProfile;
use Civi\Hiorg\Event\MapContactParametersEvent;
use CRM_Hiorg_ExtensionUtil as E;

class Synchronize {

  public static function synchronizeContacts(ConfigProfile $configProfile, HiorgUserDTO $hiorgUser): array {
    $result = [];

    // Synchronize contact data using Extended Contact Manager (XCM) with
    // profile defined in HiOrg-Server API configuration profile.
    $result['contact_id'] = self::synchronizeContactData(
      $configProfile->getXcmProfileName(),
      $hiorgUser,
      $configProfile->id
    );

    // Synchronize bank account data when CiviBanking is installed.
    $result['bank_account'] = self::synchronizeBankAccount(
      $result['contact_id'],
      $hiorgUser
    );

    // Synchronize groups with relationships of type "hiorg_groups".
    $result['relationships'] = self::processGroups(
      $result['contact_id'],
      $configProfile->getOrganisationId(),
      $hiorgUser->gruppen_namen
    );

    // Synchronize qualifications with custom entities.
    $result['qualifications'] = self::synchronizeQualifications(
      $result['contact_id'],
      $hiorgUser->qualifikationen
    );

    // Synchronize educations with custom entities.
    $result['educations'] = self::synchronizeEducations(
      $result['contact_id'],
      $hiorgUser,
      $configProfile
    );

    // Synchronize verifications with custom entities.
    $result['verifications'] = self::synchronizeVerifications(
      $result['contact_id'],
      $hiorgUser,
      $configProfile
    );

    // Dispatch event for custom synchronization.
    $event = new SynchronizeContactsEvent($hiorgUser, $configProfile, $result['contact_id'], $result);
    \Civi::dispatcher()->dispatch(SynchronizeContactsEvent::NAME, $event);
    $result = $event->getResults();

    return $result;
  }

  public static function synchronizeVolunteerHours(ConfigProfile $configProfile, HiorgVolunteerHoursDTO $hiorgVolunteerHours): array {
    $result = [];

    if (!isset($hiorgVolunteerHours->user_id)) {
      throw new \Exception(
        E::ts('HiOrg-Server user ID missing in volunteer hours record.')
      );
    }
    $result['contact_id'] = ContactIdentity::identifyContact($configProfile->id, $hiorgVolunteerHours->user_id);
    if (NULL === $result['contact_id']) {
      throw new \Exception(
        E::ts(
          'Could not identify contact for HiOrg-Server user ID %1',
          [1 => $hiorgVolunteerHours->user_id]
        )
      );
    }

    // Retrieve existing activity.
    $existing = Activity::get(FALSE)
      ->addWhere('activity_type_id:name', '=', 'hiorg_volunteer_hours')
      ->addWhere('hiorg_volunteer_hours.organization', '=', $configProfile->getOrganisationId())
      ->addWhere('hiorg_volunteer_hours.hiorg_id', '=', $hiorgVolunteerHours->id)
      ->execute();

    // Save  (create or update) activity.
    $record = [
      'activity_type_id:name' => 'hiorg_volunteer_hours',
      'subject' => E::ts('HiOrg-Server Volunteer Hours'),
      'activity_date_time' => self::formatDate(
        $hiorgVolunteerHours->von,
        'Y-m-d\TH:i:sP',
        'Y-m-d H:i:s'
      ),
      'duration' => $hiorgVolunteerHours->stunden * 60,
      'status_id:name' => 'Completed',
      'source_contact_id' => \CRM_Core_Session::getLoggedInContactID(),
      'hiorg_volunteer_hours.hiorg_id' => $hiorgVolunteerHours->id,
      'hiorg_volunteer_hours.start_date' => self::formatDate(
        $hiorgVolunteerHours->von,
        'Y-m-d\TH:i:sP',
        'Y-m-d H:i:s'
      ),
      'hiorg_volunteer_hours.end_date' => self::formatDate(
        $hiorgVolunteerHours->bis,
        'Y-m-d\TH:i:sP',
        'Y-m-d H:i:s'
      ),
      'hiorg_volunteer_hours.hours' => $hiorgVolunteerHours->stunden,
      'hiorg_volunteer_hours.call_out_km' => $hiorgVolunteerHours->anfahrt_km,
      'hiorg_volunteer_hours.occasion:label' => $hiorgVolunteerHours->anlass_beschreibung,
      'hiorg_volunteer_hours.organization' => $configProfile->getOrganisationId(),
    ];

    // Synchronize occasion option value.
    OptionValue::save(FALSE)
      ->addRecord([
        'option_group_id:name' => 'hiorg_volunteer_hours_occasion',
        'label' => $hiorgVolunteerHours->anlass_beschreibung,
        'grouping' => $hiorgVolunteerHours->anlass_typ,
      ])
      ->setMatch(['option_group_id', 'label', 'grouping'])
      ->execute();

    if ($existing->count()) {
      $record['id'] = $existing->first()['id'];
    }
    $result['activity'] = Activity::save(FALSE)
      ->addRecord($record)
      ->setMatch(['id'])
      ->execute()
      ->single();

    // Save ActivityContact with the HiOrg-Server user contact as "target".
    $existingActivityContact = ActivityContact::get(FALSE)
      ->addSelect('id')
      ->addWhere('activity_id', '=', $result['activity']['id'])
      ->addWhere('contact_id', '=', $result['contact_id'])
      ->addWhere('record_type_id:name', '=', 'Activity Targets')
      ->execute();
    $record = [
      'activity_id' => $result['activity']['id'],
      'contact_id' => $result['contact_id'],
      'record_type_id:name' => 'Activity Targets',
    ];
    if ($existingActivityContact->count()) {
      $record['id'] = $existingActivityContact->first()['id'];
    }
    $result['activity_contact'] = ActivityContact::save(FALSE)
      ->addRecord($record)
      ->setMatch(['id'])
      ->execute()
      ->single();

    return $result;
  }

  /**
   * Synchronizes HiOrg-Server users' group memberships with CiviCRM
   * relationships.
   *
   * @param $contactId
   * @param $organisationId
   * @param $groups
   *
   * @return array
   * @throws \CRM_Core_Exception
   */
  public static function processGroups($contactId, $organisationId, $groups): array {
    $existingGroups = OptionValue::get(FALSE)
      ->addSelect('value', 'name')
      ->addWhere('option_group_id:name', '=', 'hiorg_groups')
      ->execute()
      ->indexBy('value')
      ->column('name');
    $activeGroups = Relationship::get(FALSE)
      ->addSelect('id', 'hiorg_relationship_groups.hiorg_group:name')
      ->addWhere('relationship_type_id:name', '=', 'hiorg_groups')
      ->addWhere('is_active', '=', TRUE)
      ->addClause('OR',
        ['end_date', '>=', (new \DateTime())->format('Y-m-d')],
        ['end_date', 'IS EMPTY']
      )
      ->addWhere('contact_id_a', '=', $contactId)
      ->addWhere('contact_id_b', '=', $organisationId)
      ->execute()
      ->indexBy('id')
      ->column('hiorg_relationship_groups.hiorg_group:name');

    $result = [];

    // End group memberships for groups not being submitted (anymore).
    foreach (array_diff($activeGroups, $groups) as $relationshipId => $groupToEnd) {
      $result['ended'] = Relationship::update(FALSE)
        ->addWhere('id', '=', $relationshipId)
        ->addValue('end_date', (new \DateTime())->format('Y-m-d'))
        ->addValue('is_active', FALSE)
        ->execute();
    }

    // Add group memberships for submitted groups without an active
    // relationship.
    foreach (array_diff($groups, $activeGroups) as $groupToAdd) {
      if (!in_array($groupToAdd, $existingGroups)) {
        $addedGroup = OptionValue::create(FALSE)
          ->addValue('option_group_id:name', 'hiorg_groups')
          ->addValue('name', $groupToAdd)
          ->addValue('label', $groupToAdd)
          ->execute()
          ->single();
        $existingGroups[$addedGroup['value']] = $groupToAdd;
      }

      $result['created'] = Relationship::create(FALSE)
        ->addValue('relationship_type_id:name', 'hiorg_groups')
        ->addValue('contact_id_a', $contactId)
        ->addValue('contact_id_b', $organisationId)
        ->addValue('hiorg_relationship_groups.hiorg_group', array_search($groupToAdd, $existingGroups))
        ->execute();
    }

    return $result;
  }

  public static function synchronizeQualifications(int $contactId, array $qualifications): array {
    // Load ECK sub-types for the "Hiorg_Qualification" entity type.
    static $eckSubTypes;
    if (!isset($eckSubTypes)) {
      $eckSubTypes = OptionValue::get(FALSE)
        ->addWhere('option_group_id:name', '=', 'eck_sub_types')
        ->addWhere('grouping', '=', 'Hiorg_Qualification')
        ->execute()
        ->indexBy('name')
        ->getArrayCopy();
    }

    $result = [];
    foreach ($qualifications as $qualification) {
      // Add ECK sub-type if it does not yet exist.
      if (!array_key_exists($qualification->name_kurz, $eckSubTypes)) {
        $eckSubTypes[$qualification->name_kurz] = OptionValue::create(FALSE)
          ->addValue('option_group_id:name', 'eck_sub_types')
          ->addValue('grouping', 'Hiorg_Qualification')
          ->addValue('name', $qualification->name_kurz)
          ->addValue('label', $qualification->name)
          ->execute();
      }
      // Retrieve existing qualification for the contact.
      // TODO: Retrieve only once per contact, group by type (name).
      $existing = EckEntity::get('Hiorg_Qualification')
        ->addWhere('subtype:name', '=', $qualification->name_kurz)
        ->addWhere('Eck_Hiorg_Qualification.Contact', '=', $contactId)
        ->execute();

      // Save  (create or update) custom entity.
      $record = [
        'subtype:name' => $qualification->name_kurz,
        'title' => $qualification->name,
        'Eck_Hiorg_Qualification.Date_acquired' => $qualification->erwerb_datum,
        'Eck_Hiorg_Qualification.Contact' => $contactId,
      ];
      if ($existing->count()) {
        $record['id'] = $existing->first()['id'];
      }
      $result[] = EckEntity::save('Hiorg_Qualification')
        ->addRecord($record)
        ->setMatch(['id'])
        ->execute()
        ->getArrayCopy();
    }

    return $result;
  }

  public static function synchronizeVerifications(int $contactId, HiorgUserDTO $hiorgUser, ConfigProfile $configProfile): array {
    $result = [];
    $oAuthClientId = $configProfile->getOauthClientId();
    $verificationsLastSync = \Civi::settings()->get('hiorg.synchronizeVerifications.lastSync') ?? [];
    $verificationsCurrentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');
    // TODO: Introduce dedicated Result classes.
    /* @var HiorgVerificationDTO[] $verifications */
    $verifications = Hiorg::getUeberpruefungen(FALSE)
      ->setConfigProfileId($configProfile->id)
      ->setChangedSince($verificationsLastSync[$oAuthClientId][$hiorgUser->id] ?? NULL)
      ->setUserId($hiorgUser->id)
      ->execute()
      ->getArrayCopy();

    if (!empty($verifications)) {
      // Load ECK sub-types for the "Hiorg_Verification" entity type.
      static $eckSubTypes;
      if (!isset($eckSubTypes)) {
        $eckSubTypes = OptionValue::get(FALSE)
          ->addWhere('option_group_id:name', '=', 'eck_sub_types')
          ->addWhere('grouping', '=', 'Hiorg_Verification')
          ->execute()
          ->indexBy('name')
          ->getArrayCopy();
      }

      foreach ($verifications as $verification) {
        // Add ECK sub-type if it does not yet exist.
        if (!array_key_exists($type = $verification->bezeichnung, $eckSubTypes)) {
          $eckSubTypes[$type] = OptionValue::create(FALSE)
            ->addValue('option_group_id:name', 'eck_sub_types')
            ->addValue('grouping', 'Hiorg_Verification')
            ->addValue('name', $type)
            ->addValue('label', $verification->bezeichnung)
            ->execute()
            ->getArrayCopy();
        }
        // Retrieve existing verification for the contact.
        // TODO: Retrieve only once per contact, group by type (name).
        $existing = EckEntity::get('Hiorg_Verification')
          ->addWhere('subtype:name', '=', $type)
          ->addWhere('Eck_Hiorg_Verification.Contact', '=', $contactId)
          ->addWhere('Eck_Hiorg_Verification.Hiorg_id', '=', $verification->id)
          ->execute();

        // Save  (create or update) custom entity.
        $record = [
          'subtype:name' => $type,
          'title' => $verification->bezeichnung,
          'Eck_Hiorg_Verification.Contact' => $contactId,
          'Eck_Hiorg_Verification.Hiorg_id' => $verification->id,
          'Eck_Hiorg_Verification.Date_last_revision' => $verification->letzte_pruefung,
          'Eck_Hiorg_Verification.Date_next_revision' => $verification->naechste_pruefung,
          'Eck_Hiorg_Verification.Revision_result' => $verification->pruefergebnis_bestanden,
          'Eck_Hiorg_Verification.Result_restriction' => $verification->pruefergebnis_einschraenkungen,
        ];
        if ($existing->count()) {
          $record['id'] = $existing->first()['id'];
        }
        $result[] = EckEntity::save('Hiorg_Verification')
          ->addRecord($record)
          ->setMatch(['id'])
          ->execute()
          ->getArrayCopy();
      }
    }

    $verificationsLastSync[$oAuthClientId][$hiorgUser->id] = $verificationsCurrentSync;
    \Civi::settings()->set('hiorg.synchronizeVerifications.lastSync', $verificationsLastSync);

    return $result;
  }

  /**
   * Synchronizes HiOrg-Server users' bank account information with CiviBanking
   * account entities, if CiviBanking is installed.
   *
   * @param int $contactId
   * @param \Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO $hiorgUser
   *
   * @return array|NULL
   * @throws \CRM_Core_Exception
   */
  public static function synchronizeBankAccount(int $contactId, HiorgUserDTO $hiorgUser): ?array {
    if (
      \CRM_Extension_System::singleton()
        ->getManager()
        ->getStatus('org.project60.banking') === \CRM_Extension_Manager::STATUS_INSTALLED
      && !empty($hiorgUser->iban)
    ) {
      $ibanReferenceTypeId = OptionValue::get(FALSE)
        ->addSelect('id')
        ->addWhere('option_group_id.name', '=', 'civicrm_banking.reference_types')
        ->addWhere('value', '=', 'IBAN')
        ->addWhere('is_active', '=', TRUE)
        ->execute()
        ->single()['id'];
      // find existing references
      $existing_references = civicrm_api3('BankingAccountReference', 'get', [
        'reference' => $hiorgUser->iban,
        'reference_type_id' => $ibanReferenceTypeId,
        'option.limit' => 0,
      ]);

      // get the accounts for this
      $bank_account_ids = [];
      foreach ($existing_references['values'] as $account_reference) {
        $bank_account_ids[] = $account_reference['ba_id'];
      }
      if (!empty($bank_account_ids)) {
        $contact_bank_accounts = civicrm_api3('BankingAccount', 'get', [
          'id' => ['IN' => $bank_account_ids],
          'contact_id' => $contactId,
          'option.limit' => 1,
        ]);
        if ($contact_bank_accounts['count']) {
          // bank account already exists with the contact
          $account = reset($contact_bank_accounts['values']);
        }
      }

      if (!isset($account)) {
        // if we get here, that means that there is no such bank account
        //  => create one
        $data = [
          'BIC' => $hiorgUser->bic,
          'country' => substr($hiorgUser->iban, 0, 2)
        ];
        $account = civicrm_api3('BankingAccount', 'create', [
          'contact_id' => $contactId,
          'data_parsed' => json_encode($data),
        ]);

        $bank_account_reference = civicrm_api3('BankingAccountReference', 'create', [
          'reference' => $hiorgUser->iban,
          'reference_type_id' => $ibanReferenceTypeId,
          'ba_id' => $account['id'],
        ]);
      }
    }
    return $account ?? NULL;
  }

  public static function synchronizeEducations(int $contactId, HiorgUserDTO $hiorgUser, ConfigProfile $configProfile): array {
    $oAuthClientId = $configProfile->getOauthClientId();
    $educationsLastSync = \Civi::settings()->get('hiorg.synchronizeEducations.lastSync') ?? [];
    $educationsCurrentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $educations = Hiorg::getAusbildungen(FALSE)
      ->setConfigProfileId($configProfile->id)
      ->setChangedSince($educationsLastSync[$oAuthClientId][$hiorgUser->id] ?? NULL)
      ->setUserId($hiorgUser->id)
      ->execute();

    // Load ECK sub-types for the "Hiorg_Education" entity type.
    static $eckSubTypes;
    if (!isset($eckSubTypes)) {
      $eckSubTypes = OptionValue::get(FALSE)
        ->addWhere('option_group_id:name', '=', 'eck_sub_types')
        ->addWhere('grouping', '=', 'Hiorg_Education')
        ->execute()
        ->indexBy('name')
        ->getArrayCopy();
    }

    $result = [];
    foreach ($educations as $education) {
      // Extract type from description (everything before the first " - ").
      $type = substr($education->attributes->bezeichnung, 0, strpos($education->attributes->bezeichnung, ' - '));
      if (!array_key_exists($type, $eckSubTypes)) {
        // Use "Generic" education type if it doesn't map to existing ones.
        $type = 'Generic';
      }
      // Retrieve existing educations for the contact.
      $existing = EckEntity::get('Hiorg_Education')
        ->addWhere('subtype:name', '=', $type)
        ->addWhere('Eck_Hiorg_Education.Contact', '=', $contactId)
        ->addWhere('Eck_Hiorg_Education.Hiorg_id', '=', $education->id)
        ->execute();

      // Save  (create or update) custom entity.
      $record = [
        'subtype:name' => $type,
        'title' => $education->attributes->bezeichnung,
        'Eck_Hiorg_Education.Date_acquired' => $education->attributes->datum,
        'Eck_Hiorg_Education.Date_expires' => $education->attributes->gueltig_bis,
        'Eck_Hiorg_Education.Contact' => $contactId,
        'Eck_Hiorg_Education.Hiorg_id' => $education->id,
      ];
      if ($existing->count()) {
        $record['id'] = $existing->first()['id'];
      }
      $result[] = EckEntity::save('Hiorg_Education')
        ->addRecord($record)
        ->setMatch(['id'])
        ->execute()
        ->getArrayCopy();
    }

    $educationsLastSync[$oAuthClientId][$hiorgUser->id] = $educationsCurrentSync;
    \Civi::settings()->set('hiorg.synchronizeEducations.lastSync', $educationsLastSync);

    return $result;
  }

  /**
   * Creates missing option values for custom fields mapped to HiOrg-Server
   * users' (or other entities/records) attributes.
   *
   * @param array $fields
   * @param string $entity
   *
   * @return void
   * @throws \CRM_Core_Exception
   */
  public static function synchronizeOptionValues(array $fields, string $entity = 'Contact') {
    /** @var \Civi\Api4\Service\Spec\SpecGatherer $gatherer */
    $gatherer = \Civi::container()->get('spec_gatherer');
    $fieldSpecs = $gatherer->getAllFields($entity, 'create');
    foreach ($fields as $fieldName => $value) {
      $fieldSpec = $fieldSpecs[$fieldName];
      if ($fieldSpec['type'] == 'Custom') {
        if (method_exists(\CRM_Core_DAO_AllCoreTables::class, 'getDAONameForEntity')) {
          $options = (\CRM_Core_DAO_AllCoreTables::getDAONameForEntity($entity))::buildOptions('custom_' . $fieldSpec['custom_field_id']);
        }
        else {
          $options = (\CRM_Core_DAO_AllCoreTables::getFullName($entity))::buildOptions('custom_' . $fieldSpec['custom_field_id']);
        }
        $value = (array) $value;
        if (is_array($value) && !empty($newOptionValues = array_diff($value, array_keys($options)))) {
          $optionGroupId = CustomField::get(FALSE)
            ->addWhere('id', '=', $fieldSpec['custom_field_id'])
            ->addWhere('option_group_id', 'IS NOT NULL')
            ->addSelect('option_group_id')
            ->execute()
            ->column('option_group_id')[0];
          if (isset($optionGroupId)) {
            foreach ($newOptionValues as $newOptionValue) {
              OptionValue::create(FALSE)
                ->addValue('option_group_id', $optionGroupId)
                ->addValue('name', $newOptionValue)
                ->addValue('value', $newOptionValue)
                ->addValue('label', $newOptionValue)
                ->execute();
            }
          }
        }
      }
    }
  }

  public static function mapContactParameters(HiorgUserDTO $user): array {
    $mappings = [
      'prefix_id' => self::getPrefixId($user->anrede),
      'first_name' => $user->vorname,
      'last_name' => $user->nachname,
      'phone' => $user->telpriv,
      'phone2' => $user->teldienst,
      'phone3' => $user->handy,
      'email' => $user->email,
      'street_address' => $user->adresse,
      'postal_code' => $user->plz,
      'city' => $user->ort,
      // TODO: Validate country label or map, e. g. with similar_text().
      'country:label' => $user->land,
      'birth_date' => self::formatDate($user->gebdat ?? ''),

      // CiviCRM custom fields.
      'hiorg_contact_data.birth_place' => $user->gebort,
      'hiorg_contact_data.emergency_info' => $user->angehoerige,
      'hiorg_contact_data.profession' => $user->beruf,
      'hiorg_contact_data.employer' => $user->arbeitgeber,
      'hiorg_contact_data.position' => $user->funktion,
      'hiorg_contact_data.management_function' => (bool) $user->leitung,
      'hiorg_contact_data.note' => $user->bemerkung,

      // Membership fields.
      'hiorg_membership_data.membership_number' => $user->mitgliednr,
      'hiorg_membership_data.membership_start_date' => self::formatDate($user->mitglied_seit ?? ''),
      'hiorg_membership_data.membership_end_date' => self::formatDate($user->austritt_datum ?? ''),
      'hiorg_membership_data.membership_transfer_date' => self::formatDate($user->wechseljgddat ?? ''),

      // Driving license fields.
      'driving_license.classes' => $user->fahrerlaubnis['klassen'] ?? [],
      'driving_license.restriction' => $user->fahrerlaubnis['beschraenkung'] ?? '',
      'driving_license.license_number' => $user->fahrerlaubnis['fuehrerscheinnummer'] ?? '',
      'driving_license.license_date' => self::formatDate($user->fahrerlaubnis['fuehrerscheindatum'] ?? ''),

      // TODO: $user->username as IdentityTracker record (new type "HiOrg-Server user name").
    ];

    // Dispatch event for custom mapping.
    $event = new MapContactParametersEvent($user, $mappings);
    \Civi::dispatcher()->dispatch(MapContactParametersEvent::NAME, $event);
    return $event->getMappings();
  }

  public static function getPrefixId($prefix) {
    try {
      $prefix_id = OptionValue::get(FALSE)
        ->addSelect('value')
        ->addWhere('option_group_id:name', '=', 'individual_prefix')
        ->addWhere('name', '=', $prefix)
        ->addWhere('is_active', '=', TRUE)
        ->execute()
        ->single()['value'];
    }
    catch (\Exception $exception) {
      $prefix_id = NULL;
    }
    return $prefix_id;
  }

  public static function formatDate(string $date, string $inputFormat = 'Y-m-d', string $outputFormat = 'Y-m-d'): ?string {
    return $date && ($dateParsed = \DateTime::createFromFormat($inputFormat, $date))
      ? $dateParsed->format($outputFormat)
      : NULL;
  }

  /**
   * @param string|null $hiorgUserId
   *   The HiOrg-Server API configuration profile ID.
   * @param array $params
   *   Contact parameters to pass to XCM.
   * @param mixed $identityContext
   *   The HiOrg-Server user ID to add as ID Tracker record on the contact.
   *
   * @return int
   *   The CiviCRM contact ID of the synchronized contact.
   * @throws \CRM_Core_Exception
   */
  public static function synchronizeContactData(string $xcmProfile, HiorgUserDTO $hiorgUser, $identityContext): int {
    $params = self::mapContactParameters($hiorgUser);

    if ($contactId = ContactIdentity::identifyContact($identityContext, $hiorgUser->id)) {
      $params['id'] = $contactId;
    }

    self::synchronizeOptionValues($params, 'Contact');

    $xcmResult = civicrm_api3(
      'Contact',
      'createifnotexists',
      ['xcm_profile' => $xcmProfile] + $params
    );
    if (empty($xcmResult['id'])) {
      throw new \Exception(E::ts('Error retrieving/creating contact with Extended Contact Manager (XCM).'));
    }

    // Add HiOrg-Server user ID as Identity Tracker ID.
    if (!$contactId && !empty($hiorgUser->id)) {
      ContactIdentity::addIdentity($xcmResult['id'], $hiorgUser->id, $identityContext);
    }

    return (int) $xcmResult['id'];
  }

}
