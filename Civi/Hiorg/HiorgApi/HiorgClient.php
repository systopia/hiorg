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

namespace Civi\Hiorg\HiorgApi;

use Civi\Api4\OAuthSysToken;
use Civi\Hiorg\ConfigProfiles\ConfigProfile;
use CRM_Hiorg_ExtensionUtil as E;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\RequestOptions;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;

class HiorgClient {

  public const BASE_URI_DEFAULT = 'https://api.hiorg-server.de/';

  public const BASE_PATH = 'core/v1/';

  protected string $oauthToken;

  protected GuzzleClient $guzzleClient;

  protected ResponseInterface $result;

  /**
   * @throws \Civi\API\Exception\UnauthorizedException
   * @throws \CRM_Core_Exception
   */
  public function __construct(ConfigProfile $configProfile) {
    if (!$tokenRecord = self::lookupToken($oauthClientId = $configProfile->getOauthClientId())) {
      throw new \Exception(E::ts('Error looking up OAuth token for OAuth client with ID %1', [1 => $oauthClientId]));
    }
    $this->oauthToken = $tokenRecord['access_token'];
    $this->guzzleClient = new GuzzleClient([
      'base_uri' => implode('/', [trim($configProfile->getApiBaseUri(), '/'), self::BASE_PATH]),
      'headers' => [
        'Authorization' => 'Bearer ' . $this->oauthToken,
        'Accept' => 'application/json',
      ],
    ]);
  }

  /**
   * Retrieves the first token for the OAuth Client set in the configuration
   * profile.
   *
   * @param int $oauthClientId
   *
   * @return array|null
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  protected function lookupToken(int $oauthClientId): ?array {
    try {
      return OAuthSysToken::refresh(FALSE)
        ->addWhere('client_id.id', '=', $oauthClientId)
        ->execute()
        ->first();
    }
    catch (IdentityProviderException $exception) {
      throw new \CRM_Core_Exception(
        $exception->getMessage() . ': ' . $exception->getResponseBody()['message'],
        $exception->getCode(),
        $exception->getResponseBody(),
        $exception
      );
    }
  }

  protected function formatRequestOptions($options = []): array {
    return [
      RequestOptions::QUERY => $options,
    ];
  }

  protected function formatResult(): object {
    return json_decode($this->result->getBody()->getContents());
  }

  /**
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function get($uri, $options = []) {
    $this->result = $this->guzzleClient->get(
      $uri,
      $this->formatRequestOptions($options)
    );
    return $this->formatResult();
  }

  /**
   * Retrieves redcords of type "Personal".
   *
   * @param bool $self
   *   Whether to retrieve the record representing the authorized user.
   * @param \DateTime|NULL $changedSince
   *   The date retrieved records have to have been changed since. Only applies
   *   when $self is FALSE.
   * @param array $include
   *   A list of linked objects to include in the response, e.g.
   *   - "organisation"
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getPersonal(bool $self = FALSE, \DateTime $changedSince = NULL, array $include = []) {
    if (!$self) {
      $body = [
        'filter' => [
          'updated_since' => isset($changedSince) ? $changedSince->format('Y-m-d\TH:i:sP') : NULL,
        ],
        'include' => implode(',', $include),
      ];
    }
    return $this->get(
      'personal' . ($self ? '/selbst' : ''),
        $body ?? []
    );
  }

  /**
   * Retrieves records of type "Ausbildungen".
   *
   * @param string $userId
   *   The HiOrg-Server user ID to retrieve records for.
   * @param \DateTime|NULL $changedSince
   *   The date user objects connected with retrieved records have to have been
   *   changed since.
   * @param array $include
   *   A list of linked objects to include in the response, e.g.
   *   - "organisation"
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getAusbildungen(string $userId, \DateTime $changedSince = NULL, array $include = []) {
    return $this->get(
      'personal/' . $userId . '/ausbildungen',
      [
        'filter' => [
          'updated_since' => isset($changedSince) ? $changedSince->format('Y-m-d\TH:i:sP') : NULL,
        ],
        'include' => implode(',', $include),
      ]
    );
  }

  /**
   * Retrieves records of type "Überprüfungen".
   *
   * @param string $userId
   *   The HiOrg-Server user ID to retrieve records for.
   * @param \DateTime|NULL $changedSince
   *   The date retrieved records have to have been changed since.
   * @param array $include
   *   A list of linked objects to include in the response, e.g.
   *   - "organisation"
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getUeberpruefungen(string $userId, \DateTime $changedSince = NULL, array $include = []) {
    return $this->get(
      'personal/' . $userId . '/ueberpruefungen',
      [
        'filter' => [
          'updated_since' => isset($changedSince) ? $changedSince->format('Y-m-d\TH:i:sP') : NULL,
        ],
        'include' => implode(',', $include),
      ]
    );
  }

  public function getOrganisation() {
    return $this->get('organisation/selbst/stammdaten');
  }

  /**
   * Retrieves records of type "Helferstunden".
   *
   * @param null $id
   *   The ID of a single record to retrieve.
   * @param bool $own
   *   Whether only own records are to be retrieved. Only applies when $id is
   *   not given.
   * @param \DateTime|NULL $from
   *   The earliest date to retrieve records for. Defaults to 6 months ago. Only
   *   applies when $id is not given.
   * @param \DateTime|NULL $to
   *   The latest date to retrieve records for. Only applies when $id is not
   *   given.
   * @param \DateTime|NULL $changedSince
   *    The date retrieved records have to have been changed since.
   * @param array $include
   *   A list of linked objects to include in the response, e.g.
   *   - "anlass"
   *   - "typ"
   *   - "user"
   *   - "anlass.referenz"
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getHelferstunden(
    $id = NULL,
    bool $own = TRUE,
    \DateTime $from = NULL,
    \DateTime $to = NULL,
    \DateTime $changedSince = NULL,
    array $include = []
  ) {
    // The API is documented to default to "-6 months" for the "from" date, so
    // this is not necessary to declare here.
    // $from ??= new \DateTime('-6 months');
    if (!$id) {
      $body = [
        'filter' => [
          'eigene' => $own ? 'true' : 'false',
          'von' => isset($from) ? $from->format('Y-m-d') : NULL,
          'bis' => isset($to) ? $to->format('Y-m-d') : NULL,
          'changed_since' => isset($changedSince) ? $changedSince->format('Y-m-d\TH:i:sP') : NULL,
        ],
        'include' => implode(',', $include),
      ];
    }
    return $this->get(
      'helferstunden' . ($id ? '/' . $id : ''),
      $body ?? []
    );
  }

}
