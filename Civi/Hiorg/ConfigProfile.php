<?php

namespace Civi\Hiorg;

use Civi\Api4\Service\Spec\FieldSpec;
use Civi\Api4\Service\Spec\RequestSpec;
use Civi\ConfigProfiles\ConfigProfileInterface;
use Civi\Core\Event\GenericHookEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile implements EventSubscriberInterface, ConfigProfileInterface {

  /**
   * @var int $oauth_client_id
   *   The ID of the OAuth client (from oauth-client extension).
   */
  protected int $oauth_client_id;

  /**
   * @var string $xcm_profile_name
   *   THe name of the Extended Contact Matcher (XCM) profile.
   */
  protected string $xcm_profile_name;

  /**
   * @param string $xcm_profile
   * @param int $oauth_client
   */
  public function __construct() {
    $this->xcm_profile_name = $this->data['xcm_profile'];
    $this->oauth_client_id = $this->data['oauth_client_id'];
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      'civi.config_profiles.types' => 'configProfileTypes'
      ] + parent::getSubscribedEvents();
  }

  public static function configProfileTypes(GenericHookEvent $event) {
    $event->types['hiorg'] = self::class;
  }

  public static function modifyFieldSpec(RequestSpec $spec) {
    $field = new FieldSpec('xcm_profile', 'ConfigProfile', 'String');
    $field->setTitle(ts('Extended Contact manager (XCM) Profile'));
    $field->setLabel(ts('XCM Profile'));
    $field->setDescription(ts('XCM profile to use for processing contacts with this configuration profile.'));
    $field->setRequired(TRUE);
    $field->setInputType('Text');
    $spec->addFieldSpec($field);

    $field = new FieldSpec('oauth_client_id', 'ConfigProfile', 'String');
    $field->setTitle(ts('OAuth Client ID'));
    $field->setLabel(ts('OAuth Client ID'));
    $field->setDescription(ts('CiviCRM OAuth Client ID to use for authenticating with this configuration profile.'));
    $field->setRequired(TRUE);
    $field->setInputType('Text');
    $spec->addFieldSpec($field);
  }

  /**
   * @return string
   */
  public function getXcmProfileName(): string {
    return $this->xcm_profile_name;
  }

  /**
   * @return int
   */
  public function getOauthClientId(): int {
    return $this->oauth_client_id;
  }

}
