<?php

namespace Civi\Hiorg;

class ConfigProfile extends \CRM_ConfigProfiles_BAO_ConfigProfile {

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
