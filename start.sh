#!/bin/bash

# shellcheck disable=SC2164
cd ./mercure/
export MERCURE_PUBLISHER_JWT_KEY="6E9BD5E85242B4A35BE4431513DAD"
export MERCURE_SUBSCRIBER_JWT_KEY="6E9BD5E85242B4A35BE4431513DAD"
export ALLOW_ANONYMOUS="1"
export JWT_KEY="azeaze"
export ADDR="localhost:3000"
export MERCURE_EXTRA_DIRECTIVES="cors_origins http://localhost:8000 http://127.0.0.1:8000"
export CORS_ALLOWED_ORIGINS="http://localhost:8000 http://127.0.0.1:8000"
wine mercure.exe run --config Caddyfile.dev