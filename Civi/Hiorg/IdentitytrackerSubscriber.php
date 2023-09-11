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

use CRM_Hiorg_ExtensionUtil as E;
use Civi\API\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Civi\Core\Event\GenericHookEvent;
use CRM_Identitytracker_Configuration;

class IdentitytrackerSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      // Execute after mgd mixin, as we're altering an existing managed entity.
      'hook_civicrm_managed' => ['hook_civicrm_managed', -100],
    ];
  }

  public function hook_civicrm_managed(GenericHookEvent $event): void {
    $event->entities[] = [
      // TODO: Can we use the defining extension's name as "module"?
      'module' => E::LONG_NAME,
      'name' => 'CustomField__contact_id_history__id_history_context',
      'entity' => 'CustomField',
      'cleanup' => 'never',
      'update' => 'unmodified',
      'params' => [
        'version' => 4,
        'values' => [
          'custom_group_id.name' => CRM_Identitytracker_Configuration::GROUP_NAME,
          'name' => CRM_Identitytracker_Configuration::CONTEXT_FIELD_NAME,
          'is_active' => TRUE,
        ],
        'match' => [
          'custom_group_id',
          'name',
        ],
      ],
    ];
  }

}