<?php

namespace Civi\Hiorg;

use \Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Civi\Core\Event\GenericHookEvent;

class ConfigProfilesSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      'civi.config_profiles.types' => 'configProfileTypes',
    ];
  }

  public static function configProfileTypes(GenericHookEvent $event) {
    $event->types['hiorg'] = ConfigProfile::class;
  }

}
