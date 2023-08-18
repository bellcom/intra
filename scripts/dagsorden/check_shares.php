#!/usr/bin/php
<?php
/**
 * This script checks if the file shares at Ringsted are available, and makes
 * sure they are mounted. On top of this, the script also checks what the last
 * file is. Upon errors, it will send them by mail, and later by POST when our
 * event console is up and running.
 *
 * Maintained and written by Bellcom's Operations
 *
 * Last revision:
 * Jan 26 2021
 *
 */

if (PHP_SAPI !== 'cli') {
  return;
}

/**
 * Configuration
 */

/** Configuration */
$shortopts = "h::s::p::c::m::l::d::";
$longopts = ["host", "sharedArray", "port", "cred", "mountPath", "log", "debug"];
$opts = getopt($shortopts, $longopts);
$port = 445;
$debug = FALSE;
foreach ($opts as $key => $value) {
  switch ($key) {
    case 'h':
    case 'host':
      $host = $value;
      break;

    case 's':
    case 'sharedArray':
      $sharesArray = explode(',', $value);
      break;

    case 'p':
    case 'port':
      $port = $value;
      break;

    case 'c':
    case 'cred':
      $credentials = $value;
      break;

    case 'm':
    case 'mountPath':
      $mountPath = $value;
      break;

    case 'l':
    case 'log':
      $logFileArg = $value;
      break;

    case 'd':
    case 'debug':
      $debug = TRUE;
      break;

  }
}
print_r($opts);


if (!$host || !$sharesArray || !$credentials || !$mountPath) {
  die("The script is used like so: -h=host -s=directory1,directory2 -m=/path/mount/to -c=/path/to/cred [-p=port] [-l=/path/to/logfile]" . PHP_EOL);
}


/**
 * Code
 */
// By default script will log info into /dev/stdout.
// Unless log file could be specified as second argument
// or environment variable.
$log = empty($logFile) ? NULL : fopen($logFile, "a+");

writeToLog("----");          // Just to more easily read logs
echo writeToLog("Initiated check.");

// Check if connection exists
if (checkIfConnectionExists($host, $port)) {
  // If connection exists, check if shares are mounted, and if not, mount them.
  mountshares($host, $port, $sharesArray, $mountPath, $credentials);
  checkLastFile($mountPath, $sharesArray);
}
else {
  // in exit status - Also making sure the log file is closed properly.
  if ($log) {
    fclose($log);
  }
  exit("ERROR: Connection to $host through port $port is closed or not otherwise not available. Stopping script");
}

echo writeToLog("Script has now finished the run");

// Close opened file.
if ($log) {
  fclose($log);
}

/**
 * Functions
 */

/**
 * Simply checks if the connection to the host to the specified port is
 * accessible Currently does not send mails nor POST.
 *
 * @param $host
 * @param $port
 * @param $openLogfile
 *
 * @return bool
 */
function checkIfConnectionExists($host, $port = 445) {
  // Check if connection to host through port 445 is open
  $connectionOpen = @fsockopen($host, $port, $errNo, $errStr, 5);
  // If resource is available
  if (is_resource($connectionOpen)) {
    // Write to log
    echo writeToLog("Connection to $host through port $port is open. Continuing.");
  }
  else {
    // else, report error
    // in log
    echo writeToLog("ERROR: Connection to $host through port $port is closed or not otherwise not available. Stopping script");

  }
  return TRUE; // If everything works, it'll return true. If nothing works, it'll exit anyway.
}

/**
 *
 * Checks if shares are mounted, and if not mounts them.
 * Currently doesn't report any errors, because I don't know what an erroneous
 * output looks like at the moment.
 *
 * @param $host
 * @param $port
 * @param $sharesArray
 * @param $mountPath
 * @param $credentialFile
 *
 * @return bool
 */
function mountShares($host, $port, $sharesArray, $mountPath, $credentialFile) {
  global $debug;

  // Loop through shares
  foreach ($sharesArray as $share) {

    // Check if mount points are of type nfs or cifs
    $command = "df -P -T $mountPath/$share | tail -n +2 | awk '{print $2}'";
    if ($debug) {
      echo writeToLog("DEBUG: Executing command: " . $command);
    }
    $checkMountPoint = exec($command);
    // If the mount point is of type nfs or cifs, don't remount it, and simply write to log that it's already mounted
    if ($checkMountPoint == "cifs") {
      echo writeToLog("The mount point $mountPath/$share is of type NFS or CIFS, so it's most likely mounted already. Not remounting.");
    }
    else {
      // If the mount point is of something else, mount the remote share to the mount points
      echo writeToLog("The mount point $mountPath/$share is not of type NFS or CIFS, so it's most likely not mounted. Mounting.");
      $command = "mount -t cifs //$host/$share $mountPath/$share -o cred=$credentialFile";
      if ($debug) {
        echo writeToLog("DEBUG: Executing command: " . $command);
      }
      if ($port) {
        $command .= ',port=' . $port;
      }
      exec($command, $mountOutput);
      echo writeToLog(print_r($mountOutput, 1));
    }
  }

  return TRUE;
}

/**
 * This simply checks the last modified file for each share
 *
 * @param $mountPath
 * @param $sharesArray
 *
 * @return bool
 */
function checkLastFile($mountPath, $sharesArray) {

  $findOutput = FALSE;  // Just so we don't have an undefined variable

  foreach ($sharesArray as $share) {
    $findOutput = exec("/usr/bin/find $mountPath/$share -type f -printf '%T+\t%p\n' | /usr/bin/sort | /usr/bin/tail -1");
    echo writeToLog("The last modified file in $mountPath/$share is: $findOutput");
  }

  return $findOutput;
}

/**
 * This function helps put stuff in the configured log file
 *
 * @param $logString
 *
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
