#!/usr/bin/env bash

npm install
npm run build

php artisan serve --host=0.0.0.0 --port=80