<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\Result;
use Civi\Hiorg\HiorgClient;

class GetHelferstundenAction extends AbstractHiorgAction {

  /**
   * @inheritDoc
   */
  public function _run(Result $result) {
    $client = $this->getHiorgClient();
  }

}
