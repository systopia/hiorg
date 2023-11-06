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
use Civi\Hiorg\Synchronize\ContactIdentity;

return [
  [
    'name' => 'OptionValue__contact_id_history_type__hiorg',
    'entity' => 'OptionValue',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'contact_id_history_type',
        'label' => E::ts('HiOrg-Server User ID'),
        'value' => ContactIdentity::IDENTIFIER_TYPE,
        'name' => ContactIdentity::IDENTIFIER_TYPE,
        'grouping' => 'hiorg',
        'filter' => 0,
        'is_default' => FALSE,
        'weight' => 1,
        'description' => E::ts('The ID of records of the type "user" retrieved through the HiOrg-Server API.'),
        'is_optgroup' => FALSE,
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'component_id' => NULL,
        'domain_id' => NULL,
        'visibility_id:name' => 'admin',
        'icon' => NULL,
        'color' => NULL,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
];
