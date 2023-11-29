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
use Civi\Api4\ConfigProfile;
use Civi\Hiorg\ConfigProfiles\ConfigProfile as HiorgConfigProfile;
use Civi\Hiorg\Queue\Task\SynchronizeContactsTask;
use Civi\Api4\Generic\Result;
use Civi\Api4\Hiorg;
use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;

class SynchronizeContactsAction extends AbstractHiorgAction {

  /**
   * The ID of the configuration profile to use for the HiOrg-Server API call.
   *
   * Leave empty to synchronize data for all active configuration profiles.
   *
   * @var int|null $configProfileId
   */
  protected ?int $configProfileId = NULL;

  /**
   * The timeout in seconds after which the execution of queued synchronisation
   * tasks should be stopped.
   *
   * Leave empty for no timeout.
   *
   * @var int|null $timeout
   */
  protected ?int $timeout = NULL;

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $maxRunTime = $this->validateTimeout();
    $configProfiles = $this->getConfigProfiles();

    $queue = $this->getQueue($configProfiles);
    $queueResult = self::runQueue($queue, $maxRunTime);

    $result->exchangeArray($queueResult);
  }

  private function validateTimeout(): ?int {
    $phpMaxExecutionTime = ini_get('max_execution_time');
    if (
      $phpMaxExecutionTime > 0
      && (
        !isset($this->timeout)
        || $this->timeout > $phpMaxExecutionTime
      )
    ) {
      throw new \Exception('The timeout exceeds the max_execution_time PHP setting value.');
    }

    return isset($this->timeout) ? time() + $this->timeout : NULL;
  }

  protected function getConfigProfiles() {
    $configProfilesQuery = ConfigProfile::get('hiorg', FALSE)
      ->addSelect('id')
      ->addWhere('is_active', '=', TRUE);
    if (isset($this->configProfileId)) {
      $configProfilesQuery
        ->addWhere('id', '=', $this->configProfileId);
    }
    return $configProfilesQuery
      ->addOrderBy('id')
      ->execute()
      ->indexBy('id')
      ->getArrayCopy();
  }

  private function getQueue(array $configProfiles) {
    $queue = \Civi::queue(
      'hiorg-synchronize-contacts-' . ($this->getConfigProfileId() ?? implode('-', array_keys($configProfiles))),
      [
        'type' => 'Sql',
        'reset' => FALSE,
      ]
    );
    // Load queue.
    if (!$queue->existsQueue()) {
      self::fillQueue($queue, $configProfiles);
    }
    return $queue;
  }

  private static function fillQueue(\CRM_Queue_Queue $queue, array $configProfiles) {
    foreach ($configProfiles as $configProfile) {
      $hiorgConfigProfile = HiorgConfigProfile::getById($configProfile['id']);
      $oAuthClientId = $hiorgConfigProfile->getOauthClientId();
      $lastSync = \Civi::settings()->get('hiorg.synchronizeContacts.lastSync') ?? [];
      $currentSync = (new \DateTime())->format('Y-m-d\TH:i:sP');

      // Retrieve HiOrg user data via HiOrg-Server API.
      $personalResult = Hiorg::getPersonal()
        ->setConfigProfileId($hiorgConfigProfile->id)
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

  private static function runQueue(\CRM_Queue_Queue $queue, ?int $maxRunTime = NULL): array {
    $runner = new \CRM_Queue_Runner([
      'title' => E::ts('HiOrg-Server: Synchronize Contacts'),
      'queue' => $queue,
      'errorMode' => \CRM_Queue_Runner::ERROR_CONTINUE,
    ]);

    // Run queue for given timeout.
    $continue = TRUE;
    $queueResult = [];
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

    return $queueResult;
  }

}
