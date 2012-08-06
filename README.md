Cuentalo Sitio Web
========================

-- Un día con tiempo haremos el README. --

1. Como instalar cuentalo ?

* Clonar el repositorio
* Setear los permisos de los directorios "app/logs" y "app/cache" correctamente (http://symfony.com/doc/current/book/installation.html)
* Descargar "Composer" en el directorio clonado
* Instalar las dependencias (gracias a composer es php composer.phar install)
* Comprobar que tu configuracion de php es correcta, "php app/check.php"
* Copiar y renombrar el archivo parameters.dist.yml a parameters.yml, y completarlo con los datos correspondientes de tu base de datos
* Ejecutar los siguientes comandos:
- php app/console doctrine:database:create
- php app/console doctrine:schema:create
- php app/console assets:install web
- php app/console assetic:dump
* Configurar apache httdconf de la siguiente manera:

<VirtualHost *:80>
    DocumentRoot   "/directorio/de/cuentalo/web"
    DirectoryIndex app_dev.php
    ServerName     cuentalo.local

    <Directory "/directorio/de/cuentalo/web">
        AllowOverride All
        Allow from All
    </Directory>
</VirtualHost>

* Abrir la url de tu sitio web configurado en apache
