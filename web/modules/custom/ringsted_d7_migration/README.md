# Ringsted D7 Migration module

## Module purpose

The aim of this module is to provide migration of the content from Ringsted.dk D7 version to OS2Web D8.

## Install

1. Module is part of the repository, and can be installed:
    ```
    drush en ringsted_d7_migration
    ```

2. Create separate Database and import D7 version of the site there. Database must be defined in **settings.php** file (next to the default one):
```
$databases['default']['default'] = array (
  ...
);

// Describe migration database.
$databases['migrate']['default'] = array (
  'database'  => '[db_name]',
  'username'  => '[db_user]',
  'password'  => '[db_password]',
  'prefix' => '',
  'host'      => '[db_host]',
  'port' => '',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver'    => 'mysql',
);
```

## Migrate quick run
Run the entire migration process with single command:
```
cd web
sh ./modules/custom/ringsted_d7_migration/scripts/migrate.sh
```

## Migrate process details for manual run

The recommended usage migrate process is via Drush:

Read more about the Drush commands for Migrate API on [Migrate tools](https://www.drupal.org/project/migrate_tool).

 * Migrate section terms:
    ```
    drush migrate:import ringsted_d7_taxonomy_section
    ```
 * Migrate News:
    ```
    drush migrate:import ringsted_d7_node_news
    ```
  * Migrate Postlister:
    ```
    drush migrate:import ringsted_d7_node_postlister
    ```

## Useful hints

When changing migration definition in **.yml** files, import the changes with:
```
drush cim --partial --source=modules/custom/ringsted_d7_migration/config/install -y
```

If migration stopped with an error, it might need to be reset, e.g.:
```
drush migrate:reset [migation_name]
```

Instead of migrating/importing all entities, it is faster to do development with importing only few entities. Use **limit** flag:
```
drush migrate:import [migation_name] --limit=5
```

By default each migration run will import only new entities, but sometimes you want to overwrite what you already have instead of importing new ones. That can be done with **update** flag:
```
drush migrate:import [migation_name] --update
```

Use it with **limit** flag to force update previously imported entities:
```
drush migrate:import [migation_name] --limit=5 --update
```

## Contribution

Project is opened for new features and os course bugfixes.
If you have any suggestion or you found a bug in project, you are very welcome
to create an issue in github repository issue tracker.
For issue description there is expected that you will provide clear and
sufficient information about your feature request or bug report.

### Code review policy
See [OS2Web code review policy](https://github.com/OS2Web/docs#code-review)

### Git name convention
See [OS2Web git name convention](https://github.com/OS2Web/docs#git-guideline)
