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

use CRM_Hiorg_ExtensionUtil as E;
use Civi\Hiorg\Queue\Task\SynchronizeContactsTask;
use Civi\Api4\Generic\Result;
use Civi\Api4\Hiorg;
use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;

class SynchronizeContactsAction extends AbstractHiorgAction {

  /**
   * The timeout in seconds after which the execution of queued synchronisation
   * tasks should be stopped.
   *
   * @var int|null $timeout
   */
  protected ?int $timeout = NULL;

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $phpMaxExecutionTime = ini_get('max_execution_time');
    if ($phpMaxExecutionTime > 0 && $this->timeout > $phpMaxExecutionTime) {
      throw new \Exception('The timeout exceeds the max_execution_time PHP setting value.');
    }
    $maxRunTime = isset($this->timeout) ? time() + $this->timeout : NULL;

    $oAuthClientId = $this->getConfigProfile()->getOauthClientId();
    $lastSync = \Civi::settings()->get('hiorg.synchronizeContacts.lastSync') ?? [];
    $currentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');

    // Load queue.
    $queue = \Civi::queue('hiorg-synchronize-contacts-' . $oAuthClientId, [
      'type'  => 'Sql',
      'reset' => FALSE,
    ]);
    if (!$queue->existsQueue()) {
      // Retrieve HiOrg user data via HiOrg-Server API.
      $personalResult = Hiorg::getPersonal()
        ->setConfigProfileId($this->getConfigProfileId())
        ->setChangedSince($lastSync[$oAuthClientId] ?? NULL)
        ->execute();
      // TODO: Log/Report errors.

      // Add queue items for each record.
      foreach ($personalResult as $record) {
        $hiorgUserResult = [];
        $hiorgUser = HiorgUserDTO::create($record);
        $queue->createItem(new SynchronizeContactsTask(
          $this->getConfigProfile(),
          $hiorgUser
        ));
      }

      // Store synchronization time.
      $lastSync[$oAuthClientId] = $currentSync;
      \Civi::settings()->set('hiorg.synchronizeContacts.lastSync', $lastSync);
    }

    $runner = new \CRM_Queue_Runner([
      'title' => E::ts('HiOrg-Server: Synchronize Contacts'),
      'queue' => $queue,
      'errorMode' => \CRM_Queue_Runner::ERROR_CONTINUE,
    ]);

    // Run queue for given timeout.
    $continue = TRUE;
    while((!isset($maxRunTime) || time() < $maxRunTime) && $continue) {
      $taskResult = $runner->runNext(false);
      if (!$taskResult['is_continue']) {
        // All items in the queue are processed.
        $continue = false;
      }
      $queueResult[] = &$taskResult;
      // If there is a lock on the next item, do not attempt to re-run it.
      // Otherwise the loop will run until the end of the timeout without doing
      // anything. This can only be recognized by evaluating the exception
      // message, if any.
      // TODO: This should be handled differently, see
      //       https://lab.civicrm.org/dev/core/-/issues/4622
      if (
        !empty($taskResult['is_error'])
        && isset($taskResult['exception'])
        && $taskResult['exception']->getMessage() == 'Failed to claim next task'
      ) {
        break;
      }

      if (is_a($taskResult['exception'], \Exception::class)) {
        $taskResult['error_message'] = $taskResult['exception']->getMessage();
      }
    }

    $result->exchangeArray($queueResult);
  }

}
