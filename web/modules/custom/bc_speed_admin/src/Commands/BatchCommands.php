<?php
namespace Drupal\bc_speed_admin\Commands;

use Drupal\Core\Site\Settings;
use Drush\Commands\DrushCommands;
use Drupal\bc_speed_admin\Controller;

Class BatchCommands extends DrushCommands
{

    /**
     * speedadmin test run
     *
     * @command sd:test
     * @aliases sdt
     * @options $options arr AN option that takes multiple values.
     */
    public function testRun($options=array())
    {
        echo "test run\n";
    }

    /**
     * speedadmin sync
     *
     * @command sd:sync
     * @aliases sdsync
     * @options $options arr AN option that takes multiple values.
     */
    public function sync($options=array())
    {
        echo "sync ...\n";

        Controller\Sync::handler();

        echo "done ...\n";
    }




}