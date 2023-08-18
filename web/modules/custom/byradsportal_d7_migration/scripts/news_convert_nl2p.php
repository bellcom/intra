<?php


use Drupal\Core\Database\Database;
use Drupal\node\Entity\Node;

$database = \Drupal::database();
$migrateDatabase = Database::getConnection('default', 'migrate');

// Getting all migrate NIDS.
$node_migrate_tables = [
  'migrate_map_byradsportal_d7_node_news',
];

$nids = [];
foreach ($node_migrate_tables as $table) {
  $table_nids = $database->select($table)->fields($table, [
    'destid1',
  ])
    ->isNotNull('destid1')
    ->execute()
    ->fetchCol();

  $nids += $table_nids;
}

$i = 0;
$totalUpdates = 0;

foreach ($nids as $nid) {
    $node = Node::load($nid);
    print_r('Node found: [' . $node->id() . '] ' . $node->label());
    $description = $node->get('field_os2web_news_description')->value;
    $description = nl2p($description, FALSE);

    $node->field_os2web_news_description->value = $description;
    $node->field_os2web_news_description->format = 'wysiwyg_tekst';
    $node->save();

    $totalUpdates++;

    print_r(PHP_EOL);
}

print_r("Total updated: $totalUpdates");
print_r(PHP_EOL);



function nl2p($string, $line_breaks = true, $xml = true) {

  $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

  // It is conceivable that people might still want single line-breaks
  // without breaking into a new paragraph.
  if ($line_breaks == true)
    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'), trim($string)).'</p>';
  else
    return '<p>'.preg_replace(
        array("/([\n]{2,})/i", "/([\r\n]{3,})/i","/([^>])\n([^<])/i"),
        array("</p>\n<p>", "</p>\n<p>", '$1<br'.($xml == true ? ' /' : '').'>$2'),

        trim($string)).'</p>';
}
