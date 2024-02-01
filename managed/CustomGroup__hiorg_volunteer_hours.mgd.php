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
    'name' => 'CustomGroup__hiorg_volunteer_hours',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'hiorg_volunteer_hours',
        'title' => E::ts('Volunteer Hours'),
        'extends' => 'Activity',
        'extends_entity_column_value:name' => [
          'hiorg_volunteer_hours',
        ],
        'style' => 'Inline',
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__hiorg_id',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'hiorg_id',
        'label' => E::ts('HiOrg-Server ID'),
        'data_type' => 'Int',
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
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
    'name' => 'CustomField__hiorg_volunteer_hours__start_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'start_date',
        'label' => E::ts('Start Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'date_format' => 'yy-mm-dd',
        'time_format' => 2,
        'note_columns' => 60,
        'note_rows' => 4,
        'column_name' => 'start_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__end_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'end_date',
        'label' => E::ts('End Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'date_format' => 'yy-mm-dd',
        'time_format' => 2,
        'column_name' => 'end_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__hours',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'hours',
        'label' => E::ts('Number of Hours'),
        'data_type' => 'Float',
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'text_length' => 255,
        'column_name' => 'hours',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__call_out_distance_km',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'call_out_km',
        'label' => E::ts('Call-Out Distance (km)'),
        'data_type' => 'Float',
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'text_length' => 255,
        'column_name' => 'call_out_distance_km',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__occasion',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'occasion',
        'label' => E::ts('Occasion'),
        'html_type' => 'Text',
        'is_searchable' => TRUE,
        'text_length' => 255,
        'column_name' => 'occasion_id',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__hiorg_volunteer_hours__organization',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'hiorg_volunteer_hours',
        'name' => 'organization',
        'label' => E::ts('Organization'),
        'data_type' => 'EntityReference',
        'html_type' => 'Autocomplete-Select',
        'is_searchable' => TRUE,
        'text_length' => 255,
        'column_name' => 'organization_id',
        'fk_entity' => 'Contact',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
