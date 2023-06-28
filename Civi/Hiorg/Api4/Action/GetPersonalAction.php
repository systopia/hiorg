<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Hiorg\HiorgClient;

class GetPersonalAction extends AbstractHiorgAction {

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $client = $this->getHiorgClient();
  }

}
