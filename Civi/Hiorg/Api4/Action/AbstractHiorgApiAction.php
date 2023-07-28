<?php

namespace Civi\Hiorg\Api4\Action;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Hiorg\ConfigProfile;
use Civi\Hiorg\HiorgClient;

abstract class AbstractHiorgApiAction extends AbstractHiorgAction {

  /**
   * The JSON-decoded HiOrg-Server API result.
   *
   * @var object $_response
   */
  protected object $_response;

  /**
   * The HiOrg-Server API client.
   *
   * @var \Civi\Hiorg\HiorgClient $_hiorgClient
   */
  protected HiorgClient $_hiorgClient;

  /**
   * {@inheritDoc}
   */
  public function __construct($entityName, $actionName) {
    parent::__construct($entityName, $actionName);
  }

  /**
   * @inheritDoc
   */
  public function _run(Result $result): void {
    $this->_hiorgClient = new HiorgClient(ConfigProfile::getById($this->configProfileId));
    $this->doRun();
    $this->formatResult($result);
  }

  abstract protected function doRun(): void;

  /**
   * Formats the JSON-decoded HiOrg-Server API response as a CiviCRM API result.
   *
   * @param \Civi\Api4\Generic\Result $result
   *
   * @return void
   * @throws \CRM_Core_Exception
   */
  protected function formatResult(Result $result) {
    if (isset($this->_response->errors)) {
      throw new \CRM_Core_Exception(
        $this->_response->errors[0]->title . '(' . $this->_response->errors[0]->detail . ')',
        $this->_response->errors[0]->code,
        $this->_response->errors
      );
    }
    elseif (isset($this->_response->data)) {
      // Wrap single result in array for CiviCRM API to count correctly.
      $result->exchangeArray(is_object($this->_response->data) ? [$this->_response->data] : $this->_response->data);
    }
  }

}
