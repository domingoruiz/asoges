# Asoges

Sistema integral para la gestión de asociaciones

## Entorno de desarrollo
- Apple OS X 26.0 (Apple Silicon)
- Docker Compose [docker-lamp](https://github.com/domingoruiz/docker-lamp)
- PHP 8.4.8
- Mysql 8.0.42
- Laravel 12.19.3

## Comandos
Actualizar Composer 
- composer update

Ejecuta todas las migraciones y seeders pendientes
- php artisan migrate; php artisan db:seed

Ejecuta todas las migraciones y seeders desde cero
- php artisan migrate:fresh --seed 

Limpiar cache Laravel
- php artisan optimize:clear

## Desarrollo
Aplicación desarrollada por Domingo Ruiz Arroyo <ordenadordomi@gmail.com>