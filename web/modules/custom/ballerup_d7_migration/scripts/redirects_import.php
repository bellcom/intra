<?php

use Drupal\Component\Utility\UrlHelper;

$argv = $_SERVER['argv'];
if (count($argv) < 4) {
  exit("This script supposed to be run ONLY via drush with full path to CSV file as first argument.
Example: drush scr script.php /path/to/redirects.csv
CSV file format:
\"/source/redirect/path\", \"http://external.url/destination/redirect/path\"
\"/source/path\", \"internal:/destination/redirect/path\"
  ");
}

$storage = \Drupal::entityTypeManager()->getStorage('redirect');
$messenger = \Drupal::service('messenger');

$added = 0;
$skipped = 0;
/** @var \Drupal\redirect\RedirectRepository $repository */
$repository = \Drupal::service('redirect.repository');
$data = getImportData($argv[3], $messenger);
if (empty($data)) {
  exit('No input data.');
}

foreach ($data as $row) {
  $source = $row[0];
  $destination = $row[1];
  if (!UrlHelper::isValid($source)) {
    $messenger->addWarning(sprintf('Source URL "%s" is not valid.', $source));
    $skipped++;
    continue;
  }
  if (!UrlHelper::isValid($destination)) {
    $messenger->addWarning(sprintf('Destination URL "%s" is not valid.', $destination));
    $skipped++;
    continue;
  }
  $existing = $repository->findBySourcePath(ltrim ($source, '/'));
  if (!empty($existing)) {
    $messenger->addWarning('Redirect for URL ' . $source . ' already exists.');
    $skipped++;
    continue;
  }

  /** @var \Drupal\redirect\Entity\Redirect $redirect */
  $redirect = $storage->create();
  $redirect->setSource($source);
  $redirect->setRedirect($destination);
  $redirect->setStatusCode(301);
  $redirect->save();
  $messenger->addMessage('Added redirect for URL ' . $source);
  $added++;
}
$messenger->addMessage(sprintf('Redirects adding finished: total: %d, added %d, skipped %d', count($data), $added, $skipped));

function getImportData($data_file_path, $messenger) {
  $path = $data_file_path;
  $messenger->addMessage(sprintf('Getting data from CSV file %s.', $path));
  $file = fopen($path, "r");
  if (!$file) {
    return NULL;
  }
  $data = [];
  while (!feof($file)) {
    $row = fgetcsv($file, 0, ",");
    if (empty($row)) {
      continue;
    }
    if (!is_array($row)) {
      $messenger->addWarning(sprintf('Wrong line %s during parsing %s.', $row, $path));
      continue;
    }
    $row = array_map("utf8_encode", $row);
    $data[] = $row;

  }
  fclose($file);
  $messenger->addMessage(sprintf('Read %d rows from csv file %s.', count($data), $data_file_path));
  return $data;
}

