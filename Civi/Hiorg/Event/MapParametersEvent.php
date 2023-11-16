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

namespace Civi\Hiorg\Event;

use Civi\Hiorg\Api\DTO\HiorgUserDTO;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event for altering synchronization mappings of HiOrg-Server user fields to
 * CiviCRM contact attributes.
 */
class MapParametersEvent extends Event {

  public const NAME = 'civi.hiorg.mapParameters';

  /**
   * @var array
   *   An array with CiviCRM contact attributes as keys and actual attribute
   *   values as element values.
   */
  protected array $mappings = [];

  /**
   * @var \Civi\Hiorg\Api\DTO\HiorgUserDTO
   *   The HiOrg-Server user DTO object containing all properties fetched via
   *   the HiOrg-Server API.
   */
  protected HiorgUserDTO $user;

  public function __construct(HiorgUserDTO $user, array $mappings = []) {
    $this->user = $user;
    $this->mappings = $mappings;
  }

  /**
   * @return array|\Civi\Hiorg\Api\DTO\HiorgUserDTO
   *   The HiOrg-Sever user DTO object.
   */
  public function getUser(): array {
    return $this->user;
  }

  /**
   * @return array
   *   The current set of mappings.
   */
  public function getMappings(): array {
    return $this->mappings;
  }

  /**
   * @param array $mappings
   *
   * @return void
   */
  public function setMappings(array $mappings = []): void {
    $this->mappings = $mappings;
  }

  /**
   * Adds an array of mappings to the current set of mappings using array
   * addition.
   *
   * @param array $mappings
   *
   * @return array
   *   Mapping keys that did not exist before adding.
   */
  public function addMappings(array $mappings):array {
    $added = array_diff_key($mappings, $this->mappings);
    $this->mappings += $mappings;
    return $added;
  }

  /**
   * Adds a single mapping to the current set of mappings using array addition.
   *
   * @param string $field
   * @param mixed|NULL $value
   *
   * @return bool
   *   Whether the mapping has been added, i.e. did not exist before.
   */
  public function addMapping(string $field, mixed $value = NULL): bool {
    $added = !isset($this->mappings[$field]);
    $this->mappings += [
      $field => $value,
    ];
    return $added;
  }

  /**
   * Explicitly sets a single mapping in the current set of mappings.
   *
   * @param string $field
   * @param mixed|NULL $value
   *
   * @return bool
   *   Whether the mapping has been added, i.e. did not exist before.
   */
  public function setMapping(string $field, mixed $value = NULL): bool {
    $added = !isset($this->mappings[$field]);
    $this->mappings[$field] = $value;
    return $added;
  }

  /**
   * Unsets a single mapping in the current set of mappings.
   *
   * @param $field
   *
   * @return bool
   *   Whether the mapping has been deleted, i.e. has existed before.
   */
  public function unsetMapping($field): bool {
    if ($exists = isset($this->mappings[$field])) {
      unset($this->mappings[$field]);
    }
    return $exists;
  }

}
