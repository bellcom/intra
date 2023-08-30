<?php

namespace Drupal\midtpunktet_d7_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\tamper\Exception\SkipTamperDataException;

/**
 * @MigrateProcessPlugin(
 *   id = "skip_group_condition",
 *   handle_multiples = TRUE
 * )
 */
class SkipGroupCondition extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $tid = $value[$this->configuration['index']]['tid'];

    if (empty($tid) || $tid != 2) {
      $message = !empty($this->configuration['message']) ? $this->configuration['message'] : '';
      throw new MigrateSkipRowException($message);
    }
    return $value[$this->configuration['index']];
  }

}
