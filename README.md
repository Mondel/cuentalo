Cuentalo Sitio Web
========================

-- Un día con tiempo haremos el README. --

Pasos para instalar cuentalo:

1. Clonar el repositorio
2. Configurar servidor apache
3. Crear directorios app/cache y app/logs
4. Arreglar permisos de app/logs y app/cache (http://symfony.com/doc/current/book/installation.html)
5. Configurar un app/config/parameters.ini(no versionado) similar al ejemplo app/config/parameters.dist.ini
6. Ejecutar php bin/vendors install
7. Ejecutar php app/console doctrine:database:create
8. Ejectuar php app/console doctrine:schema:create 