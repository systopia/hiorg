<?php

namespace Civi\Hiorg;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use Civi\Core\Event\GenericHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use CRM_Hiorg_ExtensionUtil as E;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements ConfigProfileInterface {

  public static function modifyFieldSpec(RequestSpec $spec): void {
    $field = new FieldSpec('xcm_profile', 'ConfigProfile', 'String');
    $field->setTitle(ts('Extended Contact manager (XCM) Profile'));
    $field->setLabel(ts('XCM Profile'));
    $field->setDescription(ts('XCM profile to use for processing contacts with this configuration profile.'));
    $field->setRequired(TRUE);
    $field->setInputType('Select');
    $field->setOptions(\CRM_Xcm_Configuration::getProfileList());
    $spec->addFieldSpec($field);

    $field = new FieldSpec('oauth_client_id', 'ConfigProfile', 'String');
    $field->setTitle(ts('OAuth Client ID'));
    $field->setLabel(ts('OAuth Client ID'));
    $field->setDescription(ts('CiviCRM OAuth Client ID to use for authenticating with this configuration profile.'));
    $field->setRequired(TRUE);
    $field->setInputType('Select');
    $oauth_clients = \Civi\Api4\OAuthClient::get()
      ->addSelect('guid', 'provider:label')
      ->execute()
      ->indexBy('guid')
      ->getArrayCopy();
    array_walk($oauth_clients, function(&$oauth_client) {
      $oauth_client = $oauth_client['guid'] . ' (' . $oauth_client['provider:label'] . ')';
    });
    $field->setOptions($oauth_clients);
    $spec->addFieldSpec($field);
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
