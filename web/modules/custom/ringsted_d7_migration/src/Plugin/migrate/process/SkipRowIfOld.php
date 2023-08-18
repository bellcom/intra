<?php

namespace Drupal\ringsted_d7_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateSkipRowException;

/**
 * Skips processing the current row if date is older than the specified.
 *
 * Available configuration keys:
 * - skipOlderThanDate: Date as string (will be converted via strtotime) below
 *   which the row will be skipped.
 * - message: (optional) A message to be logged in the {migrate_message_*} table
 *   for this row. If not set, nothing is logged in the message table.
 *
 * Example:
 *
 * @code
 *  process:
 *    created:
 *      plugin: skip_row_if_old
 *      skipOlderThanDate: "2017-01-01"
 *      source: created
 *      message: "Skipped old content"
 * @endcode
 *
 * @see \Drupal\migrate\Plugin\MigrateProcessInterface
 *
 * @MigrateProcessPlugin(
 *   id = "skip_row_if_old",
 *   handle_multiples = FALSE
 * )
 */
class SkipRowIfOld extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value < strtotime($this->configuration['skipOlderThanDate'])) {
      $message = !empty($this->configuration['message']) ? $this->configuration['message'] : '';
      throw new MigrateSkipRowException($message);
    }

    return $value;
  }

}
