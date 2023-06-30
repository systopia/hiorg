<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;
use Civi\Hiorg\HiorgClient;

/**
 * @method $this setSelf(bool $self)
 * @method $this setChangedSince(string $changedSince)
 */
class GetPersonalApiAction extends AbstractHiorgApiAction {

  /**
   * @var bool|null
   */
  protected ?bool $self = FALSE;

  /**
   * @var string|null
   */
  protected ?string $changedSince = NULL;

  /**
   * @inheritDoc
   */
  protected function doRun(): void {
    $this->_response = $this->_hiorgClient->getPersonal(
      $this->self,
      $this->changedSince ? \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->changedSince) : NULL
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
