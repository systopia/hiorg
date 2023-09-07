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

namespace Civi\Hiorg;

use Civi\Api4\EckEntity;
use Civi\Core\DAO\Event\PreDelete;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      'civi.dao.preDelete' => 'preDelete',
    ];
  }

  /**
   * @throws \API_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   * @throws \CRM_Core_Exception
   */
  public function preDelete(PreDelete $event): void {
    if ($event->object instanceof \CRM_Contact_DAO_Contact) {
      // Remove qualifications.
      EckEntity::delete('Hiorg_Qualification', FALSE)
        ->addWhere('Eck_Hiorg_Qualification.Contact', '=', $event->object->id)
        ->execute();
    }
  }

}
