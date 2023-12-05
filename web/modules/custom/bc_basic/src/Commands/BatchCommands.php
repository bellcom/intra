<?php
namespace Drupal\bc_basic\Commands;

use Drupal\views\Views;
use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;

Class BatchCommands extends DrushCommands
{

  /**
   * test script, put whatever you want here
   *
   * @command bc:test
   * @aliases bct
   * @options $options arr AN option that takes multiple values.
   */
  public function test($options=array())
  {

    $userIds = array("A", "B", "C", "D");
    foreach( $userIds AS $id ) {
      $user = User::create();
      $user->setUsername("tester" . $id);
      $user->setPassword("tester" . $id);
      $user->enforceIsNew();
      $user->setEmail('bellcom' . $id . '@bellcom.dk');
      $user->addRole('editor');
      $user->activate();
      $user->set('field_vis_telefon_i_email_signat', 'hide_both');
      $user->save();
    }
  }

  /**
   * test script, put whatever you want here
   *
   * @command bc:testa
   * @aliases bcta
   * @options $options arr AN option that takes multiple values.
   */
  public function testa($options=array()) {

    $user = User::create();
    $user->setUsername("testerX");
    $user->setPassword("testerX");
    $user->enforceIsNew();
    $user->setEmail('bellcomX@bellcom.dk');
    $user->addRole('editor');
    $user->activate();
    $user->set('field_vis_telefon_i_email_signat', 'hide_both');
    $user->save();

    echo "new user id " . $user->id() . "\n";


    $new = \Drupal\node\Entity\Node::create(['type' => 'os2web_page']);
    $new->setTitle('new programmatically node');
    $new->setOwnerId($user->id());
    $new->setPublished();
//    $new->set('status', 1);
    $new->save();

    echo "new node id " . $new->id() . "\n";


    $user->delete();

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
