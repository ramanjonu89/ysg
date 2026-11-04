https://medium.com/itnext/the-ultimate-guide-to-elasticsearch-in-laravel-application-ee636b79419c


php artisan elastic:migrate
php artisan elastic:migrate:rollback
php artisan scout:import "App\Models\Product"
php artisan import:elasticsearch --chunk=100

chmod -R 777 storage
php artisan migrate
php artisan passport:install
php artisan key:generate

#Delete txt file on products directory
find . -name "*.txt" -type f -delete

#generate Swagger Mobile API:
php artisan l5-swagger:generate

php artisan queue:restart