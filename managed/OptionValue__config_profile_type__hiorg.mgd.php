<?php

use CRM_Hiorg_ExtensionUtil as E;

return [
  [
    'name' => 'OptionValue__config_profile_type__hiorg',
    'entity' => 'OptionValue',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'config_profile_type',
        'label' => E::ts('HiOrg-Server API'),
        'value' => '\Civi\Hiorg\ConfigProfile',
        'name' => 'hiorg',
        'grouping' => NULL,
        'filter' => 0,
        'is_default' => FALSE,
        'weight' => 1,
        'description' => E::ts('Configuration profiles for the HiOrg-Server API extension.'),
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
