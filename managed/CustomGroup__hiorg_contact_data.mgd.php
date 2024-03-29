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
    'name' => 'CustomGroup__hiorg_contact_data',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_contact_data',
        'title' => E::ts('Contact Data'),
        'extends' => 'Individual',
        'style' => 'Inline',
        'is_active' => TRUE,
        'is_multiple' => FALSE,
        // Note: "is_reserved" hides the custom field group in the UI.
        'is_reserved' => FALSE,
        'is_public' => TRUE,
        'icon' => '',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__birth_place',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'birth_place',
        'label' => E::ts('Birth Place'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'birth_place',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__emergency_info',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'emergency_info',
        'label' => E::ts('Emergency Information'),
        'data_type' => 'Memo',
        'html_type' => 'TextArea',
        'note_columns' => 60,
        'note_rows' => 4,
        'attributes' => 'rows=4, cols=60',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'column_name' => 'emergency_info',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__profession',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'profession',
        'label' => E::ts('Profession'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'profession',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__employer',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'employer',
        'label' => E::ts('Employer'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'employer',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__position',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'position',
        'label' => E::ts('Position / Task'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'position',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__management_function',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'management_function',
        'label' => E::ts('Management Function'),
        'data_type' => 'Boolean',
        // When using CheckBox the field serialize is set to 1...
        'html_type' => 'Select',
        'is_searchable' => TRUE,
        'is_search_range' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'column_name' => 'management_function',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_contact_data__note',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_contact_data',
        'name' => 'note',
        'label' => E::ts('Note'),
        'data_type' => 'Memo',
        'html_type' => 'TextArea',
        'note_columns' => 60,
        'note_rows' => 4,
        'attributes' => 'rows=4, cols=60',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'column_name' => 'note',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
