CHANGELOG
=========

## `4.8.1` (27/05/2019)

Corrección en tarea `update`. Al terminar escribía el nuevo número de versión en el archivo `VERSION`, a pesar de que ya no se usa para nada.

## `4.8.0` (27/05/2019)

Actualización de mantenimiento:

* `OTemplate`: Limpieza de código. Cambio sintaxis antigua de `array()` por `[]`.
* `updateUrls`: Mejoro los mensajes mostrados por consola al ejecutar esta tarea (tabulaciones, colores...).
* `update`: Mejora al obtener las actualizaciones. Ahora al realizar una actualización se descarga la versión específica de cada archivo. Hasta ahora siempre se descargaba la últiima versión del archivo y en el caso de que hubiese varias actualizaciones, cada actualización siempre cogía la misma versión (la master).
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

__rename__: Método estatico para cambiar de nombre y/o de ubicación a un archivo. Recibe dos parámetros: nombre antiguo y nuevo nombre. Devuelve `true` o `false` como resultado de la operación. Por ejemplo:

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

Nueva clase `OCrypt` para cifrar/descifrar cadenas de texto. Esta clase acepta una clave de 32 caracteres como clave de cifrado y ofrece unos métodos `encrypt` y `decrypt` para cifrar y descrifrar los datos:

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

La clase ahora puede cargar imagenes `jpg`, `png` o `gif` y cambiar su tamaño, escalarlas o convertirlas entre formatos.

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

Por último, al ejecutar el comando `php ofw.php` se muestra la lista de tareas disponibles, solo que ahora están ordenadas de manera alfabetica.

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
6. Datos de ejemplo: se incluye un ejemplonde una pequeña aplicación de un sitio de fotos (con usuarios, fotos y tags) como demostración de como crear el modelo, módulos, controladores, filtros o tareas. Para crear una aplicación nueva, tan solo es necesario borrar el contenido de las carpetas que hay dentro de la carpeta `app`.

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

1. Nueva propiedad `expose` en los objetos del modelo. Se ha añadido el método `toString` a los objetos del modelo, de modo que al hacer un `echo $objeto` se muestra un objeto JSON con todas las propiedades del objeto, excepto las explicitamente marcadas como `expose = false`.

## `2.14` (10/04/2018)

1. Nueva task `composer` para exportar proyectos enteros a un solo archivo y luego poder crear todo el proyecto con un solo comando.

Ejecutando `php task/composer.php` se crea un archivo llamado `ofw-composer.php` en la carpeta `tmp` que contiene todos los archivos del framework.  Por ejemplo esto sirve para crear un backup o para poder exportar el proyecto entero y llevarlo a otro servidor.

2. Pequeñas correcciones en funciones de la clase `Base` para `composer` y nueva funcion `getParamList` para obtener varios parametros con un solo comando.

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
