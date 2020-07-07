CHANGELOG
=========

## `6.0.1` (07/07/2020)

Corrección al crear una nueva acción. Al realizar la comprobación para ver si una acción ya existía previamente, si había una acción que tuviese un nombre que empezase igual fallaba.

Por ejemplo, al intentar crear la acción `sync`, si ya existía una acción llamada `syncStock` fallaba por que la acción existente empieza igual que la nueva que se quiere crear.

## `6.0.0` (03/07/2020)

¡Nueva versión 6.0!

Esta es una nueva versión mayor por que introduce cambios que rompen la estructura  y la filosofía de URLs actual.

Esta nueva versión tiene cuatro puntos principales:

* Nuevo sistema de URLs
* Menos archivos de configuración
* Configuración de los plugins
* Nueva tarea `add`

### Nuevo sistema de URLs

Hasta ahora la configuración de las URLs se basaba en el archivo `urls.json`. Este archivo contenía una relación de URLs y los módulos/acciones que se debían ejecutar. En este archivo también se indicaba si una URL debía ejecutar un filtro o el tipo de retorno que debía devolver.

A partir de ahora, este archivo desaparece y son las propias acciones, en su documentación phpDoc, las que definen toda esta información. Por ejemplo:

```php
/**
 * API para la aplicación prueba
 *
 * @prefix /api
 * @type json
 */
class api extends OModule {
	/**
	 * Función para obtener un usuario
	 *
	 * @url /getUser
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	 function getUser(ORequest $req): void { ... }

	 /**
 	 * Función para obtener la lista completa de usuarios, pero en XML
 	 *
 	 * @url /getUserList
	 * @type xml
	 * @filter userFilter
 	 * @param ORequest $req Request object with method, headers, parameters and filters used
 	 * @return void
 	 */
 	 function getUserList(ORequest $req): void { ... }
}
```

### Menos archivos de configuración

Con el nuevo sistema de URLs, el archivo de configuración `urls.json` desaparece. A su vez, ahora se prescinde del archivo `plugins.json` que se creaba y mantenía automáticamente al instalar plugins. Ahora el propio Framework lee la carpeta donde están instalados los plugins y ya no es necesario este archivo.

El archivo `translations.json` se incluía en cada instalación, a pesar de ser solo necesario en el caso de que se instalase el plugin `OTranslate`.

De este modo se ha pasado de cuatro archivos de configuración a uno solo: `config.json`

### Configuración de los plugins

El plugin `OEmailSMTP` se configuraba mediante valores en el archivo `config.json`, y estos valores eran almacenados como parte de la configuración del Framework. A partir de esta versión, el Framework prescinde de todo tipo de configuración o valores externos y la configuración de los plugins se realiza con el archivo `config.json` solo en caso de que el plugin lo requiera.

Por ejemplo, al instalar el plugin `OEmailSMTP` (llamado `email_smtp` en el repositorio de Plugins), automáticamente se creará un apartado llamado `plugins` en el archivo `config.json`, que a su vez contendrá un apartado llamado `email_smtp` con todos los valores de configuración del plugin.

En caso de desinstalar el plugin, este apartado de configuración se eliminará automáticamente.

De este modo el Framework solo contendrá la configuración del propio Framework y el resto serán valores extra auxiliares.

### Nueva tarea `add`

La nueva tarea `add` sirve para crear nuevos módulos, acciones, servicios o tareas. En lugar de escribir manualmente nuevos archivos `php`, usando esta nueva tarea el trabajo se reduce a un solo comando, reduciendo el trabajo y la posibilidad de introducir errores.

**Nuevo módulo**

Comando: `php ofw.php add module (nombre del módulo)`

Este comando crea un nuevo módulo en la carpeta `modules`, su archivo php y su carpeta para `templates`. Se comprueba que no exista un módulo con el nombre indicado antes de crear el nuevo.

**Nueva acción**

Comando: `php ofw.php add action (módulo) (nombre de la acción) (URL)`

Opcionalmente, también se puede indicar un último parámetro indicando el tipo (por defecto es `html`).

Este comando crea una nueva acción en el módulo indicado. Crea su función, su configuración en su respectivo apartado phpDoc y el archivo `template` necesario. Se comprueba que el módulo indicado exista y que la acción indicada no exista.

**Nuevo servicio**

Comando: `php ofw.php add service (nombre del servicio)`

Este comando crea un nuevo servicio vacío que puede ser usado en cualquier módulo. Crea su archivo php en la carpeta `services`.

**Nueva tarea**

Comando: `php ofw.php add task (nombre de la tarea)`

Este comando crea una nueva tarea vacía que puede ser usada tanto en las acciones como desde el CLI. Crea su archivo php en la carpeta `task`.


*Esta actualización tiene una tarea `postinstall` que actualiza automáticamente todas las acciones para que usen el nuevo sistema de URLs.*


## `5.8.0` (16/06/2020)

Reestructuración en carpetas de módulos/acciones para unificar sintaxis. Hasta ahora, el archivo `urls.json` hacía referencia a módulos y acciones, al crear nuevas funciones se hablaba de módulos y acciones... pero luego el código se guardaba en la carpeta `controller` y las clases de esa carpeta heredaban la clase `OController`.

En esta nueva versión desaparece por completo el termino `controller` para pasar a hablar unicamente de `modulos` y `acciones`. Hasta ahora todo el código de la aplicación se encontraba en varios archivos php en la carpeta `app/controller` y sus `templates` asociados se encontraban en una carpeta con el nombre de ese controller en la carpeta `app/template`.

Esto último creaba otro problema a su vez, ya que al estar los `template` de los módulos en la carpeta `app/template`, hacía que no se pudiese crear módulos con nombre `layout` o `partials` ya que habría colisión con las carpetas propias del Framework.

Ahora los módulos están más "autocontenidos", todos los módulos se guardan en la carpeta `app/module` y dentro de esta carpeta se creará una carpeta para cada módulo. Dentro de la carpeta de cada módulo hay un archivo php con la clase del módulo y una carpeta `template` con los `templates` de las acciones de ese módulo. Además los archivos de los `templates` tendrán como extensión el tipo de archivo que son, no todos php como hasta ahora.

Por ejemplo, antes la estructura sería esta:

* app
  * controller
    * api.php
	* home.php
  * template
    * api
	  * getUser.php
	* home
	  * start.php
	  * user.php

Y la nueva estructura sería esta:

* app
  * module
    * api
	  * api.php
	  * template
	    * getUser.json
    * home
	  * home.php
	  * template
	    * start.html
		* user.html

Esta actualización tiene una tarea `postinstall` que actualiza automáticamente la estructura de toda la aplicación.

## `5.7.1` (16/06/2020)

Corrección de estilo en nueva template de la `task` `plugins/update`.

## `5.7.0` (16/06/2020)

Nuevas funciones `getTemplate` y `getPartial` en `OTools`. Estas funciones antes eran parte de `OTemplate`, pero al sacarlas a `OTools` se pueden usar desde cualquier parte de la aplicación.

Usando estas nuevas funciones he remodelado las `task`. Ahora todas las task heredan de la clase `OTask` y así tienen acceso a varias funciones:

* `getColors`: si la `task` es llamada desde el CLI `ofw.php`, tendrá acceso a este método que le permite colorear el resultado de salida usando la clase `OColors`.
* `getConfig`: acceso a la configuración global.
* `getLog`: acceso a un objeto `OLog` generico.

Esta actualización tiene una tarea `postinstall` que actualiza automáticamente todas las `task` que haya en la aplicación.

## `5.6.5` (08/06/2020)

Corrección en `OModel`: en la función `generate` había un error al generar el SQL de modelo en caso de que una columna no tuviese comentario.

## `5.6.4` (29/05/2020)

Corrección en `OModel`: en la función `getModel` había un caso en el que devolvía un tipo incorrecto.

## `5.6.3` (28/05/2020)

Corrección en `OTools` al actualizar la cache de URLS. Si se hacía automáticamente en una llamada web, la clase `OColors` no estaba incluida y fallaba.

## `5.6.2` (05/05/2020)

Corrección en `OModel` al actualizar un registro.

## `5.6.1` (29/04/2020)

Correcciones.

Corrección al crear una nueva acción, seguía poniendo que las acciones recibían el array `$req` en lugar de la clase `ORequest`. También he añadido el PHPdoc asociado a este parámetro.

Corrección en `OCore` para que se puedan usar los `services` desde las `tasks`.

## `5.6.0` (28/04/2020)

Nueva clase `ORequest`. Este es un `breaking change`.

Hasta ahora los controllers recibían un array con varios campos como `method`, `params`, `headers` o `filters`. A partir de esta versión, los controllers recibirán un objeto de tipo `ORequest` que contiene toda esta información y varios métodos que permiten obtener la información concreta que se quiera.

Estos son los métodos que tiene la clase `ORequest`:

* `getMethod()`: devuelve el tipo de método empleado al hacer la llamada (GET/POST...). Devuelve el método en minúsculas.
* `getParams()`: devuelve el array de parámetros que se han pasado al hacer la llamada. Es el equivalente a obtener los parámetros del array que se pasaba anteriormente.
* `getParam($key, $default=null)`: anteriormente había que usar el método `OTools::getParam` para filtrar el parámetro que se quería obtener de la lista completa de parámetros. Ahora esa función se ha integrado dentro de la clase. Como parámetros recibe `$key` con la clave del parámetro a buscar y `$default` un valor por defecto en caso de que no exista la clave solicitada.
* `getParamString($key, $default=null)`: como la anterior, pero este método fuerza a que el resultado obtenido sea una cadena de texto.
* `getParamInt($key, $default=null)`: como `getParam`, pero fuerza a que el resultado sea un int.
* `getParamFloat($key, $default=null)`: como `getParam`, pero fuerza a que el resultado sea un float.
* `getParamBool($key, $default=null)`: como `getParam`, pero fuerza a que el resultado sea un booleano.
* `getFilters()`: devuelve un array asociativo con todos los valores devueltos por los filtros.
* `getFilter($key)`: devuelve un array con los valores devueltos por el filtro indicado.

Al haber añadido estos métodos a esta nueva clase, los anteriores `OTools::getParam` y `OTools::getParamList` se han eliminado y puede causar que las aplicaciones fallen. Es un cambio importante que va a obligar a repasar todas las aplicaciones, pero merecerá en cuanto a limpieza, validaciones y casteos.

## `5.5.2` (27/04/2020)

Corrección en parámetros de entrada. Las acciones de los métodos reciben un array `$req` con los parámetros enviados y las cabeceras de la llamada. He añadido (por que en algún momento lo quite...) el tipo de método de llamada (GET/POST...) en una variable `method` de ese array.

Por otra parte, al haber añadido tipado fuerte y comprobaciones estrictas, el código de ejemplo fallaba por una función que recibía un string en vez de un int.

## `5.5.1` (27/04/2020)

Corrección de tipado en `updateUrls`: las nuevas acciones creadas tenían tipado de retorno, pero el objeto `$req` que reciben no tenía.

## `5.5.0` (26/04/2020)

Renombro tarea `composer` a `extractor` ya que Composer es una herramienta muy conocida de PHP y no quiero que sean confundidas.

## `5.4.1` (26/04/2020)

Corrección menor en tarea `plugins`: faltaba una frase por localizar.

## `5.4.0` (16/04/2020)

Con los cambios de la última versión donde añadí el tipado fuerte a todo el Framework, ahora al utilizar la tarea `updateUrls` para crear nuevos módulos y acciones, se les añadirá el tipado estricto y el retorno por defecto.

## `5.3.4` (15/04/2020)

Corrección en `OConfig`. Nueva corrección por problemas de tipado. Esta vez en configuración de SMTP.

## `5.3.3` (15/04/2020)

Corrección en `ODB`. Nueva corrección por problemas de tipado.

## `5.3.2` (15/04/2020)

Corrección en `OTemplate`. Nueva corrección por problemas de tipado.

## `5.3.1` (15/04/2020)

Corrección en `OConfig`. Gracias al tipado fuerte aparecen errores que antes no se veían.

## `5.3.0` (15/04/2020)

¡Tipado fuerte en todo el Framework!

PHP 7.4 ha introducido como novedad la posibilidad de añadir tipado a las variables de las clases y como ya se podía añadir al retorno de las funciones, he actualizado todas las clases que componen el Framework añadiéndoles tipado fuerte para hacer el framework mucho más robusto. He añadido en todos los archivos del Framework el llamado tipado estricto para forzar a que los datos tengan que ser correctos, de lo contrario la aplicación falla lanzando excepciones.

Al hacer esto he hecho que sea obligatorio usar por lo menos la versión 7.4.0 de PHP para ejecutar el Framework y han aflorado numerosos errores que se han corregido.

Los datos de ejemplo también han sido actualizados, pero no se descargarán al actualizar instalaciones ya existentes.

La parte de la app que no corresponde al Framework no tiene por qué cumplir el tipado estricto, pero se aconseja para así mantener un mismo estilo.

## `5.2.1` (06/04/2020)

Corrección en `OService`. He añadido métodos `getConfig` y `getLog` a la clase `OService` para igualar su funcionalidad a la de `OController`. Ahora en ambas clases se puede acceder de igual manera a la configuración o al objeto de `logger` genérico de la clase.

## `5.2.0` (06/04/2020)

Cambios en `services`: hasta ahora se creaba una relación circular en la que los `controllers` instanciaban `services` y estos a su vez tenían una variable con la clase que les había instanciado de modo que se creaba una relación circular `controller -> service -> controller -> service...`

Esto servía para que los servicios pudiesen acceder a la configuración, al acceso a la base de datos del `controller` o a otros `services`.

Ahora, los `services` ya no cuentan con ese acceso de modo que se les ha dotado de accesos específicos a la configuración o su propio `logger`.

En caso de necesitar acceder a la base de datos, tendrán que hacerlo instanciando sus propios `ODB`. Y si necesitan acceder a otros servicios podrán hacerlo del mismo modo que hacen los controladores, instanciandolos a una variable y tratarlos como un objeto.

Este es un `breaking change` ya que habrá que adaptar los servicios y controladores ya en uso.

NOTA: esta es la primera versión en la que se va a probar un script `postinstall`.

## `5.1.0` (03/04/2020)

Mejoras en `OModel`: si se le llamaba a `save` en un objeto de modelo y este no contenía ningún cambio, la consulta `UPDATE` resultante estaba mal formada. Se ha corregido de modo que si no hay ningún campo que actualizar simplemente devuelve un valor `false`.

Mejoras en logs internos: ahora la clase `OLog` acepta un string opcional en su constructor que indicaría el nombre de la clase desde donde se está ejecutando. Las clases internas del Framework ya uilizan esta nueva mejora de modo que si se indica como `log_level` el valor `ALL`, en los logs resultantes ya se verá el nombre de la clase desde donde se guarda la traza.

## `5.0.4` (24/03/2020)

Corrección tipográfica en `Ocache`: faltaba un carácter ">".

## `5.0.3` (24/03/2020)

Corrección en `OUpdate`: intentaba borrar el backup de un archivo nuevo, cosa que no existe.

## `5.0.2` (24/03/2020)

Corrección en `OModel` (no cogía bien las Primary Keys al actualizar un registro) y en `OUrl` (no cogía bien la configuración al usarse de manera estática).

## `5.0.1` (23/03/2020)

Corrección en filtros (y otro breaking change):

A partir de esta versión, los `controllers` reciben un parámetro `$req` que es un array con dos o tres campos:

* `params`: array con los parámetros recibidos (ya sea por la URL, por GET, POST, FILES o Body Content).
* `headers`: array con las cabeceras enviadas por el usuario en la llamada.
* Filtro: en caso de que se haya definido un filtro para una URL, habrá un tercer campo con el nombre del filtro y los datos que este mande. Por ejemplo, si una URL tiene un filtro llamado `loginFilter`, el array `$req` contendrá un campo llamado `loginFilter` con los datos que este haya devuelto.

## `5.0.0` (22/03/2020)

¡Nueva versión 5.0!

Esta es una nueva revisión mayor del Framework ya que incluye muchos cambios que rompen las aplicaciones anteriores que tendrán que ser adaptadas. Todo el código ha sido revisado, se ha incluido phpDoc en todas las clases y funciones para ayudar en la programación y se han localizado los mensajes (inglés y español, con más idiomas en próximas actualizaciones).

#### Novedades

* Clase `OCore`: agrupa toda la funcionalidad de carga e inicio de la aplicación. También contiene las variables `dbContainer` (donde se guardan todas las conexiones abiertas con bases de datos), `cacheContainer` (objeto donde se guardan todos los archivos cargados como cache) y las variables estáticas con las que definir la base de datos.
* Clase `OModel`: la mayor parte de la anterior clase `OBase` se ha renombrado a esta nueva clase y se han efectuado refactorizaciones y limpieza.
* Clase `OUpdate`: agrupa las funciones necesarias para actualizar el Framework a futuras versiones. Anteriormente, las tareas `updateCheck` y `update` se encargaban de las actualizaciones y esto hacía que hubiese mucho código duplicado. Ahora se ha agrupado todo el código en esta clase nueva y se ha añadido abundantes controles de errores.
* Locales: en la carpeta `ofw/locale` ahora se incluyen dos archivos (`es.php` -Español- y `en.php` -Inglés-) con todos los mensajes que se muestran mediante el CLI. El idioma del Framework se define mediante la variable `lang` en el archivo `config.json`.
* `phpDoc`: todas las clases y funciones que componen el Framework han sido documentadas para facilitar su uso desde IDEs. Todas las funciones tienen su descripción, parámetros de entrada (tipo de dato y una explicación) y datos de salida (tipo de dato y explicación).
* Nueva página de error: en las versiones anteriores, las páginas de error para 403 o 404 simplemente mostraban un mensaje. Ahora se ha creado el archivo `error.php` con un toque de diseño para mostrar algo más... bonito :) Las páginas de error siguen siendo personalizables.
* Las tareas `updateCheck` y `update` han tenido un lavado de cara (y de funcionalidades) y a partir de esta versión, se podrá incluir unos scripts llamados `postinstall` que realicen cambios en la aplicación (por ejemplo clases que antes heredaban de una clase que ya no va a existir, el script podrá actualizar todas las clases para que se realice este cambio automáticamente).

#### Refactorizaciones

* La clase `OBase` se ha dividido en varias partes pero el grueso ahora es la nueva clase `OModel`
* Las clases `ODB` y `ODBContainer` estaban cada una en un archivo, pero siempre se usan juntas, de modo que se han unido en un solo archivo.
* Las clases `OCache` y `OCacheContainer` estaban cada una en un archivo, pero siempre se usan juntas, de modo que se han unido en un solo archivo.
* Limpieza de código: había muchos lugares en los que se creaban variables de un solo uso, llamadas a funciones que devolvían un solo valor...

#### Breaking changes

* La función `OBase::getCache` (ahora llamada `OTools::getCache`) antes devolvía el contenido en JSON, ahora devuelve un objeto `OCache`.
* La función `OBase::bbcode` (ahora llamada `OTools::bbcode`) ya no tiene las etiquetas `[g]` y `[quote]` por que devolvían un HTML con unos estilos que había que definir a mano.
* Las funciones `OBase::doPostRequest` y `OBase::doDeleteRequest` ya no existen. Ahora hay una función genérica para hacer llamadas mediante CURL llamada `OTools::curlRequest` que acepta como parámetro el tipo de método con el que hacer la llamada (get / post / delete).
* La clase `OTemplate` ha perdido las funciones para añadir archivos CSS con `media queries`. En su lugar hay que añadir archivos CSS que contengan en su interior las `media queries` que se quieran usar.
* Se ha eliminado el soporte para `packages`. Solo había creado uno (un panel de admin) y estaba muy desactualizado.
* Se ha eliminado el soporte para `folder`. Antes se permitía que una aplicación estuviese en una subcarpeta del `DocumentRoot`, pero ahora es obligatorio que el `DocumentRoot` apunte a la carpeta `web`.
* Se ha refactorizado el contenido de la clase `OBase` a la nueva clase `OModel`, de modo que todas las clases de modelo que antes heredaban de `OBase` tendrán que ser modificadas para que ahora hereden esta nueva clase `OModel`.

## `4.20.0` (09/03/2020)

Refactorización y limpieza. Cambio tabulaciones de todo el Framework a tabuladores, había tabulaciones con espacios y con tabuladores y todas han sido igualadas.

Empiezo limpieza pensando en diseño para versión 5 :)


## `4.19.0` (08/03/2020)

Corrección en `ODB` al realizar transacciones. Las transacciones se realizan contra la conexión y se estaban realizando contra una query inexistente.

## `4.18.1` (10/02/2020)

Corrección de error tipográfico en `OCacheContainer`.

## `4.18.0` (10/02/2020)

Nueva clase `OCacheContainer`. Al usar el método `getCache` de la clase `Base` se lee un archivo de la carpeta `cache`, por ejemplo un archivo de configuración, pero si, por ejemplo, multiples instancias de una misma clase tienen que leer algún archivo de `cache`, se hace un acceso a disco cada vez.

Con esta clase nueva, al acceder a un archivo de la carpeta `cache`, el contenido de este archivo se guarda en memoria en esta nueva clase `OCacheContainer`, que no es más que un contenedor con métodos para leer y guardar estos valores.


## `4.17.1` (23/01/2020)

Corrección en la clase ODB. Había un error al lanzar la nueva excepción cuando ocurre un error de SQL.

## `4.17.0` (23/01/2020)

Corrección al crear nuevos objetos de modelo, no tomaba bien los valores por defecto. También se ha añadido una excepción que se lanza al ejecutar una SQL que contenga errores o que produzca un error.

## `4.16.0` (23/12/2019)

¡Plugins!

Hasta ahora cada funcionalidad nueva creada para el Framework era incorporada como una nueva clase que se cargaba junto con el resto del Framework. Para indicar que clases se cargaban, en el archivo `config.json` había un apartado llamado `base_modules`en el que mediante valores `true/false` se indicaba si la funcionalidad se debía cargar.

Este apartado ahora desaparece y aparece el concepto de `Plugins`. Todas estas clases opcionales se han borrado de la instalación por defecto del Framework y están disponibles en un nuevo repositorio:

[Osumi Framework Plugins](https://github.com/igorosabel/Osumi-Plugins)

Para utilizar estos plugins se ha creado una nueva tarea que se puede usar desde el CLI:

Para listar los plugins disponibles hay que ejecutar el siguiente comando:

`php ofw.php plugins`

Esto muestra un listado con los plugins disponibles, su versión y una breve descripción.

Para listar los plugins instalados hay que ejecutar el siguiente comando:

`php ofw.php plugins list`

Esto muestra un listado con los plugins actualmente instalados, su versión y una breve descripción.

Para instalar un nuevo plugin hay que ejecutar el siguiente comando:

`php ofw.php plugins install (nombre)`

Por ejemplo para instalar el plugin para realizar envíos de emails:

`php ofw.php plugins install email`

Esto descarga la última versión del plugin desde el repositorio, crea los archivos necesarios y actualiza el nuevo archivo de configuración `plugins.json`. Este archivo no se debe modificar manualmente.

Para comprobar si existen actualizaciones de los plugins instalados hay que ejecutar el siguiente comando:

`php ofw.php plugins updateCheck`

Esto lista los plugins instalados, muestra la versión instalada y la versión actual del repositorio. En caso de haber alguna actualización muestra un aviso.

Para actualizar los plugins instalados hay que ejecutar el siguiente comando:

`php ofw.php plugins update`

Este comando recorre los plugins instalados y en caso de haber alguna actualización descarga los archivos necesarios y actualiza el número de versión.

Este es un cambio que puede romper las aplicaciones. Si una aplicación utilizaba cualquiera de estas clases, después de actualizar el Framework a la última versión será necesario instalar los plugins correspondientes.

## `4.15.0` (24/10/2019)

Cambio en collate por defecto a `utf8mb4_unicode_ci` y charset por defecto a `utf8mb4`.

A partir de esta versión el `charset` por defecto a la hora de hacer una conexión a la base de datos cambia de `utf8` a `utf8mb4` y el `collate` de los campos de texto cambia de `utf8_general_ci` a `utf8mb4_unicode_ci`.

Los campos con `utf8` guardan 3 bytes de información por carácter y los emojis son caracteres Unicode de 4 bytes, por lo que daba un error al guardar campos de texto que tuviesen este tipo de símbolos 😎 y solo guardaba algo como `????`.

Aun así, estos valores son personalizables mediante el archivo `config.json`:

```json
...
  "db": {
    "host": "localhost",
    "user": "user",
    "pass": "password",
    "name": "db_name",
    "charset": "utf8mb4",
    "collate": "utf8mb4_unicode_ci"
 },
...
```

Los proyectos que actualicen a esta versión deberán actualizar las tablas de la base de datos a este nuevo tipo de `collate` o cambiar el archivo de configuración para indicar el tipo de `charset` y `collate` apropiados.

## `4.14.0` (24/10/2019)

Mejora en `OBase`: la clase `OBase` tenía una variable llamada `$default_model` con los valores por defecto que podían tener los distintos tipos de campos. Esta variable se ha movido a la clase estática `Base` de modo que ya no se incluye en cada variable de modelo que se use.

Esta variable contiene muchos campos y al realizar un `var_dump` ensuciaba mucho la salida y dificultaba la legibilidad dificultando la depuración. Además del consumo de memoria innecesario al ser cargada en cada variable que se use. Ahora al estar en la clase general `Base` solo se incluye una vez y ayudará en la depuración de errores.

## `4.13.0` (22/10/2019)

Corrección por dependencia de `OColors`. El archivo `base.php` utiliza en una función una llamada a `OColors` y por defecto no se incluía, pero en la última versión de PHP comprueba todas las referencias y producía un error.

## `4.12.0` (26/09/2019)

Mejora en la carga de `base_modules`. Hasta ahora, para cargar uno de los módulos que incorpora el framework había que incluir en el archivo `config.json` la lista entera de módulos y marcar con `true` cual se quería añadir.

```php
  "base_modules": {
    "browser": false,
    "email": true,
    "email_smtp": false,
    "ftp": false,
    "image": false,
    "pdf": false,
    "translate": false,
    "crypt": false,
    "file": false
  },
  ...
```

Con la mejora introducida en esta versión solo será necesario incluir en el archivo `config.json` aquellos módulos que se quieren usar.

```php
  "base_modules": {
    "email": true
  },
  ...
```

## `4.11.3` (14/09/2019)

Corrección en el método `Base::slugify`. Si la cadena de texto introducida tenía un carácter "¿" fallaba por que no estaba contemplado.

## `4.11.2` (18/08/2019)

Corrección al generar el archivo SQL del modelo. En los campos de tipo `booleano`, si el valor por defecto era `false` lo dejaba en blanco en lugar de poner un `0`.

## `4.11.1` (01/07/2019)

Corrección en `OFile`. Este archivo contiene la lista de carpetas y archivos que conforman el Framework y no estaba actualizado, en la lista no estaban la nueva carpeta `ofw/export`, había que quitar la carpeta `ofw/sql` y faltaban las dos task nuevas (`backupAll` y `backupDB`).

Corrección al actualizar para que compruebe y cree las carpetas apropiadas. Si se añade un nuevo archivo en una nueva carpeta fallaba al copiar el nuevo archivo a una carpeta que previamente no existía. Ahora primero se comprueba que la carpeta exista y en caso de no existir la crea.

## `4.11.0` (12/06/2019)

Nueva carpeta `ofw/export` para archivos generados por el framework. Ahora al usar la tarea `generateModel` o la tarea `composer`, el resultado se guardará en esta nueva carpeta. Así que he borrado la carpeta `ofw/sql` que ya no se usa.

Esta nueva carpeta también tiene un archivo `.gitignore` para no subir al repositorio los archivos generados.

## `4.10.0` (12/06/2019)

He hecho que las carpetas `app/filter`, `app/model` y `app/service` no sean obligatorias. No es obligatorio que un proyecto use filtros o servicios, y puede haber proyectos que no requieran base de datos. Hasta ahora estas carpetas eran obligatorias (aunque estuviesen vacías) por que sino daba un fallo al cargar.

He añadido la carpeta `ofw/tmp`, que era generada dinámicamente en caso de que no existiese y fuese necesaria, pero le he puesto un `.gitignore` para no subir al repositorio posibles valores temporales o de desarrollo.

He añadido otro `.gitignore` en la carpeta `app/cache` para que no se suban los valores cacheados al repositorio, ya que se generan dinámicamente.

## `4.9.0` (12/06/2019)

Nuevas tareas para realizar copias de seguridad:

* `backupDB`: exporta la base de datos mediante el comando `mysqldump` a un archivo en la carpeta `ofw/sql`.
* `backupAll`: esta tarea primero llama a `backupDB` para exportar la base de datos a un archivo y luego llama a la tarea `composer`, para crear un archivo de exportación (que contiene el dump realizado en el primer paso).

## `4.8.1` (27/05/2019)

Corrección en tarea `update`. Al terminar escribía el nuevo número de versión en el archivo `VERSION`, a pesar de que ya no se usa para nada.

## `4.8.0` (27/05/2019)

Actualización de mantenimiento:

* `OTemplate`: Limpieza de código. Cambio sintaxis antigua de `array()` por `[]`.
* `updateUrls`: Mejoro los mensajes mostrados por consola al ejecutar esta tarea (tabulaciones, colores...).
* `update`: Mejora al obtener las actualizaciones. Ahora al realizar una actualización se descarga la versión específica de cada archivo. Hasta ahora siempre se descargaba la última versión del archivo y en el caso de que hubiese varias actualizaciones, cada actualización siempre cogía la misma versión (la master).
* Borro archivos obsoletos `VERSION` y `updates.json`, ya que ahora han sido integrados en `version.json` y ya no se usaban.

## `4.7.6` (23/05/2019)

Otra release por el error de la `4.7.4`, al fallar se dejaba un archivo por actualizar.

## `4.7.5` (23/05/2019)

Corrección para la última release. La ruta de un archivo a actualizar estaba mal. Se ha marcado ese archivo para borrar por si al actualizar dejase algún resto que no debería estar.

## `4.7.4` (23/05/2019)

Corrección para los proyectos sin base de datos. Siempre se intentaba cerrar las conexiones abiertas, a pesar de que ni siquiera haya definida una base de datos.

## `4.7.3` (21/05/2019)

Nueva opción para los archivos ZIP de `OFile`. Al crear un zip a partir de una carpeta, el ZIP contiene primero una carpeta con el nombre de la carpeta origen. Esto ahora se puede cambiar mediante un nuevo parámetro opcional:

```php
$zip_file = new OFile();
$zip_file->zip('/var/www/folder', '/var/www/folder.zip', 'demo');
```

Esto crearía un archivo llamado `folder.zip`, dentro de este habría una carpeta llamada `demo` y dentro todos los archivos de la ruta `/var/www/folder`.

## `4.7.2` (16/05/2019)

Corrección en `OFile`. La lista de archivos del framework no estaba actualizada a esta nueva versión.

## `4.7.1` (16/05/2019)

Corrección en tareas `update` y `updateCheck`.

## `4.7.0` (16/05/2019)

Nueva clase `OFile` para operaciones con archivos. Esta clase ofrece las siguientes funciones:

__copy__: Método estático para copiar archivos. Recibe dos parámetros: origen y destino. Devuelve `true` o `false` como resultado de la operación. Por ejemplo:

```php
OFile::copy('/tmp/file.txt', '/var/www/file.txt');
```

__rename__: Método estático para cambiar de nombre y/o de ubicación a un archivo. Recibe dos parámetros: nombre antiguo y nuevo nombre. Devuelve `true` o `false` como resultado de la operación. Por ejemplo:

```php
OFile::rename('/tmp/file.txt', '/var/www/list.txt');
```

__delete__: Método estático para borrar un archivo. Recibe un parámetro: nombre del archivo a borrar. Devuelve `true` o `false` como resultado de la operación. Por ejemplo:

```php
OFile::delete('/tmp/file.txt');
```

__rrmdir__: Método estático para borrar recursivamente una carpeta con su contenido, todos los archivos y sub-carpetas que contenga. Recibe un parámetro: nombre de la carpeta a borrar. Devuelve `true` o `false` como resultado de la operación. Por ejemplo:

```php
OFile::rrmdir('/var/www/folder');
```

__getOFWFolders__: Método estático que devuelve la lista de carpetas que componen el framework. No recibe ningún parámetro. Por ejemplo:

```php
$folder_list = OFile::getOFWFolders();
```

__getOFWFiles__: Método estático que devuelve la lista de archivos que componen el framework. No recibe ningún parámetro. Por ejemplo:

```php
$file_list = OFile::getOFWFiles();
```

__zip__: Método para crear un archivo ZIP a partir de una carpeta. Se debe crear una variable de esta clase y acepta dos parámetros: ruta de la carpeta a comprimir y ruta/nomre del archivo ZIP que se creará. Por ejemplo:

```php
$zip_file = new OFile();
$zip_file->zip('/var/www/folder', '/var/www/folder.zip');
```

Por otra parte, he combinado el archivo `VERSION` y `updates.json` en un nuevo archivo `version.json`. En esta release todavía se mantienen los archivos antiguos para permitir que proyectos antiguos se puedan actualizar, pero en la próxima release se borrarán. Esto hará que solo los proyectos a partir de la versión 4.7.0 se puedan actualizar.

## `4.6.3` (02/05/2019)

La última release no incluía la corrección completa a la hora de hacer `insert` con valores `null`. Ahora si :)

## `4.6.2` (20/04/2019)

Corrección para `OBase`, había un error al hacer `insert` con valores `null`.

## `4.6.1` (17/04/2019)

Corrección para `OCrypt`, el framework ignoraba el parámetro de configuración y no cargaba la clase.

## `4.6.0` (17/04/2019)

Nueva clase `OCrypt` para cifrar/descifrar cadenas de texto. Esta clase acepta una clave de 32 caracteres como clave de cifrado y ofrece unos métodos `encrypt` y `decrypt` para cifrar y descifrar los datos:

```php
  // Método 1, inicializar sin clave
  $crypt = new OCrypt();
  $key = $crypt->generateKey(); // Devuelve una clave de 32 caracteres aleatoria que luego podrá ser almacenada. La clase se "auto-inicializa" con esta clave al generarla.
  // Método 2, inicializar con clave
  $crypt = new OCrypt('bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');

  // Para cifrar una cadena de texto:
  $cifrado = $crypt->encrypt('abc123');
  // También es posible indicar la clave en el propio momento del cifrado:
  $cifrado = $crypt->encrypt('abc123', 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');
  // El resultado será: K3gzWkxySUd6VkgvQTNJUUtZMjV2UT09Ojpia3sh1zglO3DYodw84855

  // Para descifrar una cadena de texto:
  $descifrado = $crypt->decrypt('K3gzWkxySUd6VkgvQTNJUUtZMjV2UT09Ojpia3sh1zglO3DYodw84855');
  // También es posible indicar la clave en el propio momento del descifrado:
  $descifrado = $crypt->decrypt('K3gzWkxySUd6VkgvQTNJUUtZMjV2UT09Ojpia3sh1zglO3DYodw84855', 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=');
  // El resultado será: abc123
```
El método de cifrado por defecto es `aes-256-cbc` pero se puede cambiar usando el método `setMethod` de la clase:

```php
  $crypt->setMethod('aes-128-ecb')
```
Ya que no todos los proyectos necesitarán esta nueva funcionalidad, he creado una nueva opción en `base_modules` del archivo `config.json` para cargar la clase en el proyecto (por defecto su valor será `false`):

```json
{
  "base_modules": {
    "crypt": true
  }
}
```
Esta nueva clase está basada en el código de [Hoover Web Development](https://bhoover.com/using-php-openssl_encrypt-openssl_decrypt-encrypt-decrypt-data/)

## `4.5.1` (21/03/2019)

Correcciones en los datos de ejemplo para que la aplicación funcione nada más descargarla. Los datos de ejemplo son los mismos que hay en [https://demo.osumi.es](https://demo.osumi.es) .

Para crear un proyecto nuevo es necesario eliminar estos archivos:

* app/cache/*
* app/controller/*
* app/filter/*
* app/model/*
* app/service/*
* app/task/*
* app/template/api
* app/template/home
* app/template/partials/*

El archivo de configuración `app/config/config.json` debe ser adaptado al proyecto nuevo y el archivo `app/template/layout/default.php` también debe ser modificado para el proyecto nuevo.

Se incluye el archivo `ofw/sql/model.sql` con los datos de prueba para la demo.

## `4.5.0` (21/03/2019)

¡Colores! Añado una nueva clase `OColors` que permite mostrar mensajes con diferentes colores en las `task` que se ejecutan como CLI. Los mensajes que se muestran por consola se pueden personalizar con un color de fondo y color de letras.

## `4.4.0` (15/03/2019)

Modifico la clase `OImage` para que ya no dependa de la librería `SimpleImage` adaptando sus funciones. Hasta ahora `OImage` era un wrapper con funciones que fueron usadas para un proyecto concreto.

La clase ahora puede cargar imágenes `jpg`, `png` o `gif` y cambiar su tamaño, escalarlas o convertirlas entre formatos.

## `4.3.0` (11/03/2019)

Cambio en las consultas internas de los objetos de modelo, en vez de construir SQLs ahora uso `Prepared Statements`. Esto hace que el parámetro `clean` quede obsoleto y se ha eliminado. En caso de estar todavía definido en algún modelo simplemente se ignorará.

## `4.2.2` (28/02/2019)

Corrección en `ODB` al obtener el último `id` insertado.

## `4.2.1` (26/02/2019)

Introduzco la nueva clase `ODBContainer`. Esta clase es un repositorio de las conexiones que se abren a la base de datos. De modo que si un objeto nuevo solicita abrir una conexión a una base de datos a la que ya se está conectado, se le devuelve esa conexión en lugar de crear una nueva.

Al acabar la ejecución se cierran todas las conexiones.

Esta versión es una corrección de la anterior ya que podía causar errores de "demasiadas conexiones abiertas" en casos con mucho acceso a base de datos.

## `4.2.0` (25/02/2019)

A partir de esta versión el modo de conectarse a la base de datos será mediante PDO. Con este cambio se abre la posibilidad de utilizar diferentes tipos de bases de datos, aunque `MySQL` sigue siendo el tipo por defecto. Esto es algo que llevo tiempo queriendo hacerlo, de echo existía la clase `ODBp` desde hace tiempo pero no la llegué a terminar ni usar nunca.

Retoques de estilo en tareas `update` y `updateCheck`.

## `4.1.1` (20/02/2019)

Retoques de estilo en tareas `update` y `updateCheck`.

## `4.1.0` (20/02/2019)

Las últimas dos actualizaciones introdujeron las tareas `update` y `updateCheck`, por lo que debería haber incrementado el número de versión (incrementar el último dígito indica correcciones sobre la versión actual).

Las tareas `update` y `updateCheck` han sido modificadas de modo que tengan en cuenta todas las actualizaciones intermedias entre la actual y la instalada. Antes, si alguien tenía la version `4.0.1` y ejecutaba la tarea `update`, se le instalaría la versión `4.1.0`, sin recibir las versiones `4.0.2` y la `4.0.3`.

Ahora las actualizaciones son secuenciales, de modo que se van instalando en orden de menor a mayor hasta alcanzar la versión actual.

Por otra parte, se ha añadido la tarea `version`, que ofrece información sobre la versión actual.

Por último, al ejecutar el comando `php ofw.php` se muestra la lista de tareas disponibles, solo que ahora están ordenadas de manera alfabética.

## `4.0.3` (20/02/2019)

Nueva tarea `updateCheck` para comprobar si existen actualizaciones del Framework y en caso de que existan, para comprobar que archivos se modificarán o eliminaran.

La tarea `update` ahora también puede borrar archivos viejos innecesarios, no solo añadir o modificar.

## `4.0.2` (19/02/2019)

Nueva tarea `update` para actualizar los archivos del Framework. Ejecutando `php ofw.php update` se comprueba la versión instalada contra la del repositorio de GitHub. En caso de haber una versión más nueva, la tarea se encarga de descargar y actualizar o añadir los archivos nuevos.

Esta versión es necesario actualizarla "a mano" pero las siguientes ya se podrán actualizar utilizando esta nueva tarea.

## `4.0.1` (18/02/2019)

Corrección en `runTask`, el método para ejecutar tareas desde los controladores.

## `4.0.0` (17/01/2019)

¡Nueva versión!

La versión 3 ha resultado ser una etapa intermedia, una forma de experimentar ideas. Pero quedaron muchas cosas a medio hacer y muchos bugs por corregir. Esta versión `4.0.0` introduce una serie de breaking changes. Estos son los principales cambios y novedades:

1. Nueva estructura de carpetas: se han separado las carpetas donde el usuario introduce su código y el código del framework propiamente. La carpeta `app` contiene todo el código de la aplicación y la carpeta `ofw` el framework en si.
2. `config.json` y archivos `config` de entorno: se ha quitado el antiguo `config.php` y ahora todas las opciones de configuración van en un solo archivo `json`, donde solo hay que incluir los campos que el usuario necesite. Ahora se pueden incluir archivos específicos de diferentes entornos, por ejemplo `config.dev.json`. Los valores de estos archivos sobrescriben los valores del archivo `config.json` global.
3. Correcciones de bugs: crear rutas nuevas en el archivo `urls.json` no creaba correctamente las nuevas funciones. `composer` también estaba roto.
4. Nuevos `services`: después de varias nomenclaturas, estructuras y aspectos... Presentamos los `services`. Primero fueron clases con funciones estáticas, luego una clase global con todas las clases dentro (lo que obligaba a usar `global $utils` cada vez que se querían usar...). Ahora por defecto no se carga ninguna de estas clases y los módulos pueden cargarlas como variables privadas que se inicializan en el constructor.
5. Nuevas `task`: hasta ahora las tareas eran scripts individuales de modo que todos tenían que inicializar todo el framework al inicio. Ahora son clases independientes y se ejecutan mediante el punto de entrada común `ofw.php`. Las `task` ahora se dividen entre las propias del framework y las creadas por el usuario, aunque todas se ejecutan del mismo modo.
6. Datos de ejemplo: se incluye un ejemplo de una pequeña aplicación de un sitio de fotos (con usuarios, fotos y tags) como demostración de como crear el modelo, módulos, controladores, filtros o tareas. Para crear una aplicación nueva, tan solo es necesario borrar el contenido de las carpetas que hay dentro de la carpeta `app`.

También he creado una nueva página para la documentación del framework (todavía en desarrollo):

[Osumi Framework Docs](https://framework.osumi.es)

## `3.1.0` (28/10/2018)

1. Corrección en OCache para expiración de cache y nueva funcionalidad para incluir nombre de remitente en OEmail (válido tanto para mail como para PHPMailer):

```php
  $email = new OEmail();
  ...
  $email->setFrom('email@prueba.com', 'Nombre remitente');
```

## `3.0.2` (03/10/2018)

1. Corrección para Tasks por la nueva versión.

## `3.0.1` (01/10/2018)

1. Corrección para llamadas CrossOrigin y corrección al inicializar Utils.

## `3.0` (28/09/2018)

1. Nueva estructura de Controllers. Hasta ahora los módulos eran archivos php independientes y dentro de cada uno estaban las funciones que componían cada módulo. Ahora cada archivo php contiene una clase php con el nombre del módulo, y heredan la nueva clase `OController`. Al heredar esta nueva clase, cada acción dentro de un módulo tiene acceso a varias funcionalidades:
- `config`: la configuración de la aplicación.
- `db`: una clase con la que realizar consultas personalizadas a la base de datos directamente.
- `template`: antes las acciones recibían como parámetro la clase `template` con la que acceder a la plantilla de la acción. Antes cada acción terminaba con una llamada a la función `process` y ahora ya no es necesario. Anteriormente las acciones solo devolvían datos de tipo HTML o JSON, pero ahora al definir que acción y módulo se ejecutan con cada URL, se puede añadir una nueva clave `type` en la que indicar el tipo deseado. De este modo, ahora en las acciones tampoco es necesario quitar el `layout` e indicar que los datos son JSON, por ejemplo.
- `log`: clase log genérica para el controller.
- `sessión`: clase con la que acceder a los datos de la sesión.
- `cookie`: clase con la que acceder a los cookies.

Antes:

```php
  /*
   * Ejemplo de función API que devuelve un JSON
   */
  function executeApiCall($req, $t){
    global $c, $s;
    /*
     * Código de la página
     */
    $status = 'ok';

    $t->setLayout(false);
    $t->setJson(true);

    $t->add('status',$status);
    $t->process();
  }
```

Ahora:

```php
class api extends OController{
  /*
   * Ejemplo de función API que devuelve un JSON
   */
  public function apiCall($req){
    /*
     * Código de la función
     */

    $status = 'ok';

    $this->getTemplate()->add('status', $status);
  }
}
```

2. Cambios en funciones auxiliares. Antes las funciones auxiliares eran clases estáticas que iban en la carpeta `model/static`. Ahora el nombre de la carpeta ha cambiado a `model/utils` y las clases ahora son clases normales, ya que ahora se crea un objeto global llamado `$utils` en el que se cargan todas las clases auxiliares.

Antes:

```php
stAdmin::getCategory($id);
```

Ahora:

```php
$utils['admin']->getCategory($id);
```

Estas clases auxiliares tienen incluido el controlador que se está usando de modo que tiene acceso a los mismos objetos (`config`, `db`...). De este modo no es necesario abrir conexiones a la base de datos en cada función, sino que pueden usar la conexión del controlador mejorando mucho el rendimiento.

## `2.16` (17/09/2018)

1. Nueva función `Base::runTask` con la que ejecutar una `task` desde código. Por ejemplo, una tarea que actualiza un `sitemap.xml` periódicamente con un cronjob pero que se pueda ejecutar cada vez que se actualice manualmente un producto.

## `2.15` (04/06/2018)

1. Nueva propiedad `expose` en los objetos del modelo. Se ha añadido el método `toString` a los objetos del modelo, de modo que al hacer un `echo $objeto` se muestra un objeto JSON con todas las propiedades del objeto, excepto las explícitamente marcadas como `expose = false`.

## `2.14` (10/04/2018)

1. Nueva task `composer` para exportar proyectos enteros a un solo archivo y luego poder crear todo el proyecto con un solo comando.

Ejecutando `php task/composer.php` se crea un archivo llamado `ofw-composer.php` en la carpeta `tmp` que contiene todos los archivos del framework.  Por ejemplo esto sirve para crear un backup o para poder exportar el proyecto entero y llevarlo a otro servidor.

2. Pequeñas correcciones en funciones de la clase `Base` para `composer` y nueva función `getParamList` para obtener varios parámetros con un solo comando.

## `2.13.2` (23/12/2017)

1. Los filtros pueden definir una url de retorno en caso de que no se cumpla, pero no funcionaba

## `2.13.1` (15/12/2017)

1. Pequeña corrección para que el método (get, post...) vaya en el objeto $req que se pasa a cada controller, en vez de ir en el objeto $s que se encarga de la sesión.

## `2.13` (13/12/2017)

1. Añado OToken para crear y validar tokenes JWT
2. Corrección en index para que se envíen las cabeceras para permitir peticiones Cross Origin si está así configurado (por defecto permitido).

## `2.12` (09/12/2017)

1. Añado OCache para crear archivos de cache para pares clave-valor.

## `2.11.2` (17/10/2017)

1. Corrección en update-urls, había un error al generar los nuevos controllers.

## `2.11.1` (17/10/2017)

1. Corrección en update-urls, las urls no heredaban correctamente todos los posibles parámetros.

## `2.11` (18/09/2017)

1. Nueva estructura para el archivo urls.json. Ahora las urls se pueden agrupar, cada grupo puede tener un prefijo (p.e. "/api/").
2. Nuevo gestor de tareas internas. Utilizando la tarea "`ofw`" se pueden acceder a tareas internas:
    1. "`generate-model`": tarea para generar el SQL resultante a partir del modelo definido en "`model/app`".
    2. "`update-urls`": tarea que lee el archivo "`urls.json`" y crea los controladores, funciones y templates. Esta tarea lee el archivo "`urls.json`" y genera una versión más reducida en la carpeta "`cache`" para uso interno.
3. Añado template de la función "`api`" de muestra que faltaba.

## `2.10` (03/07/2017)

1. Añado método a ODB (`all`) para obtener toda la lista de una consulta en vez de tener que andar recorriendo los resultados.

## `2.9.2` (08/06/2017)

1. Corrección en OBase para que no ejecute una SQL vacía en caso de que no se haya modificado nada.

## `2.9.1` (11/03/2017)

1. Corrección en OTemplate


## `2.9` (19/02/2017)

1. Posibilidad de redirigir a urls customizadas en caso e 403/404/500


## `2.8` (09/02/2017)

1. Corrección en clase de muestra
2. Corrección en OTemplate para poder añadir css y js desde los ontrollers

## `2.7` (21/01/2017)

1. Corrección en clase de muestra
2. Filtros de seguridad: en el archivo `urls.json` se puede definir un filtro de seguridad que se aplicará antes del controller llamado. Si el filtro de seguridad devuelve error, el usuario recibe el status 403.

## `2.6` (14/01/2017)

1. Añado clase ODBp para conexiones a la base de datos usando PDO, para consultas con Prepared Statements

## `2.5` (21/12/2016)

1. Añado clase OFTP con varios métodos para acceder a servidores remotos. Métodos como put, get, delete...

## `2.4` (28/10/2016)

1. ¡Paquetes! Sistema de plugins que incorpora nuevas funcionalidades o apartados completos a una web. Basta con añadir una línea en el archivo `config/packages.json`, añadir la carpeta correspondiente en `model/packages` y en caso de que lo necesite, la carpeta pública correspondiente en `web/pkg`
2. Se incluye el primer paquete, `backend`, que ofrece una interfaz para manejar los datos de la base de datos desde una interfaz creada con Angular Material
3. Correcciones y mejoras detectadas al hacer el nuevo desarrollo.
4. Añado tipo `FLOAT` para valores con decimales (01/11/2016)

## `2.3` (17/10/2016)

1. Añado la posibilidad de usar campos TEXT, en vez de marcarlos como texto y ponerles tamaño grande
2. Añado referencias a otros modelos para crear las foreign keys
3. Añado modificaciones para preparar el backend (20/10/2016)

## `2.2` (12/10/2016)

1. Separo librerías externas a la carpeta `model/lib`
2. Preparo carpetas para librerías PHPMailer y TCPDF pero no las incluyo, son proyectos grandes por si solos y solo se deberían incluir si fuesen necesarios
3. Añado funciones para transacciones en ODB (commit, rollback)

## `2.1` (11/10/2016)

1. Añado CHANGELOG
2. Refactorizo todas las clases G_* a O*, p.e. `G_Log` por `OLog`
3. Modifico task/generateModel para que sirva para todas las clases sin tener que añadirlas a mano
