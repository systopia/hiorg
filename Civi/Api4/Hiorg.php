<?php

namespace Civi\Api4;

use Civi\Api4\Generic\BasicGetFieldsAction;
use Civi\Hiorg\Api4\Action\GetHelferstundenAction;
use Civi\Hiorg\Api4\Action\GetPersonalAction;

class Hiorg extends Generic\AbstractEntity {

  /**
   * @inheritDoc
   */
  public static function getFields($checkPermissions = TRUE) {
    return (new BasicGetFieldsAction(
      __CLASS__,
      __FUNCTION__,
      function($getFieldsAction) {
        return [];
      }
    ))
      ->setCheckPermissions($checkPermissions)
      ->setLoadOptions(TRUE);
  }

  public static function getPersonal($checkPermissions = TRUE) {
    return (new GetPersonalAction(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  public static function getHelferstunden($checkPermissions = TRUE) {
    return (new GetHelferstundenAction(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

}
