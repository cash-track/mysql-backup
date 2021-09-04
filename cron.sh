#!/bin/sh

set -e

export $(cat /app/.env | sed 's/#.*//g' | xargs)

exec "$@"
