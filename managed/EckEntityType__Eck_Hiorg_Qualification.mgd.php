<?php

use CRM_Hiorg_ExtensionUtil as E;

return [
  [
    'name' => 'EckEntityType_Hiorg_Qualification',
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
];
