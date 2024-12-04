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
    'name' => 'EckEntityType__Hiorg_Qualification',
    'entity' => 'EckEntityType',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Hiorg_Qualification',
        'label' => E::ts('HiOrg-Server: Qualification'),
        'icon' => NULL,
        'in_recent' => FALSE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup__Eck_Hiorg_Qualification',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Eck_Hiorg_Qualification',
        'title' => E::ts('HiOrg-Server: Qualification'),
        'extends' => 'Eck_Hiorg_Qualification',
        'style' => 'Inline',
        'collapse_display' => TRUE,
        'is_active' => TRUE,
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
    'name' => 'CustomField__Hiorg_Qualififcation__Contact',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Qualification',
        'name' => 'Contact',
        'label' => E::ts('Contact'),
        'data_type' => 'EntityReference',
        'html_type' => 'Autocomplete-Select',
        'is_reserved' => TRUE,
        'is_required' => TRUE,
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'column_name' => 'contact_id',
        'filter' => 'contact_type=Individual',
        'in_selector' => FALSE,
        'fk_entity' => 'Contact',
        'fk_entity_on_delete' => 'cascade',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Qualification__Date_acquired',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Qualification',
        'name' => 'Date_acquired',
        'label' => E::ts('Acquirement Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_reserved' => FALSE,
        'is_required' => FALSE,
        'is_searchable' => TRUE,
        'is_search_range' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'date_format' => 'yy-mm-dd',
        'time_format' => 0,
        'column_name' => 'date_acquired',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
