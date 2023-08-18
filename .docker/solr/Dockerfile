FROM solr:8.5.2

# Copying solr core config to get abylity create project SOLR core
COPY config /solr-config

# Copying solr startup settings.
COPY solr.in.sh /etc/default/solr.in.sh

# Initiate entrypoint for create SOLR core
COPY entrypoint.sh /
ENTRYPOINT ["/entrypoint.sh"]
