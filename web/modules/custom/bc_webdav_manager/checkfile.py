#!/usr/bin/env python
# -*- coding: utf-8 -*-
import pexpect
import sys

# To make this script work, you have to install cadaver:
# apt install cadaver

# Start cadaver
child = pexpect.spawn('cadaver ' + '"' + sys.argv[1] + '"')
# Expect it to spit the below out
child.expect('dav:/webdav/')
# Send this to cadaver
child.sendline('discover ' + '"' + sys.argv[2] + '"')
# Expect the below line in return
child.expect('Discovering *')
# Read the output
child.readline()
# Removing the output linebreak by calling the print on child.before
print(child.before)
