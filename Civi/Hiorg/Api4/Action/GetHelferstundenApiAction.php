<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;
use Civi\Hiorg\HiorgClient;

class GetHelferstundenApiAction extends AbstractHiorgApiAction {

  /**
   * @var int|null
   */
  protected ?int $id = NULL;

  /**
   * @var bool|null
   */
  protected ?bool $own = TRUE;

  /**
   * @var string|null
   */
  protected ?string $from = NULL;

  /**
   * @var string|null
   */
  protected ?string $to = NULL;

  /**
   * @inheritDoc
   */
  public function doRun(): void {
    $this->_response = $this->_hiorgClient->getHelferstunden(
      $this->id,
      $this->own,
      \DateTime::createFromFormat('Y-m-d', $this->from),
      \DateTime::createFromFormat('Y-m-d', $this->to)
    );
  }
  public static function fields() {
    return parent::fields() + [
        [
          'name' => 'id',
          'data_type' => 'Integer',
        ],
        [
          'name' => 'own',
          'data_type' => 'Boolean',
          'default_value' => TRUE,
        ],
        [
          'name' => 'from',
          'data_type' => 'String',
          'default_value' => (new \DateTime('-6 months'))->format('Y-m-d'),
        ],
        [
          'name' => 'to',
          'data_type' => 'String',
        ],
      ];
  }

}
