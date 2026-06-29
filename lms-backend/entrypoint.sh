#!/bin/bash
composer install
# Run Laravel Artisan commands
php artisan optimize:clear
php artisan key:generate 
php artisan migrate --seed 

# Check if superadmin exists
if ! php artisan user:exists --role=superadmin; then
    php artisan create:superadmin
fi

# Start the Laravel development server
php artisan serve --host=0.0.0.0 --port=80
