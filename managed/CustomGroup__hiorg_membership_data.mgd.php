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
    'name' => 'CustomGroup__hiorg_membership_data',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_membership_data',
        'title' => E::ts('Membership Data'),
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
    'name' => 'CustomField__hiorg_membership_data__membership_number',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_membership_data',
        'name' => 'membership_number',
        'label' => E::ts('Membership Number'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'membership_number',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_membership_data__membership_start_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_membership_data',
        'name' => 'membership_start_date',
        'label' => E::ts('Membership Start Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'date_format' => 'yy-mm-dd',
        'time_format' => NULL,
        'column_name' => 'membership_start_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_membership_data__membership_end_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_membership_data',
        'name' => 'membership_end_date',
        'label' => E::ts('Membership End Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'date_format' => 'yy-mm-dd',
        'time_format' => NULL,
        'column_name' => 'membership_end_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_membership_data__membership_transfer_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_membership_data',
        'name' => 'membership_transfer_date',
        'label' => E::ts('Transferred from Youth Organization'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'date_format' => 'yy-mm-dd',
        'time_format' => NULL,
        'column_name' => 'membership_transfer_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
