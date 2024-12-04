# HiOrg-Server API

This extension connects your CiviCRM to
a [*HiOrg-Server*](https://www.hiorg-server.de/) instance.

## Features

### Synchronization with CiviCRM data structures

* Synchronizes *HiOrg-Server* user data into CiviCRM contacts using the [
  *Extended Contact Matcher* (XCM)](https://github.com/systopia/de.systopia.xcm)
  extension for matching existing contacts
* Comes with some custom fields to synchronize *HiOrg-Server*-specific user
  data:
  * Additional contact data
  * Driving license information
  * Membership data
* Utilizes the [*Entity Construction
  Kit* (ECK)](https://github.com/systopia/de.systopia.eck) extension for
  providing CiviCRM entities to synchronize *HiOrg-Server*-specific data:
  * Educations
  * Qualifications
* Adds a relationship type for synchronizing *HiOrg-Server* group memberships in
  *HiOrg-Server* organisations
* Adds an activity type for synchronizing volunteer hours of *HiOrg-Server*
  users
* Utilizes the [*Identity
  Tracker*](https://github.com/systopia/de.systopia.identitytracker) extension
  for tracking *HiOrg-Server* user IDs on CiviCRM contacts
* Utilizes the [*Configuration
  Profiles*](https://github.com/systopia/config-profiles) extension for
  providing configurable synchronization profiles
* Provides an OAuth provider for authorizing against the *HiOrg-Server* API

### Configuration

The extension conects to the *HiOrg-Server* API using configurable profiles,
which also hold configuration for synchronization with CiviCRM entities:

* OAuth Client ID - needs to be configured independently
* API Base URI - base URI of the *HiOrg-Server* API endpoints
* Organisation ID - CiviCRM organisation contact representing the organisation
  using the *HiOrg-Server* instance
* *Extended Contact Matcher* (XCM) profile - used for synchronization

### API

This extension comes with a CiviCRM API (version 4) entity `Hiorg` and the
following actions implementing corresponding *HiOrg-Server* API endpoints (see
their [documentation](https://api.hiorg-server.de/docs)):

* `getPersonal`
* `getAusbildungen`
* `getHelferstunden`

Synchronization of all those data with CiviCRM data structures (contacts, ECK
entities, etc.) is also done via a CiviCRM API action,
called `synchronizeContacts`, and another one called `synchronizeVolunteerHours`
for synchronizing *Helferstunden* records (volunteer hours) with CiviCRM
activities.
