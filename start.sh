#!/usr/bin/env bash

docker-compose up -d

if [ ! -d "vendor" ]; then
  echo "~> installing dependencies"
  docker-compose run testit-nginx composer install
  echo "~> dependencies installed"
fi

echo "~> starting tests"