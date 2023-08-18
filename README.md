# OS2Web Hjemmeside Drupal 8 project

## Usage

git clone git@github.com:OS2web/os2web8.git

composer install

cd web/sites/default

cp env.example.settings.php env.settings.php

If you stick to the development settings, then you don't have to edit the env.settings.php.

Otherwise, within the env.settings.php file, change the variable `$settings['project_env']` to either `PROD_ENV` or `STAGE_ENV`, depending on if it is a production or staging environment.

Then after that, create either `prod.settings.php` or `stage.settings.php`, and put in the necessary information.

Here is an example for `prod.settings.php`:

```php
/**
 * @file
 * Production environment settings.
 */

$databases['default']['default'] = array (
  'database' => '[DB_NAME]',
  'username' => '[DB_USERNAME]',
  'password' => '[DB_PASSWORD]',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['hash_salt'] = '[GENERATE HASH SALT]';

$settings['file_private_path'] = '../private';
$settings['file_temp_path'] = '../tmp';

$settings['trusted_host_patterns'] = [
  '^yourhost\.devel\.dk$',
];
```

As you can see, you have to have the following information:

- Database name
- Database username
- Database password
- Hash salt

You can also remove the `$settings['trusted_host_patters']` if you want to, but it is recommended to keep it and configured for your site for security reasons.

If your database is hosted on a different host, be sure to change this in the database array also.

To generate a hash salt, run the following command:
```bash
drush eval "var_dump(Drupal\Component\Utility\Crypt::randomBytesBase64(55))"
```

Thereafter, you can install the site with drush:
```bash
drush si os2web
```

There are a bunch of other variables you can use to further set the values from start. For that look [here](https://www.drush.org/11.x/commands/site_install/).

After that, your os2web is installed!

Once installation is done, you can go in and set default drupal theme from available list on `/admin/appearance`

There are plenty other OS2Web modules, which you may want to install.
These you can find under `/admin/modules`.

## Local development

OS2Web subsites - See [.docker/os2web-subsites/README.md](.docker/os2web-subsites/README.md)

## Contribution

OS2Web Hjemmeside projects is opened for new features and os course bugfixes.
If you have any suggestion or you found a bug in project, you are very welcome
to create an issue in github repository issue tracker.
For issue description there is expected that you will provide clear and
sufficient information about your feature request or bug report.

### Code review policy
See [OS2Web code review policy](https://github.com/OS2Web/docs#code-review)

### Git name convention
See [OS2Web git name convention](https://github.com/OS2Web/docs#git-guideline)
