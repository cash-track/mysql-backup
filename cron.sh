#!/bin/sh

set -e

export $(cat /app/.env | xargs)

exec "$@"
