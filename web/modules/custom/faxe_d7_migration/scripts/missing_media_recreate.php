<?php

use Drupal\media\Entity\Media;

print_r('Creating missing media ' . PHP_EOL);

$created_media = 0;
$ids = \Drupal::entityQuery('file')
  ->execute();

/** @var \Drupal\file\FileUsage\FileUsageInterface $file_usage */
$file_usage = \Drupal::service('file.usage');

foreach ($ids as $id) {
  /** @var \Drupal\file\Entity\File $file */
  $file = \Drupal::entityTypeManager()->getStorage('file')->load($id);

  $usage = $file_usage->listUsage($file);
  // Check if media is missing.
  if (empty($usage['file']) || !in_array('media', array_keys($usage['file']))) {
    // File is image.
    if (strpos( $file->getMimeType() , 'image' ) === 0) {
      $media = Media::create([
        'bundle' => 'images',
        'uid' => 1,
        'name' => $file->getFilename(),
        'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => $file->getFilename()
        ],
      ]);
      $media->save();
    }
    // File is document.
    else {
      $media = Media::create([
        'bundle' => 'document',
        'uid' => 1,
        'name' => $file->getFilename(),
        'field_media_file' => [
          'target_id' => $file->id()
        ],
      ]);
      $media->save();
    }

    $created_media++;
  }
}

print_r('Created missing media: ' . $created_media . PHP_EOL);
print_r('Creating missing media DONE' . PHP_EOL);
