#!/bin/bash

# Git operations
git status
git add .
git commit -m "Auto deployment: $(date +'%Y-%m-%d %H:%M:%S')"
git push heroku main
# git push

# Restart Heroku
# heroku restart

# Drop all MySQL tables
# heroku run php artisan migrate:reset

# Additional Laravel optimizations
heroku run php artisan clear-compiled
heroku run php artisan optimize

# Set appropriate permissions
heroku run chmod -R 777 storage

# Migrate database
heroku run php artisan migrate

# Clean Laravel cache and routes
heroku run php artisan cache:clear
heroku run php artisan route:clear
heroku run php artisan config:cache

# Passport keys and client
heroku run php artisan passport:keys --force
# heroku run php artisan passport:keys
heroku run php artisan passport:client --personal