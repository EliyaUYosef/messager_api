#!/bin/bash

# Restart Heroku
heroku restart

# Clean Laravel cache and routes
heroku run php artisan cache:clear
heroku run php artisan route:clear

# Drop all MySQL tables
# heroku run php artisan migrate:reset

# Git operations
git add .
git commit -m "Auto deployment: $(date +'%Y-%m-%d %H:%M:%S')"
git push
git push heroku main

# Additional Laravel optimizations
heroku run php artisan clear-compiled
heroku run php artisan optimize

# Set appropriate permissions
heroku run chmod -R 777 storage

# Migrate database
# heroku run php artisan migrate

# Passport keys and client
# heroku run php artisan passport:keys --force
heroku run php artisan passport:keys
heroku run php artisan passport:client --personal