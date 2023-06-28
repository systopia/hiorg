<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;
use Civi\Hiorg\HiorgClient;

class AbstractHiorgAction extends AbstractAction {

  /**
   * @var int $configProfileId
   *
   * The configuration profile to use for the HiOrg-Server API call.
   *
   * @required
   */
  protected int $configProfileId;

  /**
   * @inheritDoc
   */
  abstract public function _run(Result $result): void;

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

  public function getHiorgClient(): HiorgClient {
    $configProfile = ConfigProfile::getById($this->configProfileId);
    return new HiorgClient($configProfile);
  }

}
