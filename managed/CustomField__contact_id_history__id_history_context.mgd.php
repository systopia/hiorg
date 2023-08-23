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

use CRM_Hiorg_ExtensionUtil as E;
use CRM_Identitytracker_Configuration;

return [
  // Re-define a managed entity for the "context" custom fields for the Identity
  // Tracker extension, but setting "is_active" to TRUE.
  // TODO: Find out how to set a weight so that this managed entity wins over
  //       the original definition which sets "is_active" to FALSE.
  [
    'name' => 'CustomField__contact_id_history__id_history_context',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => CRM_Identitytracker_Configuration::GROUP_NAME,
        'name' => CRM_Identitytracker_Configuration::CONTEXT_FIELD_NAME,
        'is_active' => TRUE,
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];


