<?php
/*-------------------------------------------------------+
| SYSTOPIA HiOrg-Server API                              |
| Copyright (C) 2024 SYSTOPIA                            |
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

return [
  [
    'name' => 'Job__hiorg_synchronize_contacts',
    'entity' => 'Job',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'run_frequency:name' => 'Always',
        'name' => E::ts('HiOrg-Server API: Synchronize Contacts'),
        'description' => E::ts('Fetches HiOrg-Server users and related records via the HiOrg-Server API and synchronizes them with CiviCRM contacts.'),
        'api_entity' => 'Hiorg',
        'api_action' => 'synchronizeContacts',
        'parameters' => 'version=4',
        'is_active' => FALSE,
      ],
      'match' => [],
    ],
  ],
];
