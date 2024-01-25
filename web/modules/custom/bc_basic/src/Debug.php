<?php

namespace Drupal\bc_basic;

Class Debug {

  public function _log($message=null, $keys=false) : void {
    if (!empty($message) && is_writable('/var/www/logs')) {
      $log = '/var/www/logs/debug.log';
      if (is_string($message)) {
        file_put_contents($log, "#\n" . print_r( $message , true) . "\n", FILE_APPEND);

      } elseif ((is_array($message) || is_object($message)) && is_bool($keys) && $keys) {
        file_put_contents($log, "#\n" . print_r( array_keys($message) , true) . "\n", FILE_APPEND);

      } elseif ((is_array($message) || is_object($message)) && !$keys) {
        file_put_contents($log, "#\n" . print_r( $message , true) . "\n", FILE_APPEND);

      }
      else {
        file_put_contents($log, "#\n" . gettype( $message ) . "\n", FILE_APPEND);
      }
    }
  }


  public static function log($message=null, $keys=false) : void { (new self)->_log($message, $keys); }

}
