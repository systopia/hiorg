<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;
use Civi\Hiorg\HiorgClient;

class GetPersonalAction extends AbstractHiorgAction {

  protected ?bool $self = FALSE;

  protected ?\DateTime $changedSince = NULL;

  /**
   * @inheritDoc
   */
  protected function doRun(): void {
    $this->_response = $this->_hiorgClient->getPersonal(
      $this->self,
      $this->changedSince
    );
  }

  public static function fields() {
    return parent::fields() + [
      [
        'name' => 'self',
        'data_type' => 'Boolean',
        'default_value' => FALSE,
      ],
      [
        'name' => 'changedSince',
        'data_type' => 'String',
      ],
    ];
  }

}
