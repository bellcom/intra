#!/bin/bash
if [ ! -d "/var/solr/data/os2web-core" ]; then
  echo "Creating SOLR core os2web-core"
  bin/solr start
  bin/solr create_core -c os2web-core -d /solr-config
  bin/solr stop
fi

# Starting SOLR service.
solr-foreground
