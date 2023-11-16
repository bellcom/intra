<?php
namespace Drupal\bc_basic\Commands;

use Drupal\views\Views;
use Drush\Commands\DrushCommands;
Class BatchCommands extends DrushCommands
{

  /**
   * test script, put whatever you want here
   *
   * @command bc:test
   * @aliases bct
   * @options $options arr AN option that takes multiple values.
   */
  public function test($options=array()) {


  }



  /**
   * List all hooks
   *
   * @command bc:hooks
   * @aliases bch
   * @options $options arr AN option that takes multiple values.
   */
  public function showHooks($options=array())
  {



  }



}
