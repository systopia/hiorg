# Usage

## Manual synchronization

!!! note
    This sections is yet to be completed.

## API

The extension provides CiviCRM API (version 4) API actions for fetching and
synchronizing data from your *HiOrg-Server* instance with the API entity
`Hiorg`.

## `synchronizeContacts`

This action synchronizes all data retrievable by the extension with CiviCRM data
structures, i.e. Contacts, Relationships, custom entities for *Qualification*,
*Education*, and *Verification* records.

Internally, API actions implmenting the *HiOrg-Server* API endpoints are being
used to retrieve data, filtered by records changed since the date of the last
synchronization, which is being stored in a CiviCRM setting.

## `synchronizeVolunteerHours`

!!! note
    This section is yet to be completed.

## API actions implementing *HiOrg-Server* API endpoints

### `getPersonal`

This action implements the **HiOrg-Server** API
endpoint [`GET/personal`](https://api.hiorg-server.de/docs#operation/PersonalGET)
awhich retrieves information about *HiOrg-Server* users with all their
attributes, group memberships, permissions and qualifications.

This API action does not synchronize any of the retrieved data with CiviCRM.

### `getAusbildungen`

This action implements the **HiOrg-Server** API
endpoint [`GET/personal/{userid}/ausbildungen`](https://api.hiorg-server.de/docs#operation/ausbildungUserGET)
awhich retrieves information about *HiOrg-Server* users' education records.

This API action does not synchronize any of the retrieved data with CiviCRM.

### `getUeberpruefungen`

This action implements the **HiOrg-Server** API
endpoint [`GET/personal/{userid}/uberpruefungen`](https://api.hiorg-server.de/docs#operation/ueberpreufungGet)
awhich retrieves information about *HiOrg-Server* users' verification records.

This API action does not synchronize any of the retrieved data with CiviCRM.

### `getHelferstunden`

This action implements the **HiOrg-Server** API
endpoint [`GET/helferstunden`](https://api.hiorg-server.de/docs#operation/helferstundenGetList)
awhich retrieves information about *HiOrg-Server* volunteer hours.

This API action does not synchronize any of the retrieved data with CiviCRM.
