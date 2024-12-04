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

namespace Civi\Hiorg\Event;

use Civi\Hiorg\ConfigProfiles\ConfigProfile;
use Civi\Hiorg\HiorgApi\DTO\HiorgUserDTO;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event for synchronizing HiOrg-Server API user data with CiviCRM contacts.
 */
class SynchronizeContactsEvent extends Event {

  public const NAME = 'civi.hiorg.synchronizeContacts';

  protected HiorgUserDTO $hiorgUser;

  protected ConfigProfile $configProfile;

  protected int $contactId;

  protected array $results;

  public function __construct(HiorgUserDTO $hiorgUser, ConfigProfile $configProfile, int $contactId, array $results) {
    $this->hiorgUser = $hiorgUser;
    $this->configProfile = $configProfile;
    $this->contactId = $contactId;
    $this->results = $results;
  }

  public function getUser(): HiorgUserDTO {
    return $this->hiorgUser;
  }

  public function getConfigProfile(): ConfigProfile {
    return $this->configProfile;
  }

  public function getContactId(): int {
    return $this->contactId;
  }

  public function getResults(): array {
    return $this->results;
  }

  /**
   * @param $key
   *
   * @return mixed|null
   */
  public function getResult($key) {
    return $this->results[$key] ?? NULL;
  }

  /**
   * Adds a single result to the current set of results using array addition.
   *
   * @param string $key
   * @param mixed $result
   *
   * @return bool
   *   Whether the result has been added, i.e. did not exist before.
   */
  public function addResult(string $key, $result): bool {
    $added = !isset($this->results[$key]);
    $this->results += [$key => $result];
    return $added;
  }

}
