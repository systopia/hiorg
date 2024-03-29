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

namespace Civi\Hiorg\EventSubscriber\ConfigProfiles;

use Civi\Core\Event\GenericHookEvent;
use Civi\Hiorg\ConfigProfiles\ConfigProfile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigProfilesSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      'civi.config_profiles.types' => 'configProfileTypes',
    ];
  }

  public static function configProfileTypes(GenericHookEvent $event): void {
    $event->types['hiorg'] = ConfigProfile::class;
  }

}
