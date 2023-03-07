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

use Civi\Core\Event\GenericHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OAuthProviderSubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      'hook_civicrm_oauthProviders' => 'oauthProviders',
    ];
  }

  public static function oauthProviders(GenericHookEvent $event) {
    $event->providers['hiorg'] = [
      'name' => 'hiorg',
      'title' => 'HiOrg-Server',
      'class' => OAuthProvider::class,
      'options' => [
        'urlAuthorize' => 'https://api.hiorg-server.de/oauth/v1/authorize',
        'urlAccessToken' => 'https://api.hiorg-server.de/oauth/v1/token',
        // TODO: ResourceOwnerDetails URL correct?
        'urlResourceOwnerDetails' => 'https://api.hiorg-server.de/oauth/v1/token',
        'scopes' => [
          'openid personal/selbst:read organisation/selbst/stammdaten:read helferstunden:read',
        ],
      ]
    ];
  }

}