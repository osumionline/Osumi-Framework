Osumi Framework
===============

Osumi Framework es un pequeño framework orientado al modelo MVC (Modelo, Vista, Controlador) para la creación de aplicaciones web, tanto pequeñas como grandes.

## Instalación
El framework se puede instalar en cualquier servidor con Apache2 y PHP 5.3 (o superior). MySQL no es necesario pero en caso de querer usarlo el requisito es que sea de la versión 5.1 o superior.

El `DocumentRoot` de la aplicación debe apuntar a la carpeta `web`

## Estructura
El Framework se compone de varias carpetas organizadas de la siguiente manera:

### config
Carpeta con los ficheros de configuración:

`config.php`: configuración general del sitio (configuración de la base de datos, CSS y JS por defecto...).

`base.json`: archivo que indica que módulos que trae el Framework por defecto se quieren habilitar.

`translations.json`: archivo con textos traducidos a varios idiomas.

`urls.json`: en este archivo se indican las urls del sitio, de modo que al llamar a una url se sepa que modulo y acción se debe ejecutar.

### controllers
Carpeta con la lógica de la aplicación. Corresponde a Controlador del MVC. Cada archivo en esta carpeta corresponde a un `módulo` que tiene dentro varias funciones llamadas `acciones`.

### moel
Esta carpeta tiene cuatro carpetas dentro:

`app`: carpeta con clases por cada tabla de la base de datos, corresponde al Modelo.

`base`: carpeta con las clases internas del Framework.

`routing`: sistema de enrutado de Symfony para las "urls bonitas".

`static`: carpeta con las clases estáticas que contienen funciones que se usarán a lo largo del sitio.

### log
Carpeta donde se almacenan los logs creados por el modo debug.

### task
Carpeta para archivos script o tareas que no se ejecutan desde el navegador, para tareas internas de mantenimiento por ejemplo.

### templates
Carpeta para las plantillas de las páginas que componen el sitio. Se distribuye en varias carpetas:

`layout`: carpeta con las plantillas estructurales de la aplicación.

`partials`: carpeta con las plantillas de elementos reutilizables (pueden tener lógica). Se organizan en carpetas según los nombres de los módulos que las usan.

`carpetas para los módulos`: varias carpetas, cada una con el nombre de un módulo. Dentro de estas carpetas habrá un archivo php por cada acción del módulo.

### web
Carpeta pública o visible. Contiene el archivo index.php, archivo a través del que se canaliza toda la lógica.

En esta carpeta se encuentran las carpetas para los archivos CSS, JavaScript o imágenes.