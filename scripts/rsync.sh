#!/bin/bash

RSYNC=$(which rsync)
CHMOD=$(which chmod)
CHOWN=$(which chown)
MOUNT=$1
UPLOAD=$2
UTF8CONVERT=$3
USAGE="The script is used like so: $0 /path/to/mount /path/to/upload"

if [ "$1" != "" ]; then
	if [ "$2" != "" ]; then
		$RSYNC -rqt $MOUNT/* $UPLOAD/
		$CHMOD -R g+rw $UPLOAD
		$CHOWN -R www-data:www-data $UPLOAD
	else
		echo $USAGE
	fi
else
	echo $USAGE
fi
