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
    'name' => 'CustomGroup__driving_license',
    'entity' => 'CustomGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'driving_license',
        'title' => E::ts('Driving License'),
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
    'name' => 'OptionGroup__driving_license_classes',
    'entity' => 'OptionGroup',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'driving_license_classes',
        'title' => E::ts('Driving License Classes'),
        'data_type' => 'String',
        'is_reserved' => TRUE,
        'is_active' => TRUE,
        'is_locked' => FALSE,
        'option_value_fields' => [
          'name',
          'label',
          'description',
        ],
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__AM',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('AM'),
        'value' => 'AM',
        'name' => 'AM',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__A',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('A'),
        'value' => 'A',
        'name' => 'A',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__A1',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('A1'),
        'value' => 'A1',
        'name' => 'A1',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__A2',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('A2'),
        'value' => 'A2',
        'name' => 'A2',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__B',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('B'),
        'value' => 'B',
        'name' => 'B',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__BE',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('BE'),
        'value' => 'BE',
        'name' => 'BE',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__B96',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('B with Code 96'),
        'value' => 'B96',
        'name' => 'B96',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__B196',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('B with Code 196'),
        'value' => 'B196',
        'name' => 'B196',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__C',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('C'),
        'value' => 'C',
        'name' => 'C',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__CE',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('CE'),
        'value' => 'CE',
        'name' => 'CE',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__C1',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('C1'),
        'value' => 'C1',
        'name' => 'C1',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__C1E',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('C1E'),
        'value' => 'C1E',
        'name' => 'C1E',
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__D',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('D'),
        'value' => 'D',
        'name' => 'D',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__DE',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('DE'),
        'value' => 'DE',
        'name' => 'DE',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__D1',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('D1'),
        'value' => 'D1',
        'name' => 'D1',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__D1E',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('D1E'),
        'value' => 'D1E',
        'name' => 'D1E',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__T',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('T'),
        'value' => 'T',
        'name' => 'T',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'OptionValue__driving_license_classes__L',
    'entity' => 'OptionValue',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'driving_license_classes',
        'label' => E::ts('L'),
        'value' => 'L',
        'name' => 'L',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'description' => '',
        'is_optgroup' => FALSE,
        'is_reserved' => FALSE,
        'is_active' => TRUE,
      ],
      'match' => [
        'option_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__driving_license__classes',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'driving_license',
        'name' => 'classes',
        'label' => E::ts('Classes'),
        'data_type' => 'String',
        'html_type' => 'Select',
        'is_searchable' => TRUE,
        'is_search_range' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'column_name' => 'classes',
        'option_group_id.name' => 'driving_license_classes',
        'serialize' => 1,
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__driving_license__restriction',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'driving_license',
        'name' => 'restriction',
        'label' => E::ts('Restriction'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'restriction',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__driving_license__license_number',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'driving_license',
        'name' => 'license_number',
        'label' => E::ts('License Number'),
        'data_type' => 'String',
        'html_type' => 'Text',
        'is_searchable' => FALSE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'text_length' => 255,
        'column_name' => 'license_number',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
  [
    'name' => 'CustomField__driving_license__license_date',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => 'driving_license',
        'name' => 'license_date',
        'label' => E::ts('License Date'),
        'data_type' => 'Date',
        'html_type' => 'Select Date',
        'is_searchable' => TRUE,
        'is_search_range' => TRUE,
        'is_active' => TRUE,
        'is_view' => FALSE,
        'date_format' => 'yy-mm-dd',
        'time_format' => NULL,
        'column_name' => 'license_date',
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];
