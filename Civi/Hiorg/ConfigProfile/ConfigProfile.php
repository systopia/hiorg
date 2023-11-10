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

namespace Civi\Hiorg\ConfigProfile;

use Civi\Api4\OAuthClient;
use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use CRM_Hiorg_ExtensionUtil as E;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements ConfigProfileInterface {

  const NAME = 'hiorg';

  public static function getFields(): array {
    $oauth_clients = OAuthClient::get(FALSE)
      ->addSelect('id', 'guid', 'provider:label')
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
    return [
      'xcm_profile' => (new FieldSpec('xcm_profile', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(ts('Extended Contact manager (XCM) Profile'))
        ->setLabel(ts('XCM Profile'))
        ->setDescription(ts('XCM profile to use for processing contacts with this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Select')
        ->setOptions(\CRM_Xcm_Configuration::getProfileList()),
      'oauth_client_id' => (new FieldSpec('oauth_client_id', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(ts('OAuth Client'))
        ->setLabel(ts('OAuth Client'))
        ->setDescription(ts('CiviCRM OAuth Client to use for authenticating with this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Select')
        ->setOptions($oauth_clients),
      'organisation_id' => (new FieldSpec('organisation_id', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(ts('Organisation'))
        ->setLabel(ts('Organisation'))
        ->setDescription(ts('CiviCRM organisation contact to use as corresponding contact with this configuration profile.'))
        ->setRequired(TRUE)
        ->setFkEntity('Contact')
        ->setInputAttrs(['filter' => ['contact_type' => 'Organization']])
        ->setInputType('EntityRef'),
      'api_base_uri' => (new FieldSpec('api_base_uri', 'ConfigProfile_' . self::NAME, 'String'))
        ->setTitle(ts('API Base URI'))
        ->setLabel(ts('API Base URI'))
        ->setDescription(ts('HiOrg-Server API base URI for this configuration profile.'))
        ->setRequired(TRUE)
        ->setInputType('Text'),
    ];
  }

  public static function modifyFieldSpec(RequestSpec $spec): void {
  }

  public static function processValues(array &$profile): void {
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
