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

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile\ConfigProfile;

/**
 * @method int getConfigProfileId
 */
abstract class AbstractHiorgAction extends AbstractAction {

  /**
   * The ID of the configuration profile to use for the HiOrg-Server API call.
   *
   * @var int|null $configProfileId
   *
   * @required
   */
  protected ?int $configProfileId = NULL;

  /**
   * The configuration profile to use for the HiOrg-Server API call.
   *
   * @var ConfigProfile|null
   */
  protected ?ConfigProfile $_configProfile = NULL;

  /**
   * @return static
   * @throws \Exception
   */
  public function setConfigProfileId($configProfileId) {
    // parent::setConfigProfileId($configProfileId); is magic via
    // parent::__call() and can't be documented.
    parent::__call(__FUNCTION__, func_get_args());
    // Load the profile.
    $this->_configProfile = ConfigProfile::getById($this->configProfileId);
    return $this;
  }
  public function getConfigProfile() {
    return $this->_configProfile;
  }

  /**
   * @inheritDoc
   */
  abstract public function _run(Result $result);

  /**
   * Defines parameters for this API action.
   *
   * @return array[]
   */
  public static function fields(): array {
    return [
      [
        'name' => 'configProfile',
        'data_type' => 'Integer',
        'pseudoconstant' => [
          'callback' => ConfigProfile::class . '::loadAll',
        ],
      ],
    ];
  }

}
