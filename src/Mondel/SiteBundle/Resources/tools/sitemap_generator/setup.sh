cd /home/cuentalo/cuentalo.com.uy/src/Mondel/CuentaloBundle/Resources/tools/sitemap_generator; wget --spider --recursive --no-verbose --output-file=wgetlog.txt http://www.cuentalo.com.uy; sed -n "s@.\+ URL:\([^ ]\+\) .\+@\1@p" wgetlog.txt | sed "s@&@\&amp;@" > sedlog.txt; python sitemap_generator.py; rm -rf www.cuentalo.com.uy