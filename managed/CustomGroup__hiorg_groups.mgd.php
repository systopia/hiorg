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

return [
  [
    'name' => 'CustomGroup__hiorg_relationship_groups',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_relationship_groups',
        'title' => E::ts('HiOrg-Server: Group Membership'),
        'extends' => 'Relationship',
        'extends_entity_column_value:name' => [
          'hiorg_groups',
        ],
        'style' => 'Inline',
        'is_active' => TRUE,
        'is_multiple' => FALSE,
        // Note: "is_reserved" hides the custom field group in the UI.
        'is_reserved' => FALSE,
        'is_public' => TRUE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionGroup__hiorg_groups',
    'entity' => 'OptionGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_groups',
        'title' => E::ts('HiOrg-Server: Groups'),
        'description' => E::ts('Available groups for all connected HiOrg-Server organisations.'),
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'option_value_fields' => [
          'name',
          'label',
          'description',
          'icon',
        ],
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_relationship_groups__group',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_group',
        'label' => E::ts('Group'),
        'custom_group_id.name' => 'hiorg_relationship_groups',
        'html_type' => 'Select',
        'data_type' => 'String',
        'is_required' => TRUE,
        'is_searchable' => TRUE,
        'is_search_range' => FALSE,
        'is_view' => FALSE,
        'in_selector' => TRUE,
        'column_name' => 'hiorg_group',
        'option_group_id.name' => 'hiorg_groups',
      ],
    ],
    'match' => ['custom_group_id.name', 'name'],
  ],
];
