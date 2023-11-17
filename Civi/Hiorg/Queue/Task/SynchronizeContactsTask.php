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

namespace Civi\Hiorg\Queue\Task;

use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;
use Civi\Hiorg\ConfigProfiles\ConfigProfile;
use Civi\Hiorg\Synchronize\Synchronize;
use CRM_Hiorg_ExtensionUtil as E;

class SynchronizeContactsTask extends \CRM_Queue_Task {

  public function __construct(ConfigProfile $configProfile, HiorgUserDTO $hiorgUser) {
    parent::__construct(
      [$this, 'doRun'],
      [
        'configProfile' => $configProfile,
        'hiorgUser' => $hiorgUser,
      ],
      E::ts('Synchronizing HiOrg-Server user (ID: %1)', [1 => $hiorgUser->id])
    );
  }

  protected function doRun(\CRM_Queue_TaskContext $context, ConfigProfile $configProfile, HiorgUserDTO $hiorgUser) {
    try {
      $hiorgUserResult = Synchronize::synchronizeContacts($configProfile, $hiorgUser);
      $result = \CRM_Queue_Task::TASK_SUCCESS;
      $message = E::ts(
        'Synchronized HiOrg-Server user (ID: %1) with CiviCRM contact (ID: %2).',
        [
          1 => $hiorgUser->id,
          2 => $hiorgUserResult['contact_id'],
        ]
      );
    }
    catch (\Exception $exception) {
      $result = \CRM_Queue_Task::TASK_FAIL;
      $message = E::ts(
        'Failed synchronizing HiOrg-Server user with ID %1. Error: %2',
        [
          1 => $hiorgUser->id,
          2 => $exception->getMessage(),
        ]
      );
    }

    $context->log->info($message);
    return $result;
  }

}
