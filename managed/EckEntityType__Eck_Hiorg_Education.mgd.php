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
    'name' => 'EckEntityType__Hiorg_Education',
    'entity' => 'EckEntityType',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Hiorg_Education',
        'label' => E::ts('HiOrg-Server: Education'),
        'icon' => NULL,
        'in_recent' => FALSE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__eck_sub_types__Generic',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'eck_sub_types',
        'label' => E::ts('Generic'),
        'name' => 'Generic',
        'grouping' => 'Hiorg_Education',
        'weight' => 0,
        'is_reserved' => TRUE,
        'icon' => 'fa-graduation-cap',
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup__Eck_Hiorg_Education',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Eck_Hiorg_Education',
        'title' => E::ts('HiOrg-Server: Education'),
        'extends' => 'Eck_Hiorg_Education',
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
    'name' => 'CustomField__Hiorg_Education__Contact',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Contact',
        'label' => E::ts('Contact'),
        'data_type' => 'EntityReference',
        'html_type' => 'Autocomplete-Select',
        'is_reserved' => FALSE,
        'is_required' => TRUE,
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'column_name' => 'contact_id',
        'filter' => 'contact_type=Individual',
        'in_selector' => FALSE,
        'fk_entity' => 'Contact',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Education__Hiorg_id',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Hiorg_id',
        'label' => E::ts('HiOrg-Server Record ID'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'hiorg_id',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Education__Date_acquired',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
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
  [
    'name' => 'CustomField__Hiorg_Education__Date_expires',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Date_expires',
        'label' => E::ts('Expiration Date'),
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
        'column_name' => 'date_expires',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Education__Date_last_revision',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Date_last_revision',
        'label' => E::ts('Last Revision Date'),
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
        'column_name' => 'date_last_revision',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Education__Date_next_revision',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Date_next_revision',
        'label' => E::ts('Next Revision Date'),
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
        'column_name' => 'date_next_revision',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Education__Document',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Education',
        'name' => 'Document',
        'label' => E::ts('Document'),
        'data_type' => 'File',
        'html_type' => 'File',
        'is_reserved' => FALSE,
        'is_required' => FALSE,
        'is_searchable' => FALSE,
        'is_search_range' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'column_name' => 'document',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
