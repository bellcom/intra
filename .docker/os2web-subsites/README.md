# OS2Web-subsites docker image

Image based on official [Drupal image](https://hub.docker.com/_/drupal)

Image includes all functional project files inside (PHP code, Composer dependencies).

Drupal content files should be attached as [Volumes](https://docs.docker.com/storage/volumes/) to container:
* private files - `/opt/drupal/private`
* sites - `/opt/drupal/web/sites`
* apache sites-availbale - `/etc/apache/sites-availbale`
* apache sites-enabled - `/etc/apache/sites-enabled`
* sites config storage - `/opt/drupal/config`
* cron jobs - `/var/spool/cron/crontabs`

## Build image

To build image use `build.sh` script with git tag of OS2Web project release as first argument.
NOTE: You should have existing tag for OS2Web project before.

Example:
```
./build.sh [tag-name] --push
```

`--push` - when you this option build will be pushed to docker hub.

## Local developemnt through docker

While main OS2Web solution is using Docksal for local development. It's easier for testing purposes to use bare docker-compose environment for OS2Web sibsutes.

It expects that all action would be performed on Docker subsites folder .docker/os2web-subsites


### Restart docker stack

docker-compose down
sudo rm -rf volumes/*
docker volume rm os2web-subsites-docker-compose_mysql
docker-compose up -d
docker-compose exec php bash



### Install subsites creator
sudo -E -u www-data drush si --account-pass=admin -y && drush en bc_subsites admin_toolbar_tools -y


### Entironments variables

Define your .env file as it stated below
```
#
# Copy and rename this file to .env at root of this project.
#

# Uncomment and populate as needed.
## Subsite admin UI db creadentials
MYSQL_DATABASE=os2subsites
MYSQL_HOSTNAME=mariadb
MYSQL_ROOT_PASSWORD=root
MYSQL_PASSWORD=os2subsites
MYSQL_PORT=3306
MYSQL_USER=os2subsites

## Drupal salt
DRUPAL_HASH_SALT=w-NR7Q2C3URQH6qdRvDPlbxVqdvyGMbsOwyXgtqkdJGNI7FBMbrD79UMx2DDNlSqvfmY-OOKLw
## Domain suffix that would be used for subsites
DOMAIN_SUFFIX=os2subsites.local
## Path to base site config directory
BASE_SUBSITE_CONFIG_DIR=/opt/drupal/config/ay-test0604-3.${DOMAIN_SUFFIX}/sync

# OS2SUBSITE common varaibles.
## Flag for using environment variables
## Comment this variale to use values from `config.sh file`
USE_ENV_CONFIG=1
## Drupal project root directory
BASEDIR=/opt/drupal
SERVERIP=127.0.0.1
## Default Drupal profile for subsites.
## Use `base_config` value
PROFILE=base_config
ALLOWED_INSTALL_PROFILES=minimal,os2web
## Email used for subsite admin user.
EMAIL=drupal@bellcom.dk
## Path to
SCRIPTDIR=/opt/drupal/scripts/os2subsites_provision
## Path to drush
DRUSH=/usr/local/bin/drush
## Database creadentials for creating subsite db.
DBHOST=mariadb
DBUSER_HOST=mariadb
### Optional varialbles. Leave it empty to user root as user name.
DB_ROOT_USER=root
### Optional varialbles. Leave it empty to user root user without password from localhost.
DB_ROOT_PASSWORD=root
## External db provisioning. NOTE: To disable external provisioning you have to JUST comment variable
EXTERNAL_DB_PROVISIONING=1
PROVISIONING_SOURCES_PATH=/opt/drupal/private/subsite-credentials
## For internal db provisioning you have to specify directory to mysql db files are stored. It's used for db existing check.
#DBDIR=/var/lib/mysql
## Subsite admin pass
ADMINPASS=admin
## Path to subsite vhost template
VHOSTTEMPLATE=${SCRIPTDIR}/../os2subsites_provision/os2subsites-vhost-template
## Drupal document root directory path
MULTISITE=${BASEDIR}/web
## Path to file where subsites registering.
SITESFILE=${MULTISITE}/sites/sites.php
## Temp directory for subsites
TMPDIRBASE=${BASEDIR}/tmp
## Log directory for subsites
LOGDIRBASE=${BASEDIR}/logs
## Session directory for subsites
SESSIONDIRBASE=${BASEDIR}/sessions
## Username for subsite administrator user. User will be created on subsites.
SITEADMIN=subsiteadmin
## Apache webserver username.
APACHEUSER=www-data
## Root user name. Sometimes this variable is empty.
USER=root
# Version of Drupal core. Accepted values 7,8
DRUPAL=8
# Additional option for site-install command
INSTALL_OPTIONS=

## Variable used only in docker-compose.yaml
COMPOSE_PROJECT_NAME=os2web-subsites-docker-compose
TAG=2.1.5-rc-BKDK-521 # <---- This value should be one you use for testing.
WEB_SERVER_PORT=8098
```

### Handling domains

Main domain for subsite creator is localhost:8098

NOTE: Port could be different if you changed `WEB_SERVER_PORT` variable.

Each subsites domain should be defined in your local `/etc/hosts` file like:
```
127.0.0.1 new-subsite.os2subsites.local
```

NOTE: Domain suffix `.os2subsites.local` could be different if you changed `DOMAIN_SUFFIX` variable.
