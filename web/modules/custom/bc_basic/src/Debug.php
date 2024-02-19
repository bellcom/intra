<?php

namespace Drupal\bc_basic;

Class Debug {

  private $logpath = '/var/www/logs';
  private $logfile = 'debug.log';

  public function _log($message=null, $keys=false) : void {
    if (!empty($message) && is_writable($this->logpath)) {
      $log = $this->logpath . '/' . $this->logfile;
      if (is_string($message) || is_numeric($message)) {
        file_put_contents( $log, "#\n" . print_r( $message , true) . "\n", FILE_APPEND);

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

  public function _gettype($message=null) : void {
    if (!empty($message) && is_writable($this->logpath)) {
      $log = $this->logpath . '/' . $this->logfile;
      file_put_contents( $log, "#\n" . gettype($message) . "\n", FILE_APPEND);
    }
  }


  public static function log($message=null, $keys=false) : void { (new self)->_log($message, $keys); }
  public static function gettype($message=null) : void { (new self())->_gettype($message); }


}
