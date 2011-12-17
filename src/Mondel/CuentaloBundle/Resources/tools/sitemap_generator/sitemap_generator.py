# -*- coding: utf-8 -*-

'''
Created on 17/12/2011

@author: nicolas
'''

import urllib2
import re
import xml.dom.minidom


base = 'http://www.cuentalo.com.uy'

url_sitemap = [base + '/contacto']
url_no_sitemap = []

f = file('/home/cuentalo/cuentalo.com.uy/src/Mondel/CuentaloBundle/Resources/tools/sitemap_generator/sedlog.txt', 'r')
data = [l.strip() for l in f.readlines()]

for d in data:
    if 'robots.txt' in d:
        url_no_sitemap.append(d)
        robots_html = urllib2.urlopen(d).read()
        patt = 'Disallow:\s*(.*)'
        for i in re.findall(patt, robots_html):
            url_no_sitemap.append(base + i)

for d in data:
    agregar = False
    if d not in url_no_sitemap:
        for u in url_no_sitemap:
            if u not in d and d not in url_sitemap:
                agregar = True
            else:
                agregar = False
                break
    if agregar:
        url_sitemap.append(d)

url_sitemap_aux = []
idmax = 0

for url in url_sitemap:
    pattern = 'cuentalo.com.uy/contenido/(\d+)'
    m = re.search(pattern, url)
    if m is not None:
        if int(m.group(1)) > idmax:
            idmax = int(m.group(1))
        
for i in range(idmax):
    url_aux = 'http://www.cuentalo.com.uy/contenido/%s' % i
    try:
        resp = urllib2.urlopen(url_aux)
        if url_aux not in url_sitemap:
            url_sitemap.append(url_aux)
    except:
        pass

print '%s urls encontradas' % len(url_sitemap)

document = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'

for url in url_sitemap:
    document += '<url><loc>%s</loc><changefreq>hourly</changefreq><priority>0.8</priority></url>' % url 

document += '</urlset>'

try:
    dom = xml.dom.minidom.parseString(document)
    sitemap_file = file('/home/cuentalo/cuentalo.com.uy/web/sitemap.xml', 'w')
    sitemap_file.write(document);
    sitemap_file.close()
except:
    pass
