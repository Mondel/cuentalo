Cuentalo Sitio Web
========================

-- Un día con tiempo haremos el README. --

Pasos para instalar cuentalo:

1. Clonar el repositorio
2. Configurar servidor apache
3. Arreglar permisos de app/logs y app/cache (http://symfony.com/doc/current/book/installation.html)
4. Configurar un app/parameters.ini(no versionado) similar al ejemplo app/parameters.dist.ini
5. Ejecutar php app/console doctrine:database:create
6. Ejectuar php app/console doctrine:schema:create
7. 