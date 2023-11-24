# Configuration

## Connecting to *HiOrg-Server* instances

Authentication against the *HiOrg-Server* API utilizes CiviCRM's *OAuth* core
extension, which the *HiOrg-Server API* extension comes with an *OAuth Provider*
for. Thus, you will need an *OAuth client* configured within your *HiOrg-Server*
instance, consisting of a *Client ID* and a *Client Secret*.

*OAuth* configuration can be found in CiviCRM's navigation menu at
*Administration* » *System Settings* » *OAuth* (`/civicrm/admin/oauth`).

For *HiOrg-Server* intances hosted on *HiOrg-Server*'s premises (i.e.
at https://hiorg-server.de), head to the client configuration for the *OAuth
Provider* named *HiOrg-Server* (`hiorg`).

!!! info
    *HiOrg-Server* instances can be installed on your own server. You will then
    need an *OAuth Provider* for your server's domain, which currently requires
    developing an extension. This might be made configurable in future releases.

1. Enter the *OAuth Client* ID
2. Enter the *OAuth Client* Secret
3. Save the client configuration
4. Obtain an authorization token (*Auth Code*) - you will be redirected to your
   *HiOrg-Server* instance's login form
5. Enter your *HiOrg-Server* user credentials - you willbe redirected to a
   confirmation form for authorizing CiviCRM to use the *HiOrg-Server* API on
   behalf of your *HiOrg-Server* user
6. Confirm the authorization request - you will be redirected to your CiviCRM
   *OAuth* configuration and should see the authorization token

There might be multiple authorization tokens per *OAuth* client. The extension
uses only the first and tries to refresh the token automatically before it
expires.

## *HiOrg-Server* API profiles

Synchronizing data requires a configuration profile (most likely one per
connceted *HiOrg-Server* instance). Once you have successfully configured an
*OAuth Client* for connecting to your *HiOrg-Server* instance, head to the
*HiOrg-Server - API Profile* configuration, which can be found in the navigation
menu at *Adminstration* » *Automation* » *HiOrg-Server - API Profile*
(`/civicrm/admin/config-profile/hiorg`). Add a new profile and fill in the
following configuration options:

* The *OAuth Client* to use for connecting to the *HiOrg-Server* instance
* The organisation contact representing your *HiOrg-Server* organisation, this
  is necessary for creating relationships etc. between synchronized contacts and
  your organisation contact
* The *Extended Contact Matcher* (XCM) profile to use for matching existing
  contacts
* The base URI of the *HiOrg-Server* API - this might be derived automatically
  in future releases, once *OAuth Providers* are configurable
* Whether the profile is active (included during synchronisation)

## Scheduled Job

!!! note
    This section is yet to be completed.
