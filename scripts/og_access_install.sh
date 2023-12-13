#!/usr/bin/env bash

SCRIPT_DIR="$(dirname "$(readlink -f "$0")")"
CONTRIB_DIR="$SCRIPT_DIR/../web/modules/contrib/"

if [ -d "$CONTRIB_DIR/og" ]; then
  wget -q -P "$CONTRIB_DIR" "https://github.com/zerolab/og/archive/og_access.zip"
fi

if [ -f "$CONTRIB_DIR/og_access.zip" ]; then
  unzip -q -d "$CONTRIB_DIR" "$CONTRIB_DIR/og_access.zip"
  rm -rf "$CONTRIB_DIR/og_access.zip"
fi

if [ -d "$CONTRIB_DIR/og-og_access" ]; then
  rm -rf "$CONTRIB_DIR/og"
  mv "$CONTRIB_DIR/og-og_access" "$CONTRIB_DIR/og"
fi




