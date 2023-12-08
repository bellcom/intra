<?php
namespace Drupal\bc_basic\Commands;

use Drupal\views\Views;
use Drush\Commands\DrushCommands;
use Drupal\user\Entity\User;

Class BatchCommands extends DrushCommands
{

  /**
   * create 5 users bellcomX
   *
   * @command bc:b
   * @aliases bcb
   * @options $options arr AN option that takes multiple values.
   */
  public function testb($options=array())
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
   * create a user with a node and delete user
   *
   * @command bc:a
   * @aliases bca
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
    $new->save();

    echo "new node id " . $new->id() . "\n";

    $nids = \Drupal::entityQuery('node')
      ->condition('uid', $user->id())
      ->execute();

    echo count($nids) . " nodes \n";

    $this->confirm('continue ');
    $user->delete();

  }


}
