### Vider les donnees metiers 
php artisan db:reset-project


### Reensemencer les roles & utilisateurs 
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder
