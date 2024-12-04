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
    'name' => 'EckEntityType__Hiorg_Verification',
    'entity' => 'EckEntityType',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Hiorg_Verification',
        'label' => E::ts('HiOrg-Server: Verification'),
        'icon' => NULL,
        'in_recent' => FALSE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomGroup__Eck_Hiorg_Verification',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'Eck_Hiorg_Verification',
        'title' => E::ts('HiOrg-Server: Verification'),
        'extends' => 'Eck_Hiorg_Verification',
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
    'name' => 'CustomField__Hiorg_Verification__Contact',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
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
    'name' => 'CustomField__Hiorg_Verification__Hiorg_id',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
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
    'name' => 'CustomField__Hiorg_Verification__Date_last_revision',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
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
    'name' => 'CustomField__Hiorg_Verification__Date_next_revision',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
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
    'name' => 'CustomField__Hiorg_Verification__Revision_result',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'always',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
        'name' => 'Revision_result',
        'label' => E::ts('Revision Result'),
        'data_type' => 'Boolean',
        // When using CheckBox the field "serialize" is set to 1.
        'html_type' => 'Select',
        'is_required' => FALSE,
        'is_searchable' => TRUE,
        'is_search_range' => FALSE,
        'is_active' => TRUE,
        'is_view' => TRUE,
        'column_name' => 'revision_result',
        'serialize' => 0,
        'in_selector' => FALSE,
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__Hiorg_Verification__Result_restriction',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'Eck_Hiorg_Verification',
        'name' => 'Result_restriction',
        'label' => E::ts('Restriction'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'result_restriction',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
