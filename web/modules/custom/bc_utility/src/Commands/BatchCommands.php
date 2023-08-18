<?php
namespace Drupal\bc_utility\Commands;

use Drush\Commands\DrushCommands;
use Drupal\bc_utility\Controller\OldContentNotify;

Class BatchCommands extends DrushCommands
{

  /**
   * Search node with foraeldet status
   *
   *
   * @command old:notify
   * @aliases oln
   * @options $options arr AN option that takes multiple values.
   */
  public function oldContentNotify($options=array())
  {
    $now = date("Y-m-01");
    $run = false;
    $last_start = \Drupal::keyValue('bc_utility_cron')->get('last_start');
    if (empty($last_start))
    {
      $run = true;
      \Drupal::keyValue('bc_utility_cron')->set('last_start', $now);
    }
    else
    {
      $lastdiff = strtotime("+1 month", strtotime($last_start));
      $run = ($now !== date('Y-m-01', $lastdiff));
      // TODO or older that now + one month
    }

    if ($run)
    {
      \Drupal::logger('bc_utility')->notice('bc utility cron start');
      \Drupal\bc_utility\Controller\OldContentNotity::handler();
    }

  }


  /**
   * just for test
   *
   * @command dev:test
   * @aliases devtest
   * @options $options arr AN option that takes multiple values.
   */
  public function fortest($options=array()) {


  }



}
