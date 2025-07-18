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

namespace Civi\Hiorg\ConfigProfiles;

use Civi\Api4\OAuthClient;
use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use CRM_Hiorg_ExtensionUtil as E;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements ConfigProfileInterface {

  const NAME = 'hiorg';

  public static function getFields(): array {
    return [
      'xcm_profile' => (new FieldSpec('xcm_profile', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(E::ts('Extended Contact manager (XCM) Profile'))
        ->setLabel(E::ts('XCM Profile'))
        ->setDescription(E::ts('XCM profile to use for processing contacts with this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Select')
        ->setOptionsCallback([\CRM_Xcm_Configuration::class, 'getProfileList']),
      'oauth_client_id' => (new FieldSpec('oauth_client_id', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(E::ts('OAuth Client'))
        ->setLabel(E::ts('OAuth Client'))
        ->setDescription(E::ts('CiviCRM OAuth Client to use for authenticating with this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Select')
        ->setOptionsCallback([__CLASS__, 'getOAuthClientOptions']),
      'organisation_id' => (new FieldSpec('organisation_id', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(E::ts('Organisation'))
        ->setLabel(E::ts('Organisation'))
        ->setDescription(E::ts('CiviCRM organisation contact to use as corresponding contact with this configuration profile.'))
        ->setRequired(TRUE)
        ->setFkEntity('Contact')
        ->setInputAttrs(['filter' => ['contact_type' => 'Organization']])
        ->setInputType('EntityRef'),
      'api_base_uri' => (new FieldSpec('api_base_uri', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(E::ts('API Base URI'))
        ->setLabel(E::ts('API Base URI'))
        ->setDescription(E::ts('HiOrg-Server API base URI for this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Text'),
      'exclude_hiorg_user_status' => (new FieldSpec('exclude_hiorg_user_status', 'ConfigProfile_' . self::NAME, 'Array'))
        ->setTitle(E::ts('Exclude HiOrg-Server User status'))
        ->setLabel(E::ts('Exclude HiOrg-Server User status'))
        ->setDescription(E::ts('HiOrg-Server user status to exclude when retrieving user data via the HiOrg-Server API.'))
        ->setRequired(FALSE)
        ->setInputType('Select')
        ->setInputAttrs(['multiple' => TRUE])
        ->setOperators(['IN', 'NOT IN'])
        ->setOptionsCallback([__CLASS__, 'getHiorgUserStatusOptions']),
    ];
  }

  public static function modifyFieldSpec(RequestSpec $spec): void {
  }

  public static function processValues(array &$profile): void {
  }

  public static function getOAuthClientOptions(): array {
    $oauth_clients = OAuthClient::get(FALSE)
      ->addSelect('id', 'guid', 'provider:label')
      // TODO: Filter for HiOrg-Server OAuth clients only - this can't be done
      //       using the type ("hiorg"), as there might be other compatible
      //       OAuth providers, e.g. when the HiOrg-Server API is available on
      //       another server.
      ->execute()
      ->indexBy('id')
      ->getArrayCopy();
    array_walk($oauth_clients, function (&$oauth_client) {
      // OAuthClient entities do not have a configurable label which might be
      // useful to display here. See
      // https://lab.civicrm.org/dev/core/-/issues/4765
      $oauth_client = E::ts(
        '[%1] %2 (Provider: %3)',
        [
          1 => $oauth_client['id'],
          2 => $oauth_client['guid'],
          3 => $oauth_client['provider:label'],
        ]
      );
    });
    return $oauth_clients;
  }

  public static function getHiorgUserStatusOptions(): array {
    $hiorgUserStatus = [
      'aktiv',
      'eingeschraenkt',
      'extern',
      'gesperrt',
    ];
    return array_combine($hiorgUserStatus, $hiorgUserStatus);
  }

  public function getId(): int {
    return (int) $this->id;
  }

  /**
   * @return string
   * @throws \CRM_Core_Exception
   */
  public function getXcmProfileName(): string {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return $data['xcm_profile'];
  }

  /**
   * @return int
   * @throws \CRM_Core_Exception
   */
  public function getOauthClientId(): int {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return (int) $data['oauth_client_id'];
  }

  /**
   * @return int
   * @throws \CRM_Core_Exception
   */
  public function getOrganisationId(): int {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return (int) $data['organisation_id'];
  }

  /**
   * @return string
   * @throws \CRM_Core_Exception
   */
  public function getApiBaseUri(): string {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return (string) $data['api_base_uri'];
  }

  /**
   * @return string
   * @throws \CRM_Core_Exception
   */
  public function getExcludeHiOrgUserStatus(): array {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return (array) $data['exclude_hiorg_user_status'];
  }

  /**
   * @throws \Exception
   */
  public static function getById(int $id): ConfigProfile {
    $configProfile = new self();
    $configProfile->copyValues(['id' => $id]);
    if (!$configProfile->find(TRUE)) {
      throw new \Exception(E::ts('Error loading configuration profile with ID %1', [1 => $id]));
    }
    return $configProfile;
  }

  /**
   * @throws \API_Exception
   * @throws \CRM_Core_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public static function loadAll(): array {
    return \Civi\Api4\ConfigProfile::get('hiorg')
      ->addWhere('is_active', '=', TRUE)
      ->execute()
      ->indexBy('id')
      ->column('name');
  }

}
