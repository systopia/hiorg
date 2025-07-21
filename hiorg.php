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

// phpcs:disable PSR1.Files.SideEffects
require_once 'hiorg.civix.php';
// phpcs:enable

use Civi\Hiorg\EventSubscriber\ConfigProfiles\ConfigProfilesSubscriber;
use Civi\Hiorg\EventSubscriber\ContactSubscriber;
use Civi\Hiorg\EventSubscriber\IdentitytrackerSubscriber;
use Civi\Hiorg\EventSubscriber\OAuth\OAuthProviderSubscriber;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function hiorg_civicrm_config(\CRM_Core_Config &$config): void {
  _hiorg_civix_civicrm_config($config);

  Civi::dispatcher()->addSubscriber(new OAuthProviderSubscriber());
  Civi::dispatcher()->addSubscriber(new ConfigProfilesSubscriber());
  Civi::dispatcher()->addSubscriber(new IdentitytrackerSubscriber());
  Civi::dispatcher()->addSubscriber(new ContactSubscriber());
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function hiorg_civicrm_install(): void {
  _hiorg_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function hiorg_civicrm_enable(): void {
  _hiorg_civix_civicrm_enable();
}
