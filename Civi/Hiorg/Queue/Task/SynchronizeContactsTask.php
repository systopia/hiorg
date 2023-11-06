<?php

namespace Civi\Hiorg\Queue\Task;

use Civi\Hiorg\Api\DTO\HiorgUserDTO;
use Civi\Hiorg\ConfigProfile\ConfigProfile;
use Civi\Hiorg\Synchronize\Synchronize;
use CRM_Hiorg_ExtensionUtil as E;

class SynchronizeContactsTask extends \CRM_Queue_Task {

  protected ConfigProfile $configProfile;

  public function __construct(ConfigProfile $configProfile, array $arguments = [], ?string $title = NULL) {
    parent::__construct([$this, 'doRun'], $arguments, $title);
    $this->configProfile = $configProfile;
  }

  protected function doRun(\CRM_Queue_TaskContext $context, HiorgUserDTO $hiorgUser) {
    try {
      $hiorgUserResult = Synchronize::synchronizeContacts($this->configProfile, $hiorgUser);
      $result = \CRM_Queue_Task::TASK_SUCCESS;
      $message = E::ts('Synchronized HiOrg-Server user with ID %1', [1 => $hiorgUser->id]);
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
