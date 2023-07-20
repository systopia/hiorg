<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Api4\Hiorg;
use Civi\Api4\OptionValue;
use Civi\Api4\Relationship;
use Civi\Hiorg\Api\DTO\HiorgUserDTO;
use Civi\Hiorg\ConfigProfile;

class SynchronizeContactsAction extends AbstractHiorgAction {

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    //TODO: Retrieve $changedSince from synchronisation log (to be implemented).
    $changedSince = (new \DateTime())->format('Y-m-d\TH:i:sP');

    // Retrieve HiOrg user data via HiOrg-Server API.
    $personalResult = Hiorg::getPersonal()
      ->setConfigProfileId($this->configProfileId)
      ->setChangedSince($changedSince)
      // TODO: Remove when accessing "/personal" without "/self" is allowed,
      //       this is for testing.
      ->setSelf(TRUE)
      ->execute();

    $configProfile = ConfigProfile::getById($this->configProfileId);
    $xcmProfile = $configProfile->getXcmProfileName();
    foreach ($personalResult as $record) {
      $hiorgUser = new HiorgUserDTO($record);

      // Synchronize contact data using Extended Contact Manager (XCM) with
      // profile defined in HiOrg-Server API configuration profile.
      $xcmResult = civicrm_api3(
        'Contact',
        'createifnotexists',
        ['xcm_profile' => $xcmProfile] + self::mapParameters($hiorgUser)
      );
      if (empty($contactId = $xcmResult['id'])) {
        throw new Exception(E::ts('Error retrieving/creating contact with Extended Contact Manager (XCM).'));
      }

      // TODO: Synchronize "qualifikationen": custom entity "qualifikation instance" referencing the contact and a "qualifikation" custom entity.

      // TODO: Synchronize "ausbildungen": custom entity "ausbildungen instance" referencing the contact and a "ausbildung" custom entity.

      // Synchronize groups with relationships of type "hiorg_groups".
      static::processGroups($contactId, $configProfile->getOrganisationId(), $hiorgUser->gruppen_namen);
    }
  }

  public static function processGroups($contactId, $organisationId, $groups) {
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

    // End group memberships for groups not being submitted (anymore).
    foreach (array_diff($activeGroups, $groups) as $relationshipId => $groupToEnd) {
      Relationship::update(FALSE)
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

      Relationship::create(FALSE)
        ->addValue('relationship_type_id:name', 'hiorg_groups')
        ->addValue('contact_id_a', $contactId)
        ->addValue('contact_id_b', $organisationId)
        ->addValue('hiorg_relationship_groups.hiorg_group', array_search($groupToAdd, $existingGroups))
        ->execute();
    }
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
      'birth_date' => $user->gebdat ? \DateTime::createFromFormat('Y-m-d', $user->gebdat)
        ->format('Y-m-d') : NULL,
    ];
  }

}
