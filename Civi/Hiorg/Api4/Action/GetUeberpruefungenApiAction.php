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

/**
 * @method $this setUserId(string $userId)
 * @method $this setChangedSince(string $changedSince)
 */
class GetUeberpruefungenApiAction extends AbstractHiorgApiAction {

  /**
   * @var string|null
   *
   * @required
   */
  protected ?string $userId = NULL;

  /**
   * @var string|null
   */
  protected ?string $changedSince = NULL;

  /**
   * @inheritDoc
   */
  protected function doRun(): void {
    $this->_response = $this->_hiorgClient->getUeberpruefungen(
      $this->userId,
      $this->changedSince ? \DateTime::createFromFormat('Y-m-d\TH:i:sP', $this->changedSince) : NULL
    );
  }

  /**
   * {@inheritDoc}
   */
  public static function fields(): array {
    return parent::fields() + [
      [
        'name' => 'userId',
        'data_type' => 'String',
      ],
      [
        'name' => 'changedSince',
        'data_type' => 'String',
      ],
    ];
  }

}
