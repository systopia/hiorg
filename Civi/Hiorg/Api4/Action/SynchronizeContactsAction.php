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
    //TODO: Retrieve $changedSince from synchronisation log (to be implemented)
    $changedSince = (new \DateTime())->format('Y-m-d\TH:i:sP');

    // TODO: Retrieve contacts via getPersonal API action.
    $personalResult = Hiorg::getPersonal()
      ->setConfigProfileId($this->configProfileId)
      ->setChangedSince($changedSince)
      // TODO: Remove when accessing /personal without /self is allowed, this is for testing.
      ->setSelf(TRUE)
      ->execute();

    $configProfile = ConfigProfile::getById($this->configProfileId);
    $xcmProfile = $configProfile->getXcmProfileName();
    foreach ($personalResult as $user) {
      $userDto = new HiorgUserDTO($user);
      // TODO: Synchronize data using XCM (with profile defined in configuration profile)
      civicrm_api3('Contact', 'createifnotexists', [
        'first_name' => $userDto->vorname,
        'last_name' => $userDto->nachname,
      ]);
    }
  }

}
