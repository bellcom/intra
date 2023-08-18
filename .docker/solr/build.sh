#!/bin/bash

if [ $# -eq 0 ]; then
  echo "WARNING: There was no tag-name provided!"
  echo "Script usage is: './build.sh tag-name'"
  echo "Example: './build.sh 1.0.3'"
  exit 0
fi

docker build ./ -t dkbellcom/os2web8-solr:$1

if [ "$2" = "--push" ]; then
  echo "Docker login to dkbellcom. Type password:"
  read -s DOCKERHUB_PASS
  echo "Authorization..."
  echo $DOCKERHUB_PASS | docker login --username dkbellcom --password-stdin

  if [ $? -eq 0 ]; then
    echo "Pushing image to docker hub ..."
    docker push dkbellcom/os2web8-solr:$1
    echo "Check your image here https://hub.docker.com/repository/docker/dkbellcom/os2web8-solr/tag"
  else
    echo "Image is not pushed to docker hub :("
  fi;
fi;
