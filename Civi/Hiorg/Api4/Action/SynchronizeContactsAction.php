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

declare(strict_types = 1);

namespace Civi\Hiorg\Api4\Action;

use CRM_Hiorg_ExtensionUtil as E;
use Civi\Hiorg\ConfigProfiles\ConfigProfile as HiorgConfigProfile;
use Civi\Hiorg\Queue\Task\SynchronizeContactsTask;
use Civi\Api4\Hiorg;
use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;

class SynchronizeContactsAction extends AbstractSynchronizeAction {

  protected function getQueueName(): string {
    return 'hiorg-synchronize-contacts-'
      . (
        $this->getConfigProfileId()
        ?? implode('-', array_keys($this->getConfigProfiles()))
      );
  }

  protected static function getQueueTitle(): string {
    return E::ts('HiOrg-Server: Synchronize Contacts');
  }

  protected function fillQueue(\CRM_Queue_Queue $queue): void {
    foreach ($this->getConfigProfiles() as $configProfile) {
      $hiorgConfigProfile = HiorgConfigProfile::getById($configProfile['id']);
      $oAuthClientId = $hiorgConfigProfile->getOauthClientId();
      $lastSync = \Civi::settings()->get('hiorg.synchronizeContacts.lastSync') ?? [];
      $currentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');

      // Retrieve HiOrg user data via HiOrg-Server API.
      $personalResult = Hiorg::getPersonal(FALSE)
        ->setConfigProfileId($hiorgConfigProfile->getId())
        ->setChangedSince($lastSync[$oAuthClientId] ?? NULL)
        ->execute();
      // TODO: Log/Report errors.

      // Add queue items for each record.
      foreach ($personalResult as $record) {
        $hiorgUser = HiorgUserDTO::create($record);
        $queue->createItem(new SynchronizeContactsTask(
          $hiorgConfigProfile,
          $hiorgUser
        ));
      }

      // Store synchronization time.
      $lastSync[$oAuthClientId] = $currentSync;
      \Civi::settings()->set('hiorg.synchronizeContacts.lastSync', $lastSync);
    }
  }

}
