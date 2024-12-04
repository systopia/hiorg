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

declare(strict_types = 1);

use CRM_Hiorg_ExtensionUtil as E;

return [
  [
    'name' => 'OptionValue__activity_type__hiorg_volunteer_hours',
    'entity' => 'OptionValue',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'activity_type',
        'label' => E::ts('HiOrg-Server Volunteer Hours'),
        'name' => 'hiorg_volunteer_hours',
        'is_default' => FALSE,
        'description' => E::ts('Volunteer Hours retrieved by the HiOrg-Server API extension.'),
        'is_optgroup' => FALSE,
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'visibility_id:name' => 'admin',
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
];
