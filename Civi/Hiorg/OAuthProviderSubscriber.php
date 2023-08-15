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
        'urlResourceOwnerDetails' => '{{use_id_token}}',
        'scopes' => [
          'openid personal/selbst:read personal:read organisation/selbst/stammdaten:read helferstunden:read',
        ],
      ]
    ];

    // TODO: Consider making custom OAuth providers configurable, as
    //       HiOrg-Server instances may run on any domain which implies defining
    //       OAuth providers would need an extension implementing the
    //       "hook_civicrm_oauthProviders" event.
  }

}
