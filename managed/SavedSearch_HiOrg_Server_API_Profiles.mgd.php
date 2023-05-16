<?php

return [
  [
    'name' => 'SavedSearch_HiOrg_Server_API_Profiles',
    'entity' => 'SavedSearch',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'HiOrg_Server_API_Profiles',
        'label' => 'HiOrg-Server API Profiles',
        'form_values' => NULL,
        'mapping_id' => NULL,
        'search_custom_id' => NULL,
        'api_entity' => 'ConfigProfile',
        'api_params' => [
          'version' => 4,
          'select' => [
            'id',
            'name',
            'selector',
            'is_active',
            'is_default',
            'access_date',
          ],
          'orderBy' => [],
          'where' => [
            [
              'type:name',
              '=',
              'hiorg',
            ],
          ],
          'groupBy' => [],
          'join' => [],
          'having' => [],
        ],
        'expires_date' => NULL,
        'description' => NULL,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
  [
    'name' => 'SavedSearch_HiOrg_Server_API_Profiles_SearchDisplay_HiOrg_Server_API_Profiles',
    'entity' => 'SearchDisplay',
    'cleanup' => 'always',
    'update' => 'unmodified',
    'params' => [
      'version' => 4,
      'values' => [
        'name' => 'HiOrg_Server_API_Profiles',
        'label' => 'HiOrg-Server API Profiles',
        'saved_search_id.name' => 'HiOrg_Server_API_Profiles',
        'type' => 'table',
        'settings' => [
          'description' => NULL,
          'sort' => [],
          'limit' => 50,
          'pager' => [],
          'placeholder' => 5,
          'columns' => [
            [
              'type' => 'field',
              'key' => 'id',
              'dataType' => 'Integer',
              'label' => 'ID',
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'name',
              'dataType' => 'String',
              'label' => 'Config Profile Name',
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'selector',
              'dataType' => 'String',
              'label' => 'Selector',
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'is_active',
              'dataType' => 'Boolean',
              'label' => 'Aktiviert',
              'sortable' => TRUE,
              'rewrite' => '',
              'icons' => [],
              'cssRules' => [
                [
                  'disabled',
                  'is_active',
                  '=',
                  FALSE,
                ],
              ],
            ],
            [
              'type' => 'field',
              'key' => 'is_default',
              'dataType' => 'Boolean',
              'label' => 'Standard',
              'sortable' => TRUE,
            ],
            [
              'type' => 'field',
              'key' => 'access_date',
              'dataType' => 'Timestamp',
              'label' => 'Access Date',
              'sortable' => TRUE,
              'rewrite' => '',
            ],
            [
              'size' => 'btn-xs',
              'links' => [
                [
                  'path' => 'civicrm/admin/setting/hiorg/profile#?ConfigProfile1=[id]',
                  'icon' => 'fa-external-link',
                  'text' => 'Edit',
                  'style' => 'default',
                  'condition' => [],
                  'entity' => '',
                  'action' => '',
                  'join' => '',
                  'target' => 'crm-popup',
                ],
              ],
              'type' => 'buttons',
              'alignment' => 'text-right',
            ],
          ],
          'actions' => TRUE,
          'classes' => [
            'table',
            'table-striped',
          ],
          'addButton' => [
            'path' => 'civicrm/admin/setting/hiorg/profile',
            'text' => 'Config Profile hinzufügen',
            'icon' => 'fa-plus',
          ],
          'headerCount' => TRUE,
          'button' => NULL,
        ],
        'acl_bypass' => FALSE,
      ],
      'match' => [
        'name',
      ],
    ],
  ],
];
