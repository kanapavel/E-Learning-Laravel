#!/bin/bash

# Exécuter les migrations
php artisan migrate --force

# Démarrer le serveur Laravel
php artisan serve --host=0.0.0.0 --port=$PORT