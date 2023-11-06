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

use Civi\Api4\CustomField;
use Civi\Api4\EckEntity;
use Civi\Api4\Hiorg;
use Civi\Api4\OptionValue;
use Civi\Api4\Relationship;
use Civi\Core\Event\GenericHookEvent;
use Civi\Hiorg\Api\DTO\HiorgUserDTO;
use Civi\Hiorg\ConfigProfile\ConfigProfile;
use CRM_Hiorg_ExtensionUtil as E;

class Synchronize {

  public static function synchronizeContacts(ConfigProfile $configProfile, HiorgUserDTO $hiorgUser): array {
    $result = [];
    $xcmProfile = $configProfile->getXcmProfileName();
    $idTrackerResult = self::identifyContact($configProfile->id, $hiorgUser->id);

    // Synchronize contact data using Extended Contact Manager (XCM) with
    // profile defined in HiOrg-Server API configuration profile.
    $result['contact_id'] = self::synchronizeContactData(
      $configProfile->id,
      $xcmProfile,
      $hiorgUser,
      $idTrackerResult
    );

    // Synchronize bank account data when CiviBanking is installed.
    $result['bank_account'] = self::synchronizeBankAccount(
      $result['contact_id'],
      $hiorgUser
    );

    // Synchronize qualifications with custom entities.
    $result['qualifications'] = self::synchronizeQualifications(
      $result['contact_id'],
      $hiorgUser->qualifikationen
    );

    // Synchronize groups with relationships of type "hiorg_groups".
    $result['relationships'] = self::processGroups(
      $result['contact_id'],
      $configProfile->getOrganisationId(),
      $hiorgUser->gruppen_namen
    );

    // Synchronize educations with custom entities.
    $educationsLastSync = \Civi::settings()->get('hiorg.synchronizeEducations.lastSync') ?? [];
    $oAuthClientId = $configProfile->getOauthClientId();
    $educationsCurrentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');
    $ausbildungenResult = Hiorg::getAusbildungen()
      ->setConfigProfileId($configProfile->id)
      ->setChangedSince($educationsLastSync[$oAuthClientId][$hiorgUser->id] ?? NULL)
      ->setUserId($hiorgUser->id)
      ->execute();
    $result['educations'] = self::synchronizeEducations(
      $result['contact_id'],
      (array) $ausbildungenResult
    );
    $educationsLastSync[$oAuthClientId][$hiorgUser->id] = $educationsCurrentSync;
    \Civi::settings()->set('hiorg.synchronizeEducations.lastSync', $educationsLastSync);

    // TODO: Synchronize "ueberpruefungen": custom entity referencing the contact.

    return $result;
  }

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

  /**
   * @param int $contactId
   * @param \Civi\Hiorg\Api\DTO\HiorgUserDTO $hiorgUser
   *
   * @return array|NULL
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
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

  public static function synchronizeEducations(int $contactId, array $educations): array {
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

    return $result;
  }

  public static function synchronizeOptionValues(array $fields, string $entity = 'Contact') {
    /** @var \Civi\Api4\Service\Spec\SpecGatherer $gatherer */
    $gatherer = \Civi::container()->get('spec_gatherer');
    $spec = $gatherer->getSpec($entity, 'create', TRUE);
    $fieldSpecs = $spec->getFields(array_keys($fields));
    foreach ($fields as $fieldName => $value) {
      $fieldSpec = array_filter($fieldSpecs, function($fieldSpec) use ($fieldName) {
        return $fieldSpec->getName() === $fieldName;
      });
      $fieldSpec = reset($fieldSpec);
      if ($fieldSpec->type == 'Custom') {
        $options = (\CRM_Core_DAO_AllCoreTables::getFullName($entity))::buildOptions('custom_' . $fieldSpec->getCustomFieldId());
        if (is_array($value) && !empty($newOptionValues = array_diff($value, array_keys($options)))) {
          $optionGroupId = CustomField::get(FALSE)
            ->addWhere('id', '=', $fieldSpec->getCustomFieldId())
            ->addWhere('option_group_id', 'IS NOT NULL')
            ->addSelect('option_group_id')
            ->execute()
            ->column('option_group_id')[0];
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

  public static function mapContactParameters(HiorgUserDTO $user): array {
    $mapping = [
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
      'birth_date' => $user->gebdat
        ? \DateTime::createFromFormat('Y-m-d', $user->gebdat)->format('Y-m-d')
        : NULL,

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
      // TODO: Un-comment once the dates come in the correct format.
      //      'hiorg_membership_data.membership_start_date' => $user->mitglied_seit
      //        ? \DateTime::createFromFormat('Y-m-d', $user->mitglied_seit)
      //          ->format(('Y-m-d'))
      //        : NULL,
      //      'hiorg_membership_data.membership_end_date' => $user->austritt_datum
      //        ? \DateTime::createFromFormat('Y-m-d', $user->austritt_datum)
      //          ->format(('Y-m-d'))
      //        : NULL,
      //      'hiorg_membership_data.membership_transfer_date' => $user->wechseljgddat
      //        ? \DateTime::createFromFormat('Y-m-d', $user->wechseljgddat)
      //          ->format(('Y-m-d'))
      //        : NULL,

      // Driving license fields.
      'driving_license.classes' => $user->fahrerlaubnis['klassen'] ?: [],
      'driving_license.restriction' => $user->fahrerlaubnis['beschraenkung'],
      'driving_license.license_number' => $user->fahrerlaubnis['fuehrerscheinnummer'],
      'driving_license.license_date' => $user->fahrerlaubnis['fuehrerscheindatum']
        ? \DateTime::createFromFormat('Y-m-d', $user->fahrerlaubnis['fuehrerscheindatum'])
          ->format(('Y-m-d'))
        : NULL,

      // TODO: $user->username as IdentityTracker record (new type).
    ];

    // Dispatch event for custom mapping.
    $event = GenericHookEvent::create(['mapping' => $mapping, 'user' => $user]);
    \Civi::dispatcher()->dispatch('civi.hiorg.mapParameters', $event);

    return $event->mapping;
  }

  /**
   * @param int $configProfileId
   * @param string $hiorgUserId
   *   The HiOrg-Server user ID to pass to ID Tracker.
   *
   * @return int|null
   *   The CiviCRM Contact ID.
   * @throws \CRM_Core_Exception
   */
  public static function identifyContact(int $configProfileId, string $hiorgUserId): ?int {
    $idTrackerResult = civicrm_api3(
      'Contact',
      'findbyidentity',
      [
        'identifier_type' => 'hiorg_user',
        'identifier' => $hiorgUserId,
        'context' => $configProfileId,
      ]
    );
    return $idTrackerResult['id'] ?? NULL;
  }

  public static function getPrefixId($prefix) {
    try {
      $prefix_id = OptionValue::get(FALSE)
        ->addSelect('value')
        ->addWhere('option:group_id:name', '=', 'individual_prefix')
        ->addWhere('name', '=', $prefix)
        ->addWhere('is_active', '=', TRUE)
        ->execute()
        ->single()['id'];
    }
    catch (\Exception $exception) {
      return NULL;
    }
  }

  /**
   * @param int $configProfileId
   *   The XCM profile name.
   * @param string $xcmProfile
   *   Contact parameters to pass to XCM.
   * @param array $params
   *   The CiviCRM contact ID of the already idfentified contact to pass to XCM.
   * @param int|null $contactId
   * @param string|null $hiorgUserId
   *   The HiOrg-Server user ID to add as ID Tracker record on the contact.
   *
   * @return int
   *   The CiviCRM contact ID of the synchronized contact.
   * @throws \CRM_Core_Exception
   */
  public static function synchronizeContactData(int $configProfileId, string $xcmProfile, HiorgUserDTO $hiorgUser, ?int $contactId = NULL): int {
    $params = self::mapContactParameters($hiorgUser);

    if ($contactId) {
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
      civicrm_api3(
        'Contact',
        'addidentity',
        [
          'contact_id' => $xcmResult['id'],
          'identifier_type' => 'hiorg_user',
          'identifier' => $hiorgUser->id,
          'context' => $configProfileId,
        ]
      );
    }

    return (int) $xcmResult['id'];
  }

}
