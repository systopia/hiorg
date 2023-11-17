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

namespace Civi\Hiorg\Synchronize;

class ContactIdentity {

  public const IDENTIFIER_TYPE = 'hiorg_user';

  /**
   * @param int $contactId
   * @param string $hiorgUserId
   * @param mixed $context
   *
   * @return void
   * @throws \CRM_Core_Exception
   */
  public static function addIdentity(int $contactId, string $hiorgUserId, $context) {
    civicrm_api3(
      'Contact',
      'addidentity',
      [
        'contact_id' => $contactId,
        'identifier_type' => self::IDENTIFIER_TYPE,
        'identifier' => $hiorgUserId,
        'context' => $context,
      ]
    );
  }

  /**
   * @param mixed $context
   * @param string $hiorgUserId
   *   The HiOrg-Server user ID to pass to ID Tracker.
   *
   * @return int|null
   *   The CiviCRM Contact ID.
   * @throws \CRM_Core_Exception
   */
  public static function identifyContact($context, string $hiorgUserId): ?int {
    $idTrackerResult = civicrm_api3(
      'Contact',
      'findbyidentity',
      [
        'identifier_type' => self::IDENTIFIER_TYPE,
        'identifier' => $hiorgUserId,
        'context' => $context,
      ]
    );
    return $idTrackerResult['id'] ?? NULL;
  }

}
