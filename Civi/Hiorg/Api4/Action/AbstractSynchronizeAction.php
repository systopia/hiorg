<?php
/*-------------------------------------------------------+
| SYSTOPIA HiOrg-Server API                              |
| Copyright (C) 2024 SYSTOPIA                            |
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

use Civi\Api4\ConfigProfile;
use Civi\Api4\Generic\Result;

abstract class AbstractSynchronizeAction extends AbstractHiorgAction {

  /**
   * The ID of the configuration profile to use for the HiOrg-Server API call.
   *
   * Leave empty to synchronize data for all active configuration profiles.
   *
   * @var int|null
   */
  protected ?int $configProfileId = NULL;

  /**
   * The timeout in seconds after which the execution of queued synchronisation
   * tasks should be stopped.
   *
   * Leave empty for no timeout.
   *
   * @var int|null
   */
  protected ?int $timeout = NULL;

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $maxRunTime = $this->validateTimeout();
    if (NULL !== ($queue = $this->getQueue())) {
      $queueResult = $this->runQueue($queue, $maxRunTime);
      $result->exchangeArray($queueResult);
    }
  }

  abstract protected function getQueueName(): string;

  abstract protected static function getQueueTitle(): string;

  abstract protected function fillQueue(\CRM_Queue_Queue $queue): void;

  /**
   * @return \CRM_Queue_Queue|null
   *   The filled queue, or NULL if the queue is empty.
   */
  protected function getQueue(): ?\CRM_Queue_Queue {
    $queue = \Civi::queue(
      $this->getQueueName(),
      [
        'type' => 'Sql',
        'reset' => FALSE,
      ]
    );
    // Load queue.
    if (!$queue->existsQueue()) {
      $this->fillQueue($queue);
    }

    return $queue->getStatistic('total') > 0
      ? $queue
      : NULL;
  }

  protected function runQueue(\CRM_Queue_Queue $queue, ?int $maxRunTime = NULL): array {
    $totalItems = $queue->getStatistic('total');
    $runner = new \CRM_Queue_Runner([
      'title' => static::getQueueTitle(),
      'queue' => $queue,
      'errorMode' => \CRM_Queue_Runner::ERROR_CONTINUE,
    ]);

    // Run queue for given timeout.
    $continue = TRUE;
    $queueResult = [];
    while ($totalItems > 0 && (!isset($maxRunTime) || time() < $maxRunTime) && $continue) {
      $taskResult = $runner->runNext(FALSE);
      if (!$taskResult['is_continue']) {
        // All items in the queue are processed.
        $continue = FALSE;
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

  /**
   * Retrieves configuration profiles relevant for this action. This is either
   * only one profile as given through the API parameter, or all active HiOrg-
   * Server configuration profiles. The IDs of relevant profiles will be part of
   * the queue's name for identification.
   *
   * @return array
   *
   * @throws \CRM_Core_Exception
   */
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

}
