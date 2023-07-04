<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Api4\Hiorg;
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
      $user = new HiorgUserDTO($record);

      // Synchronize contact data using Extended Contact Manager (XCM) with
      // profile defined in HiOrg-Server API configuration profile.
      $xcmResult = civicrm_api3(
        'Contact',
        'createifnotexists',
        ['xcm_profile' => $xcmProfile] + self::mapParameters($user)
      );
      if (empty($id = $xcmResult['id'])) {
        throw new Exception(E::ts('Error retrieving/creating contact with Extended Contact Manager.'));
      }

      // TODO: Synchronize "qualifikationen": custom entity "qualifikation instance" referencing the contact and a "qualifikation" custom entity.

      // TODO: Synchronize "ausbildungen": custom entity "ausbildunge instance" referencing the contact and a "ausbildung" custom entity.

      /**
       * TODO: ASB-spezifisch:
       *       - Beziehung "tätig bei RV" anlegen, wenn nicht vorhanden
       */
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
      // TODO: "gruppen_namen" in multi-value custom field with option values (overwrite).
    ];
  }

}
