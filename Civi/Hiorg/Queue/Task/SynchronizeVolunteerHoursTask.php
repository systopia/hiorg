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
use Civi\Hiorg\HiorgApi\DTO\HiorgVolunteerHoursDTO;
use Civi\Hiorg\Synchronize\Synchronize;
use CRM_Hiorg_ExtensionUtil as E;

class SynchronizeVolunteerHoursTask extends \CRM_Queue_Task {

  public function __construct(ConfigProfile $configProfile, HiorgVolunteerHoursDTO $hiorgVolunteerHours) {
    parent::__construct(
      [$this, 'doRun'],
      [
        'configProfile' => $configProfile,
        'hiorgVolunteerHours' => $hiorgVolunteerHours,
      ],
      E::ts('Synchronizing HiOrg-Server volunteer hours (ID: %1)', [1 => $hiorgVolunteerHours->id])
    );
  }

  protected function doRun(\CRM_Queue_TaskContext $context, ConfigProfile $configProfile, HiorgVolunteerHoursDTO $hiorgVolunteerHours) {
    try {
      $volunteerHoursResult = Synchronize::synchronizeVolunteerHours($configProfile, $hiorgVolunteerHours);
      $result = \CRM_Queue_Task::TASK_SUCCESS;
      $message = E::ts(
        'Synchronized HiOrg-Server volunteer hours (ID: %1) with CiviCRM activity (ID: %2).',
        [
          1 => $hiorgVolunteerHours->id,
          2 => $volunteerHoursResult['activity']['id'],
        ]
      );
      $context->log->info($message);
    }
    catch (\Exception $exception) {
      $result = \CRM_Queue_Task::TASK_FAIL;
      $message = E::ts(
        'Failed synchronizing HiOrg-Server volunteer hours with ID %1. Error: %2',
        [
          1 => $hiorgVolunteerHours->id,
          2 => $exception->getMessage(),
        ]
      );
      $context->log->err($message);
    }
    return $result;
  }

}
