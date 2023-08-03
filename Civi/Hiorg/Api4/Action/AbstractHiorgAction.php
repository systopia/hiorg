<?php

namespace Civi\Hiorg\Api4\Action;

use \Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;

abstract class AbstractHiorgAction extends AbstractAction {

  /**
   * The ID of the configuration profile to use for the HiOrg-Server API call.
   *
   * @var int $configProfileId
   *
   * @required
   */
  protected ?int $configProfileId = NULL;

  /**
   * The configuration profile to use for the HiOrg-Server API call.
   *
   * @var \Civi\Hiorg\ConfigProfile
   */
  protected ?ConfigProfile $_configProfile = NULL;

  public function setConfigProfileId($configProfileId) {
    parent::setConfigProfileId($configProfileId);
    // Load the profile.
    $this->_configProfile = ConfigProfile::getById($this->configProfileId);
    return $this;
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
  public static function fields() {
    return [
      [
        'name' => 'configProfile',
        'data_type' => 'Integer',
        'pseudoconstant' => [
          'callback' => '\Civi\Hiorg\ConfigProfile::loadAll',
        ],
      ],
    ];
  }

}
