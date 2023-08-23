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

namespace Civi\Api4;

use Civi\Api4\Generic\BasicGetFieldsAction;
use Civi\Hiorg\Api4\Action\GetHelferstundenApiAction;
use Civi\Hiorg\Api4\Action\GetPersonalApiAction;
use Civi\Hiorg\Api4\Action\SynchronizeContactsAction;

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
    return (new GetPersonalApiAction(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  public static function getHelferstunden($checkPermissions = TRUE) {
    return (new GetHelferstundenApiAction(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  public static function synchronizeContacts($checkPermissions = TRUE) {
    return (new SynchronizeContactsAction(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

}
