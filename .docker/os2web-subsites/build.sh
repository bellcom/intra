#!/bin/bash

if [ $# -eq 0 ]; then
  echo "WARNING: There was no tag-name provided!"
  echo "Script usage is: './build.sh tag-name'"
  echo "Example: './build.sh 1.0.3'"
  exit 0
fi

echo "Getting base image dkbellcom/os2web8:$1"
docker image pull dkbellcom/os2web8:$1

echo "Building os2web-subsites"
docker build ./ --build-arg TAG=$1 -t dkbellcom/os2web-subsites:$1

if [ "$2" = "--push" ]; then
  echo "Docker login to dkbellcom. Type password:"
  read -s DOCKERHUB_PASS
  echo "Authorization..."
  echo $DOCKERHUB_PASS | docker login --username dkbellcom --password-stdin

  if [ $? -eq 0 ]; then
    echo "Pushing image to docker hub ..."
    docker push dkbellcom/os2web-subsites:$1
    docker rmi dkbellcom/os2web-subsites:$1
    echo "Image dkbellcom/os2web-subsites:$1 was remove from this machine"
    echo "Check your image here https://hub.docker.com/r/dkbellcom/os2web-subsites/tags"
  else
    echo "Image is not pushed to docker hub :("
  fi;
fi;
