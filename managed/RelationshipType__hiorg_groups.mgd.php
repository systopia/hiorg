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

use CRM_Hiorg_ExtensionUtil as E;

return [
  [
    'name' => 'RelationshipType__hiorg_groups',
    'entity' => 'RelationshipType',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name_a_b' => 'hiorg_groups',
        'label_a_b' => E::ts('HiOrg-Server: Group Membership'),
        'name_b_a' => 'hiorg_groups',
        'label_b_a' => E::ts('HiOrg-Server: Group Membership'),
        'description' => E::ts('Stores group memberships of HiOrg users in their organisation\'s groups.'),
        'contact_type_a' => 'Individual',
        'contact_type_b' => 'Organization',
        'is_reserved' => TRUE,
        'is_active' => TRUE,
      ],
      'match' => [
        'name_a_b',
      ],
    ],
  ],
];
