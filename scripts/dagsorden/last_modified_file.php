#!/usr/bin/php
<?php
/**
 * This script checks the folders last updated file and logs it.
 */

if (PHP_SAPI !== 'cli') {
  return;
}

/** Configuration */
$args = $_SERVER['argv'];
if (empty($args) || count($args) < 2) {
  exit("The script is used like so: last_modified_file.php /path/to/directory [/path/to/logfile]" . PHP_EOL);
}

// By default script will log info into /dev/stdout.
// Unless log file could be specified as second argument
// or environment variable.
$logFile = empty($args[2]) ? (getenv('LOG_FILE') ?: NULL) : $args[2];

// Path to subject directory.
$directoryPath = $args[1];
if (!file_exists($directoryPath)) {
  exit("WARNING: Directory $directoryPath does not exist." . PHP_EOL);
}

/** Code */
// Open log file and put resource in variable $log
// Used later for writing and closing; +a for creating file if it doesn't exist
// and setting cursor to end of file.
$log = empty($logFile) ? NULL : fopen($logFile, "a+");

echo writeToLog("Initiated check.");
$findOutput = exec("/usr/bin/find $directoryPath -type f -printf '%TY-%Tm-%Td %TH:%TM:%.2TS\t%p\n' | /usr/bin/sort | /usr/bin/tail -1");
echo writeToLog("The last modified file in $directoryPath is: $findOutput");
echo writeToLog("Script has now finished the run");

// Close opened file.
if ($log) {
  fclose($log);
}

/**
 * This function helps put stuff in the configured log file
 *
 * @param $logString
 * @return string
 */
function writeToLog($logString) {
  // Get current date and time.
  $dt = new DateTime();
  $now = $dt->format('Y-m-d H:i:s');

  // Attach date to logString.
  $dateString = "[" . $now . "] - ";
  $logString = $dateString . $logString . "\n";

  global $log;
  // Do some logging.
  if (isset($log)) {
    fwrite($log, $logString);
  }
  return $logString;
}
