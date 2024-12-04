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

namespace Civi\Hiorg\Api4\Action;

/**
 * @method $this setId(int $id)
 * @method $this setOwn(bool $own)
 * @method $this setFrom(string $from)
 * @method $this setTo(string $to)
 * @method $this setChangedSince(string $changedSince)
 */
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
   * @var string|null
   *
   * Format: Y-m-d\TH:i:sP
   */
  protected ?string $changedSince = NULL;

  /**
   * {@inheritDoc}
   */
  public static function fields(): array {
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
        [
          'name' => 'changedSince',
          'data_type' => 'String',
        ],
    ];
  }

  /**
   * @inheritDoc
   */
  public function doRun(): void {
    $this->_response = $this->_hiorgClient->getHelferstunden(
      $this->id,
      $this->own,
      isset($this->from) ? \DateTime::createFromFormat('Y-m-d', $this->from) : NULL,
      isset($this->to) ? \DateTime::createFromFormat('Y-m-d', $this->to) : NULL,
      $this->changedSince ? \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->changedSince) : NULL,
      ['anlass', 'typ']
    );
  }

}
