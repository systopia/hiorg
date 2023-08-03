<?php

use CRM_Hiorg_ExtensionUtil as E;
use CRM_Identitytracker_Configuration;

return [
  // Re-define a managed entity for the "context" custom fields for the Identity
  // Tracker extension, but setting "is_active" to TRUE.
  [
    'name' => 'CustomField__contact_id_history__id_history_context',
    'entity' => 'CustomField',
    'cleanup' => 'never',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'custom_group_id.name' => CRM_Identitytracker_Configuration::GROUP_NAME,
        'name' => CRM_Identitytracker_Configuration::CONTEXT_FIELD_NAME,
        'is_active' => TRUE,
      ],
      'match' => [
        'custom_group_id',
        'name',
      ],
    ],
  ],
];


