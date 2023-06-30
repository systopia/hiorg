<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;

/**
 * @method $this setConfigProfileId(int $configProfileId)
 */
abstract class AbstractHiorgAction extends \Civi\Api4\Generic\AbstractAction {

  /**
   * The configuration profile to use for the HiOrg-Server API call.
   *
   * @var int $configProfileId
   *
   * @required
   */
  protected ?int $configProfileId = NULL;

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
