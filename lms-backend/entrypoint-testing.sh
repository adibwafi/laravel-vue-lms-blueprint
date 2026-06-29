#!/bin/bash

# Clear any cached configurations or routes
php artisan optimize:clear

# Generate a new application key for testing
php artisan key:generate

# Run database migrations and seeders (optional, adjust as needed)
php artisan migrate:fresh --seed 

# Execute your test suite
php artisan test
