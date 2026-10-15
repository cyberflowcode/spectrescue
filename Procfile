web: php artisan storage:link && (php artisan migrate --force && php artisan db:seed --force || echo 'Database migration pending') && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
