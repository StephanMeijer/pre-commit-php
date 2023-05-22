#!/usr/bin/env bash

set -e

if [ "$#" -eq 0 ]; then
    echo "Please provide a Composer command to run as an argument."
    exit 1
fi

composer_cmd="$1"
shift

echo "Running 'composer $composer_cmd'"

npm "$composer_cmd" "$@"
