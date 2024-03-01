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
  'hiorg.synchronizeContacts.lastSync' => [
    'name' => 'hiorg.synchronizeContacts.lastSync',
    'type' => 'Array',
    'title' => E::ts('Last Contacts Synchronization'),
    'description' => E::ts('Timestamps of last synchronization of contacts, grouped by OAuth Client IDs used for fetching data.'),
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'hiorg.synchronizeVolunteerHours.lastSync' => [
    'name' => 'hiorg.synchronizeVolunteerHours.lastSync',
    'type' => 'Array',
    'title' => E::ts('Last Volunteer Hours Synchronization'),
    'description' => E::ts('Timestamps of last synchronization of volunteer hours, grouped by OAuth Client IDs used for fetching data.'),
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'hiorg.synchronizeVerifications.lastSync' => [
    'name' => 'hiorg.synchronizeVerifications.lastSync',
    'type' => 'Array',
    'title' => E::ts('Last Verifications Synchronization'),
    'description' => E::ts('Timestamps of last synchronization of verifications, grouped by OAuth Client IDs used for fetching data and HiOrg-Server user IDs.'),
    'is_domain' => 1,
    'is_contact' => 0,
  ],
  'hiorg.synchronizeEducations.lastSync' => [
    'name' => 'hiorg.synchronizeEducations.lastSync',
    'type' => 'Array',
    'title' => E::ts('Last Educations Synchronization'),
    'description' => E::ts('Timestamps of last synchronization of educations, grouped by OAuth Client IDs used for fetching data and HiOrg-Server user IDs.'),
    'is_domain' => 1,
    'is_contact' => 0,
  ],
];
