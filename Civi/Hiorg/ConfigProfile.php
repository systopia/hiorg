<?php

namespace Civi\Hiorg;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use Civi\Core\Event\GenericHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CRM_Hiorg_ExtensionUtil as E;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements ConfigProfileInterface {

  public static function dataFieldSpec(): ?array {
    return [
      'xcm_profile' => [
        'type' => 'Text',
        'title' => E::ts('Extended Contact Manager (XCM) Profile'),
        'description' => E::ts('XCM profile to use for processing contacts with this configuration profile.'),
        'required' => TRUE,
      ],
      'oauth_client_id' => [
        'type' => 'Text',
        'title' => E::ts('OAuth Client ID'),
        'description' => E::ts('CiviCRM OAuth Client ID to use for authenticating with this configuration profile.'),
        'required' => TRUE,
      ],
    ];
  }

  public static function afformFields(): ?array {

  }

  /**
   * @return string
   */
  public function getXcmProfileName(): string {
    return $this->data['xcm_profile'];
  }

  /**
   * @return int
   */
  public function getOauthClientId(): int {
    return $this->data['oauth_client_id'];
  }

}
