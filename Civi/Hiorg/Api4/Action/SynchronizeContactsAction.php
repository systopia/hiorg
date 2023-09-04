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

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\EckEntity;
use Civi\Api4\Generic\Result;
use Civi\Api4\Hiorg;
use Civi\Api4\OptionGroup;
use Civi\Api4\OptionValue;
use Civi\Api4\Relationship;
use Civi\Hiorg\Api\DTO\HiorgUserDTO;
use Civi\Hiorg\ConfigProfile;

class SynchronizeContactsAction extends AbstractHiorgAction {

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $lastSync = \Civi::settings()->get('hiorg.synchronizeContacts.lastSync');

    // Retrieve HiOrg user data via HiOrg-Server API.
    $personalResult = Hiorg::getPersonal()
      ->setConfigProfileId($this->configProfileId)
      ->setChangedSince($lastSync)
      ->execute();

    $xcmProfile = $this->_configProfile->getXcmProfileName();
    $syncResult = [];
    foreach ($personalResult as $record) {
      $hiorgUserResult = [];
      $hiorgUser = HiorgUserDTO::create($record);
      $idTrackerResult = $this->identifyContact($hiorgUser->id);

      // Synchronize contact data using Extended Contact Manager (XCM) with
      // profile defined in HiOrg-Server API configuration profile.
      $hiorgUserResult['contact_id'] = $this->synchronizeContactData(
        $xcmProfile,
        self::mapParameters($hiorgUser),
        $idTrackerResult,
        $hiorgUser->id
      );

      // Synchronize qualifications with custom entities.
      $hiorgUserResult['qualifications'] = $this->synchronizeQualifications(
        $hiorgUserResult['contact_id'],
        $hiorgUser->qualifikationen
      );

      // TODO: Synchronize "ausbildungen": custom entity "ausbildungen instance" referencing the contact and a "ausbildung" custom entity.

      // Synchronize groups with relationships of type "hiorg_groups".
      $hiorgUserResult['relationships'] = $this->processGroups(
        $hiorgUserResult['contact_id'],
        $this->_configProfile->getOrganisationId(),
        $hiorgUser->gruppen_namen
      );

      $syncResult[] = $hiorgUserResult;
    }

    $result->exchangeArray($syncResult);
  }

  /**
   * @param int $hiorgUserId
   *   The HiOrg-Server user ID to pass to ID Tracker.
   *
   * @return string|null
   *   The CiviCRM Contact ID.
   * @throws \CRM_Core_Exception
   */
  protected function identifyContact(string $hiorgUserId): ?int {
    $idTrackerResult = civicrm_api3(
      'Contact',
      'findbyidentity',
      [
        'identifier_type' => 'hiorg_user',
        'identifier' => $hiorgUserId,
        'context' => $this->configProfileId,
      ]
    );
    return $idTrackerResult['id'] ?? NULL;
  }

  /**
   * @param string $xcmProfile
   *   The XCM profile name.
   * @param array $params
   *   Contact parameters to pass to XCM.
   * @param int|null $contactId
   *   The CiviCRM contact ID of the already idfentified contact to pass to XCM.
   * @param string|null $hiorgUserId
   *   The HiOrg-Server user ID to add as ID Tracker record on the contact.
   *
   * @return int
   *   The CiviCRM contact ID of the synchronized contact.
   * @throws \CRM_Core_Exception
   */
  protected function synchronizeContactData(string $xcmProfile, array $params, ?int $contactId = NULL, ?string $hiorgUserId = NULL) {
    if ($contactId) {
      $params['id'] = $contactId;
    }
    $xcmResult = civicrm_api3(
      'Contact',
      'createifnotexists',
      ['xcm_profile' => $xcmProfile] + $params
    );
    if (empty($xcmResult['id'])) {
      throw new Exception(E::ts('Error retrieving/creating contact with Extended Contact Manager (XCM).'));
    }

    // Add HiOrg-Server user ID as Identity Tracker ID.
    if (!$contactId && !empty($hiorgUserId)) {
      civicrm_api3(
        'Contact',
        'addidentity',
        [
          'contact_id' => $xcmResult['id'],
          'identifier_type' => 'hiorg_user',
          'identifier' => $hiorgUserId,
          'context' => $this->configProfileId,
        ]
      );
    }

    return (int) $xcmResult['id'];
  }

  protected function synchronizeQualifications(int $contactId, array $qualifications) {
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
      $existing = \Civi\Api4\EckEntity::get('Hiorg_Qualification')
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
        ->addRecord([
          'subtype:name' => $qualification->name_kurz,
          'title' => $qualification->name,
          'Eck_Hiorg_Qualification.Date_acquired' => $qualification->erwerb_datum,
          'Eck_Hiorg_Qualification.Contact' => $contactId,
        ])
        ->setMatch(['id'])
        ->execute();
    }

    return $result;
  }

  protected function processGroups($contactId, $organisationId, $groups) {
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

  protected static function mapParameters(HiorgUserDTO $user): array {
    return [
      'first_name' => $user->vorname,
      'last_name' => $user->nachname,
      'phone' => $user->telpriv,
      'phone2' => $user->teldienst,
      'phone3' => $user->handy,
      'email' => $user->email,
      'street_address' => $user->adresse,
      'postal_code' => $user->plz,
      'city' => $user->ort,
      'country:label' => $user->land, // TODO: Translate to country_id.
      'birth_date' => $user->gebdat
        ? \DateTime::createFromFormat('d.m.Y', $user->gebdat)->format('Y-m-d')
        : NULL,
      'driving_license.classes' => $user->fahrerlaubnis['klassen'],
      'driving_license.restriction' => $user->fahrerlaubnis['beschraenkung'],
      'driving_license.license_number' => $user->fahrerlaubnis['fuehrerscheinnummer'],
      'driving_license.license_date' => $user->fahrerlaubnis['fuehrerscheindatum']
        ? \DateTime::createFromFormat('Y-m-d', $user->fahrerlaubnis['fuehrerscheindatum'])->format(('Y-m-d'))
        : NULL,
      // TODO: $user->leitung as (boolean) custom field.
    ];
  }

}
