<?php

namespace Civi\Hiorg;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use Civi\Core\Event\GenericHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CRM_Hiorg_ExtensionUtil as E;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements ConfigProfileInterface {

  const NAME = 'hiorg';

  public static function getMetadata(bool $includeFields = FALSE): array {
    $metadata = [
      'name' => self::NAME,
      'label' => E::ts('HiOrg-Server API'),
      'description' => E::ts('Configuration profiles for the HiOrg-Server API extension.'),
    ];

    if ($includeFields) {
      $oauth_clients = \Civi\Api4\OAuthClient::get(FALSE)
        ->addSelect('guid', 'provider:label')
        ->execute()
        ->indexBy('id')
        ->getArrayCopy();
      array_walk($oauth_clients, function (&$oauth_client) {
        $oauth_client = $oauth_client['guid'] . ' (' . $oauth_client['provider:label'] . ')';
      });
      $metadata['fields'] = [
        'xcm_profile' => (new FieldSpec('xcm_profile', 'ConfigProfile_' . self::NAME, 'String'))
          ->setTitle(ts('Extended Contact manager (XCM) Profile'))
          ->setLabel(ts('XCM Profile'))
          ->setDescription(ts('XCM profile to use for processing contacts with this configuration profile.'))
          ->setRequired(TRUE)
          ->setInputType('Select')
          ->setOptions(\CRM_Xcm_Configuration::getProfileList()),
        'oauth_client_id' => (new FieldSpec('oauth_client_id', 'ConfigProfile_' . self::NAME, 'String'))
          ->setTitle(ts('OAuth Client ID'))
          ->setLabel(ts('OAuth Client ID'))
          ->setDescription(ts('CiviCRM OAuth Client ID to use for authenticating with this configuration profile.'))
          ->setRequired(TRUE)
          ->setInputType('Select')
          ->setOptions($oauth_clients),
      ];
    }

    return $metadata;
  }

  public static function modifyFieldSpec(RequestSpec $spec): void {
  }

  public static function processValues(array $item, array &$data): void {
  }

  /**
   * @return string
   */
  public function getXcmProfileName(): string {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return $data['xcm_profile'];
  }

  /**
   * @return string
   */
  public function getOauthClientId(): string {
    $data = self::unSerializeField($this->data, self::SERIALIZE_JSON);
    return $data['oauth_client_id'];
  }

  public static function getById(int $id) {
    $configProfile = new self();
    $configProfile->copyValues(['id' => $id]);
    $configProfile->find(TRUE);
    return $configProfile;
  }

  public static function loadAll() {
    return \Civi\Api4\ConfigProfile::get('hiorg')
      ->addWhere('is_active', '=', TRUE)
      ->execute()
      ->indexBy('id')
      ->column('name');
  }

}
