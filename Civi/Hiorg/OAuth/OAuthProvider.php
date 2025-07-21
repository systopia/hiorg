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

declare(strict_types = 1);

namespace Civi\Hiorg\OAuth;

use Civi\OAuth\CiviGenericProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;

class OAuthProvider extends CiviGenericProvider {

  public function __construct(array $options = [], array $collaborators = []) {
    // Set redirect URI to our own.
    // TODO: Consider using the default redirect URI of CiviCRM's OAuth clients.
    $options['redirectUri'] = \CRM_Utils_System::languageNegotiationURL(
      \CRM_Utils_System::url(
        'civicrm/hiorg/auth',
        '',
        TRUE
      ),
      FALSE,
      TRUE
    );
    parent::__construct($options, $collaborators);
  }

  protected function createResourceOwner(array $response, AccessToken $token): GenericResourceOwner {
    return new GenericResourceOwner($response, 'sub');
  }

}
