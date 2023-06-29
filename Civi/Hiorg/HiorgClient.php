<?php

namespace Civi\Hiorg;

use Civi\Api4\OAuthSysToken;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class HiorgClient {

  const BASE_URI = 'https://api.hiorg-server.de/core/v1/';

  protected $oauthToken;

  protected Client $guzzleClient;

  protected ResponseInterface $result;

  public function __construct(ConfigProfile $configProfile) {
    if (!$tokenRecord = self::lookupToken($oauthClientId = $configProfile->getOauthClientId())) {
      throw new \Exception(E::ts('Error looking up OAuth token for OAuth client with ID %1', [1 => $oauthClientId]));
    }
    $this->oauthToken = $tokenRecord['access_token'];
    $this->guzzleClient = new \GuzzleHttp\Client([
      'base_uri' => self::BASE_URI,
      'headers' => [
        'Authorization' => 'Bearer ' . $tokenRecord['access_token'],
        'Accept' => 'application/json',
      ],
    ]);
  }

  /**
   * Retrieves the first token for the OAuth Client set in the configuration
   * profile.
   *
   * @param \Civi\Hiorg\ConfigProfile $configProfile
   *
   * @return void
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  protected function lookupToken($oauthClientId) {
    return OAuthSysToken::refresh(FALSE)
      ->addWhere('client_id.guid', '=', $oauthClientId)
      ->execute()
      ->first();
  }

  protected function formatRequestOptions($options = []) {
    return [
      RequestOptions::JSON => $options,
    ];
  }

  protected function formatResult(): object {
    return json_decode($this->result->getBody());
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
   *   A list of linked objects to include in the response, e. g.
   *   - "organisation"
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getPersonal(bool $self = FALSE, \DateTime $changedSince = NULL, array $include = []) {
    if (!$self) {
      $body = [
        'filter' => [
          'changed_since' => $changedSince ? $changedSince->format('Y-m-dTH:i:sP') : NULL,
        ],
        'include' => implode(',', $include),
      ];
    }
    $this->result = $this->guzzleClient->get(
      'personal' . ($self ? '/selbst' : ''),
      $this->formatRequestOptions($body ?? [])
    );
    return $this->formatResult();
  }

  public function getOrganisation(bool $self = TRUE) {
    return $this->guzzleClient
      ->get('organisation/selbst/stammdaten')
      ->getBody();
  }

  /**
   * Retrieves records of type "Helferstunden".
   *
   * @param $id
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
   * @param array|NULL $include
   *   A list of linked objects to include in the response, e. g.
   *   - "anlass"
   *   - "typ"
   *   - "user"
   *   - "anlass.referenz"
   *
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function getHelferstunden($id = NULL, bool $own = TRUE, \DateTime $from = NULL, \DateTime $to = NULL, array $include = []) {
    $from ??= new \DateTime('-6 months');
    if (!$id) {
      $body = [
        'filter' => [
          'eigene' => $own,
          'von' => $from->format('Y-m-d'),
          'bis' => $to->format('Y-m-d'),
        ],
        'include' => implode(',', $include),
      ];
    }
    return $this->get(
      'helferstunden' . ($id ? '/' . $id : ''),
      [
        'body' => $body,
      ]
    );
  }

}
