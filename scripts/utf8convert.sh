#!/bin/bash
SOURCEDIR=$1
DESTDIR=$2
USAGE="The script is used like so: $0 /path/to/source /path/to/destination"
if [ "$1" != "" ]; then
	if [ "$2" != "" ]; then
		set -e

		echo 'Starting utf-8 converting script'
		# Debug here
		echo 'SOURCEDIR is:'
		echo $SOURCEDIR
		echo 'DestDir is:'
		echo $DESTDIR

		for file in $(ls $SOURCEDIR); do
			echo 'converting this file: '$file
			convmv -r -f windows-1252 -t UTF-8 $SOURCEDIR$file --notest > /dev/null
			echo 'done'
		done || exit 0

		for file in $(ls $SOURCEDIR); do
			echo 'moving this file: '$file
			rsync -avO $SOURCEDIR$file $DESTDIR > /dev/null
			# Removing old files
			rm -rf $SOURCEDIR$file
			echo 'done'
		done || exit 0

		echo 'Converting process complete, ending script'

	else
		echo $USAGE
	fi
else
	echo $USAGE
fi





