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

use Civi\Api4\Generic\Result;

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

  /**
   * {@inheritDoc}
   */
  protected function formatResult(Result $result): void {
    parent::formatResult($result);
    $filteredResult = array_filter($result->getArrayCopy(), function($record) {
      return !in_array(
        $record->attributes->status,
        $this->getConfigProfile()->getExcludeHiOrgUserStatus()
      );
    });
    $result->exchangeArray($filteredResult);
  }

  /**
   * {@inheritDoc}
   */
  public static function fields(): array {
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
