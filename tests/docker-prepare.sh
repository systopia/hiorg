#!/bin/bash
set -eu -o pipefail

CONFIG_PROFILES_VERSION=1.0.0-beta1
XCM_VERSION=1.13.1
IDENTITYTRACKER_VERSION=1.4.0

EXT_DIR=$(dirname "$(dirname "$(realpath "$0")")")
EXT_NAME=$(basename "$EXT_DIR")

i=0
while ! mysql -h "$CIVICRM_DB_HOST" -P "$CIVICRM_DB_PORT" -u "$CIVICRM_DB_USER" --password="$CIVICRM_DB_PASS" -e 'SELECT 1;' >/dev/null 2>&1; do
  i=$((i+1))
  if [ $i -gt 10 ]; then
    echo "Failed to connect to database" >&2
    exit 1
  fi

  echo -n .
  sleep 1
done

echo

export XDEBUG_MODE=off
if mysql -h "$CIVICRM_DB_HOST" -P "$CIVICRM_DB_PORT" -u "$CIVICRM_DB_USER" --password="$CIVICRM_DB_PASS" "$CIVICRM_DB_NAME" -e 'SELECT 1 FROM civicrm_setting LIMIT 1;' >/dev/null 2>&1; then
  cv flush
else
  # For headless tests it is required that CIVICRM_UF is defined using the corresponding env variable.
  sed -E "s/define\('CIVICRM_UF', '([^']+)'\);/define('CIVICRM_UF', getenv('CIVICRM_UF') ?: '\1');/g" \
    -i /var/www/html/sites/default/civicrm.settings.php
  civicrm-docker-install

  # Avoid this error:
  # The autoloader expected class "Civi\ActionSchedule\Mapping" to be defined in
  # file "[...]/Civi/ActionSchedule/Mapping.php". The file was found but the
  # class was not in it, the class name or namespace probably has a typo.
  #
  # Necessary for CiviCRM 5.66.0 - 5.74.x.
  # https://github.com/civicrm/civicrm-core/blob/5.66.0/Civi/ActionSchedule/Mapping.php
  if [ -e /var/www/html/sites/all/modules/civicrm/Civi/ActionSchedule/Mapping.php ] \
      && grep -q '// Empty file' /var/www/html/sites/all/modules/civicrm/Civi/ActionSchedule/Mapping.php; then
    rm /var/www/html/sites/all/modules/civicrm/Civi/ActionSchedule/Mapping.php
  fi

  # For headless tests these files need to exist.
  touch /var/www/html/sites/all/modules/civicrm/sql/test_data.mysql
  touch /var/www/html/sites/all/modules/civicrm/sql/test_data_second_domain.mysql

  cv ext:download "config-profiles@https://github.com/systopia/config-profiles/releases/download/$CONFIG_PROFILES_VERSION/config-profiles-$CONFIG_PROFILES_VERSION.zip"
  cv ext:download "de.systopia.xcm@https://github.com/systopia/de.systopia.xcm/releases/download/$XCM_VERSION/de.systopia.xcm-$XCM_VERSION.zip"
  cv ext:download "de.systopia.identitytracker@https://github.com/systopia/de.systopia.identitytracker/releases/download/$IDENTITYTRACKER_VERSION/de.systopia.identitytracker-$IDENTITYTRACKER_VERSION.zip"

  cv ext:enable "$EXT_NAME"
fi

cd "$EXT_DIR"
composer update --no-progress --prefer-dist --optimize-autoloader
composer composer-phpunit -- update --no-progress --prefer-dist
