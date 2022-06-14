CHANGELOG
=========

## `8.0.1` (14/05/2022)

Primera ronda de correcciones y cambios tras usar la nueva versión 8.0 en producción.

### Componentes

Ahora los componentes tienen por defecto la propiedad `nourlencode`. Ya que la mayoría de veces los componentes se utilizan para renderizar resultados parciales o para mostrar componentes reutilizables, no tenía sentido que siguiesen siendo `urlencode-ados`.

Aun así los componentes tienen ahora un método para forzar el `urlencode-ado`:

```php
$user_list = new UserListComponent(['list' => []]);
$user_list->setURLEncode(true);
```

Ahora los componentes también cuentan con un método `setValue` que permite actualizar un valor añadido al inicializar el componente, o añadir uno nuevo:

```php
$user_list->setValue('list', $new_list);
```

### Acciones

Con la versión 8.0 se introdujo el decorador `OModuleAction` con el que configurar las acciones. Este decorador aceptaba varios valores, listas, como cadenas de texto de valores separados por comas.

Esto resultaba muy ineficiente ya que cada llamada parseaba este valor para luego dividirlo en un array de valores individuales. Más código que mantener, código que no cuesta nada añadirlo y pocas veces se modificará una vez se empiece a trabajar en la acción...

```php
// Antes
#[OModuleAction(
	url: '/getUsers',
	services: 'user, backend'
)]
class getUsersAction extends OAction {
...
}

// Ahora
#[OModuleAction(
	url: '/getUsers',
	services: ['user', 'backend']
)]
class getUsersAction extends OAction {
...
}
```

### Gestión de errores

A partir de esta versión, el propio framework se encarga de gestionar los posibles errores que se produzcan en tiempo de ejecución. Tanto errores de programación (errores de sintaxis, punto-comas que faltan, corchetes sin cerrar...)  como errores debidos a la programación (accesos a bases de datos, divisiones por cero...).

El framework muestra una página con información del lugar dónde se produjo el error (archivo y línea) y la pila de trazas que han llevado a ese error.

### Métodos para cargar servicios y componentes

La versión 8.0 introdujo la filosofía de "cargar exclusivamente lo mínimo necesario". Para eso, el decorador `OModuleAction` cuenta con parámetros para cargar los servicios y componentes que se vayan a usar.

Pero los servicios, tareas o clases de modelo no cuentan con este mecanismo. Esto hacía que no se pudiese llamar de un servicio a otro o que no se pudiesen usar componentes en un servicio.

Para solucionar esto se han incluido tres métodos estáticos nuevos en la clase `OTools`:

```php
OTools::loadService('nombre_de_servicio');
// Carga el servicio app/services/nombre_de_servicio.service.php
OTools::loadComponent('nombre_de_componente');
// Carga el componente app/component/nombre_de_componente/nombre_de_componente.component.php
OTools::loadComponents(['componente-1', 'componente-2', ...]);
// Similar al anterior pero para cargar una lista de componentes, en vez de hacer llamadas individuales.
```

### Correcciones

Esta versión cuenta con unas cuantas correcciones de errores encontrados una vez usado en producción.

## `8.0.0` (25/05/2022)

¡Nueva versión 8.0!

Esta es una nueva versión mayor por que introduce cambios que rompen la estructura y la filosofía de módulos y componentes actual.

### Componentes

A partir de esta versión los componentes estarán formados por dos archivos, por ejemplo el componente `users` ahora constará de dos archivos:

* `users.component.php`: Contiene la lógica que necesite el componente así como acceso a los apartados del framework.
* `users.template.php`: Contiene el resultado que se devolverá al interpretar el componente, es el archivo que formaba anteriormente el componente.
* **Archivos CSS y JS**: En el archivo del componente se pueden incluir unas variable `css` y `js`, opcionales, que serán dos arrays con los nombres de archivos CSS y JS respectivamente. Estos archivos serán incluidos en el resultado como etiquetas `<style>...</style>` y `<script>...</script>`

Por ejemplo, el archivo `users.component.php` sería:

```php
  <?php declare(strict_types=1);
  
  namespace OsumiFramework\App\Component;
  
  use OsumiFramework\OFW\Core\OComponent;
  
  class UsersComponent extends OComponent {
    public array $css = ['users'];
  }
```

El archivo `users.template.php` sería:

```php
  <?php if (count($values['users'])>0): ?>
    <ul class="users">
  <?php foreach ($values['users'] as $user): ?>
      <li>
        <a href="/user/<?php echo $user->get('id') ?>"><?php echo $user->get('user') ?></a>
      </li>
  <?php endforeach ?>
    </ul>
  <?php else: ?>
    There are no users, yet.
  <?php endif ?>
```

Para usar este componente, en una `acción` hay que crear una variable a la que se le pasarán los valores a mostrar en su inicialización:

```php
  $users = $this->user_service->getUsers();
  $users_component = new UsersComponent(['users' => $users]);
  
  $this->getTemplate()->add('users', $users_component);
```

Los componentes ahora se incluyen como si fuese una variable normal más, usando el método `add`.

### Nombres de archivos

A partir de esta versión los diferentes archivos de la aplicación deben indicar su tipo en el nombre del archivo:

* **Filtros**: login.filter.php
* **Layout**: default.layout.php
* **Modelos**: user.model.php
* **Módulos**: api.module.php
* **Servicios**: photo.service.php
* **Tareas**: email.task.php

### Modelos

A partir de esta versión, los archivos de modelo cambian ligeramente. Ahora el nombre del archivo indicará directamente el nombre de la tabla de la base de datos. Esto hace que ya no sea necesario indicar el nombre de la tabla al cargar el modelo.

Antes:

```php
  ...
  parent::load($table_name, $model);
  ...
```

Ahora:

```php
  ...
  parent::load($model);
  ...
```

En el caso de que una tabla tenga un nombre demasiado complejo como para poder ser el nombre del archivo, el método `load` admite un segundo parámetro opcional para indicar el nombre de la tabla y prevalecerá sobre el nombre del archivo.

### Módulos

El mayor cambio de esta versión son los módulos. Inspirandome en los componentes independientes (standalone components) de Angular 14, he traído esa filosofía a los módulos. Antes los módulos eran cada uno un archivo que contenía una clase con las diferentes acciones que lo componían.

Esto tenía dos problemas principales:

* **Falta de modularidad**: En el caso de que hubiese un fallo de sintaxis en una sola línea, todas las acciones que componían ese módulo dejaban de funcionar ya que formaban parte del mismo archivo.
* **Rendimiento**: Cargar una página que formaba parte de un módulo hacía que se cargasen todos los servicios y todas las acciones de ese módulo. Una simple página de "hola mundo" podía hacer que se cargasen en memoria todas las acciones de ese módulo, aunque no tuviesen nada que ver.

A partir de esta versión los módulos se reducen a su mínima expresión, actuando simplemente de "índices", para indicar que acciones componen el módulo. Por ejemplo, antes el módulo `api` tenía esta forma:

```php
  <?php declare(strict_types=1);
  
  namespace OsumiFramework\App\Module;
  
  use OsumiFramework\OFW\Core\OModule;
  use OsumiFramework\OFW\Web\ORequest;
  use OsumiFramework\OFW\Routing\ORoute;
  use OsumiFramework\App\Service\userService;
  
  /**
   * Sample API module
   */
  #[ORoute(
    type: 'json',
    prefix: '/api'
  )]
  class api extends OModule {
    private ?userService $user_service;
  
    function __construct() {
      $this->user_service  = new userService();
    }
  
    /**
     * Function used to obtain current date
     *
     * @param ORequest $req Request object with method, headers, parameters and filters used
     * @return void
     */
    #[ORoute('/getDate')]
    public function getDate(ORequest $req): void {
      $this->getTemplate()->add('date', $this->user_service->getLastUpdate());
    }
    
    ...
    public function getUsers(ORequest $req): void { ... }
    
    ...
    public function getUser(ORequest $req): void { ... }
  }
```

Esto es, el módulo `api` era una clase donde se cargaba el servicio `userService` y se componía de tres acciones, tres métodos de esa clase.

A partir de esta versión, el módulo `api` tiene este aspecto:

```php
  <?php declare(strict_types=1);
  
  namespace OsumiFramework\App\Module;
  
  use OsumiFramework\OFW\Routing\OModule;
  
  /**
   * Sample API module
   */
  #[OModule(
    type: 'json',
    prefix: '/api',
    actions: 'getDate, getUser, getUsers'
  )]
  class apiModule {}
```

Es una clase vacía que mediante el decorador `OModule` se convierte en un módulo. El campo `actions` es un string de los nombres de las acciones que componen ese módulo. Junto al archivo `api.module.php` hay una carpeta llamada `actions` que contendrá el código de cada acción. En esta carpeta cada acción tendrá una carpeta propia con el nombre de la acción y dentro tendrá dos archivos. Por ejemplo, la acción `getUsers` tendrá este aspecto:

```php
  /app
    /module
      /api
        api.module.php
        /actions
          /getDate
          /getUser
          /getUsers
            getUsers.action.php
            getUsers.action.json
```

El archivo `getUsers.action.php` será el que contenga el código que se ejecutará al llamar a la acción y el archivo `getUsers.action.json` será el resultado devuelto tras la llamada. El archivo de la acción tiene un decorador de tipo `OModuleAction`:

```php
  #[OModuleAction(
    url: '/getUser/:id',
    services: 'user',
    filter: 'login',
    components: 'user'
  )]
  class getUserAction extends OAction {
    ...
  }
```

* **url**: URL con la que se ejecutará la acción. Si en el módulo se ha definido un `prefix`, este se añadirá al comienzo. Por ejemplo en el módulo se puede poner `prefix: '/api'` y en la acción `url: '/getUser/:id`, por lo que la URL resultante sería `/api/getUser/:id`.
* **services**: Es una cadena de texto con los servicios que se usarán en la acción, separados por comas. Por ejemplo, al poner `services: 'user'`, en la acción se podrá usar el servicio: `$this->user_service`.
* **filter**: Filtro a usar en la acción. El filtro ejecutará antes de la acción y en caso de que el resultado sea positivo se podrá acceder al filtro desde la variable `ORequest`.
* **components**: Es una cadena de texto con los componentes que se usarán en la acción, separados por comas. Por ejemplo, al poner `components: 'home/users'`, en la acción se podrá usar el componente: `$component = new UserComponent();`.
* **inlineCSS**: Es una cadena de texto con los nombres de archivos CSS, separados por comas, que se incluirán en línea mediante etiquetas `<style>...</style>`.
* **css**: Es una cadena de texto con los nombres de archivos CSS, separados por comas, que tienen que residir en la carpeta `web/css`. Se incluirán como dependencias mediante las etiquetas `<link rel="stylesheet" href="...">`.
* **inlineJS**: Es una cadena de texto con los nombres de archivos JS, separados por comas, que se incluirán en línea mediante etiquetas `<script>...</script>`.
* **js**: Es una cadena de texto con los nombres de archivos JS, separados por comas, que tienen que residir en la carpeta `web/js`. Se incluirán como dependencias mediante las etiquetas `<script src="..."></script>`.

El código que se ejecutará al llamar a la acción será la función `run`, pero dentro de la misma clase de la acción se pueden definir todo tipo de funciones auxiliares.

### Resumen

Este cambio de estructura hace que una llamada a una acción cargue y ejecute el código mínimo necesario para ser ejecutado, sin tener que cargar el resto de acciones o servicios. Por ejemplo:

```php
  #[OModuleAction(
    url: '/user/:id',
    services: 'user, photo',
    components: 'home/photo_list'
  )]
  class userAction extends OAction {
    ...
  }
```

* Llamada a `/user/:id`
* Se carga el módulo `home`.
* Se carga la acción `user`.
* Se cargan los servicios `user` y `photo`.
* Se carga el componente `home/photo_list`.
* Se ejecuta la función `run`.
* El resultado se maqueta con el archivo `user.action.html`.

De este modo no se cargan el resto de módulos, ni el resto de acciones del módulo `home` y se cargan los servicios y componentes que se utilizaran exclusivamente en esta acción.

### Migración (7.x -> 8.0)

Para actualizar a esta ultima versión basta con ejecutar:

`ofw update`

Esto actualizará todos los archivos que componen el framework y realizará una serie de tareas para actualizar el código de la aplicación a las nuevas convenciones de nombres y estructuras:

* **Componentes**: Convierte los componentes que encuentre al nuevo formato de `OComponent`. Luego es tarea del programador que actualizar el código de las acciones para usar el nuevo formato de manera manual.
* **Filtros**: Convierte el nombre de los archivos de los filtros: `loginFilter.php -> login.filter.php`.
* **Layouts**: Convierte el nombre de los archivos de los layouts: `default.php -> default.layout.php`.
* **Modelos**: Convierte el nombre de los archivos de los modelos basándose en la variable `$table_name`: `PhotoTag.php -> photo_tag.model.php`. También quita la línea donde se definía el nombre de la tabla y modifica la llamada a la función `load` para quitar este parámetro.
* **Módulos**: Convierte el nombre de los archivos de los módulos: `api.php -> api.module.php`. También crea la nueva carpeta `actions`, crea las carpetas y archivos para cada acción, pero no convierte las acciones anteriores a las nuevas. Es tarea del programador pasar manualmente el código de cada acción al archivo que le corresponda y vaciar el archivo de módulo correspondiente.
* **Servicios**: Convierte el nombre de los archivos de los servicios: `photo.php -> photo.service.php`.
* **Tareas**: Convierte el nombre de los archivos de las tareas: `email.php -> email.task.php`.

## `7.9.5` (25/05/2022)

Última corrección en `OUpdate` previa al lanzamiento de `OsumiFramework 8.0`.


## `7.9.4` (24/05/2022)

Corrección en `OUpdate` (por `postinstall`). Esta corrección sirve para preparar correctamente la actualización a la próxima versón 8.0.

## `7.9.3` (24/05/2022)

Corrección en `OUpdate`. Esta corrección sirve para preparar correctamente la actualización a la próxima versón 8.0.

## `7.9.2` (10/05/2022)

Corrección en clase `OModel`. Al usar la función `find` en objetos de modelo, las búsquedas cuyo valor fuese booleano fallaban ya que eran tomadas como cadenas de texto vacías. OFW almacena los valores booleanos como números enteros donde el valor 1 representa `true` y el valor 0 representa `false`.

```php
$tabla->find([
  'id' => 12,
  'valor' => false
])
```

SQL resultante (antes):

```sql
SELECT * FROM `tabla` WHERE `id` = 12 AND `valor` = ''
```

SQL OFW 7.9.2:

```sql
SELECT * FROM `tabla` WHERE `id` = 12 AND `valor` = 0;
```

## `7.9.1` (09/05/2022)

Corrección en clase `OModel`. Al usar la función `find` en objetos de modelo, las búsquedas cuyo valor fuese nulo fallaban ya que eran tomadas como cadenas de texto vacías:

```php
$tabla->find([
  'id' => 12,
  'valor' => null
])
```

SQL resultante (antes):

```sql
SELECT * FROM `tabla` WHERE `id` = 12 AND `valor` = ''
```

SQL OFW 7.9.1:

```sql
SELECT * FROM `tabla` WHERE `id` = 12 AND `valor` IS NULL
```

## `7.9.0` (21/03/2022)

Nuevas clases auxiliares `utils`. Ahora se pueden usar clases auxiliares que no se cargarán por defecto con todo el Framework.

Por ejemplo, una clase llamada `PDF` encargada de crear archivos PDF. Esta clase solo se utilizará en ocasiones puntuales, por lo que no es necesario incluirla en cada una de las llamadas que reciba la aplicación.

Estas nuevas clases se guardarán en la carpeta `app/utils`, bajo el namespace `OsumiFramework\App\Utils` (por ejemplo `OsumiFramework\App\Utils\PDF`).

Hay dos formas de incluirlas:

Usando ORoute
-------------

Cada acción de un módulo tiene un decorador de tipo `ORoute` con el que configurar su URL, el tipo de retorno... Ahora acepta un nuevo parámetro `utils`, una cadena de texto con los nombres de las clases a cargar separados por comas. Por ejemplo:

```php
#[ORoute(
  '/getUser/:id',
  utils: 'PDF'
)]
public function getUser(ORequest $req): void {
...
}
```

Manualmente
-----------

La función `getDir` de la clase `OConfig` ahora soporta un nuevo parámetro `app_utils`, que apunta a la carpeta `app/utils`. De este modo se puede crear una ruta al archivo que se quiera cargar e incluirlo a mano:

```php
$pdf_class_route = $this->getConfig()->getDir('app_utils').'PDF.php';
require_once $pdf_class_route;
```

## `7.8.0` (27/12/2021)

¡Nuevo `Osumi Framework CLI`!

Esta actualización prepara el framework para ser usado mediante la nueva herramienta CLI. Esta nueva herramienta es un ejecutable de línea de comandos para ser usado independientemente de cada proyecto, sustituyendo al archivo `ofw.php`.

Una vez descargado el CLI, hay que mover el ejecutable a una carpeta que esté en el PATH. A continuación ofrece una nueva opción `new` con la que crear un proyecto desde cero.

```
  $ ofw new prueba
  Cloning into 'prueba'...
  remote: Enumerating objects: 2052, done.
  remote: Counting objects: 100% (269/269), done.
  remote: Compressing objects: 100% (193/193), done.
  remote: Total 2052 (delta 145), reused 159 (delta 61), pack-reused 1783
  Receiving objects: 100% (2052/2052), 1.23 MiB | 0 bytes/s, done.
  Resolving deltas: 100% (1233/1233), done.
```

Esta tarea crea una nueva aplicación descargando el repositorio oficial y ejecutando la nueva tarea `reset`, de modo que queda listo para empezar a ser usado.

Por otra parte, estando dentro de una carpeta que contenga una aplicación `Osumi Framework`, puede ser usado del mismo modo que se usaba el archivo `ofw.php`:

```
  $ ofw version


  ==============================================================================================================

    Osumi Framework

    7.8.0 - Nuevo CLI

    GitHub:  https://github.com/igorosabel/Osumi-Framework
    Twitter: https://twitter.com/osumionline

  ==============================================================================================================
```

Al estar el ejecutable en el path, en caso de que haya varios proyectos en la misma máquina, el ejecutable es el mismo para todos y deja de ser necesario el archivo `ofw.php`.

## `7.7.0` (26/12/2021)

Nueva tarea `reset`. Esta tarea borra todo el contenido generado por el usuario, una especie de sistema de auto-destrucción que resetea el estado de la aplicación a cero.

Esto sirve para la creación de aplicaciones nuevas. Al bajar el repositorio de Github, este incluye una aplicación de ejemplo con abundante código, y hay que borrar todo antes de poder empezar a crear contenido nuevo.

Esta tarea consiste de dos pasos, para asegurarse, ya que el borrado es definitivo e irreversible.

El primer paso consiste en ejecutar la tarea:

```
  php ofw.php reset
```

Tras un aviso y una cuenta atrás de 15 segundos, que se puede interrumpir presionando Control + C, se ofrecerá un código de confirmación. Este código es de un solo uso y tiene una caducidad de 15 minutos. En caso de escribir mal el código o si se introduce pasados 15 minutos, el código deja de ser válido y hay que volver a solicitar uno nuevo.

A continuación, hay que volver a llamar a la tarea pasándole el código que se ha creado en el primer paso. Por ejemplo:

```
  php ofw.php reset 0f982313a901
```

Tras ejecutar este comando, la aplicación se habrá reseteado.

¡Asegúrate de tener copias de seguridad!

## `7.6.1` (11/12/2021)

Corrección en rutas con parámetros GET. Los parámetros pasados por GET no se estaban ignorando, de modo que las rutas que los recibiesen nunca coincidían y devolvían un error 404.

## `7.6.0` (18/10/2021)

Ahora las acciones de los métodos, ademas poder recibir un objeto de tipo `ORequest`, pueden recibir objetos DTO (Data Transfer Object) personalizados.

Estos DTOs son clases que se guardan en la carpeta `app/dto` y tienen que implementar la interfaz `ODTO`, para asegurar que contienen dos métodos necesarios. Por ejemplo:

```php
class UserDTO implements ODTO{
  private int $id_user = -1;

  public function getIdUser(): int {
    return $this->id_user;
    }
  private function setIdUser(int $id_user): void {
    $this->id_user = $id_user;
  }

  public function isValid(): bool {
    return ($this->getIdUser() != -1);
  }

  public function load(ORequest $req): void {
    $id_user = $req->getParamInt('id');
    if (!is_null($id_user)) {
      $this->setIdUser($id_user);
    }
  }
}
```

En el ejemplo, la clase `UserDTO` implementa la interfaz `ODTO` y debe tener los métodos `isValid` y `load`:

* La función `load` recibe un objeto de tipo `ORequest` tal y como lo recibían antes las acciones de los módulos. Esta función se usará para cargar los datos recibidos en las variables apropiadas del DTO.
* La función `isValid` sirve para realizar una validación de los datos obtenidos.

De este modo las acciones de los módulos que reciban los mismos parámetros (por ejemplo un id de usuario) pueden compartir un mismo DTO que obtiene y valida los datos. Así se evita repetir el mismo código en cada acción: obtener datos, validar datos...

## `7.5.0` (31/05/2021)

Mejora en `OLog`. Ahora ademas de la fecha, nivel de log, clase que le ha llamado y el mensaje, también se guardará el nombre del archivo y la línea desde donde se ha ejecutado la llamada a logear.

## `7.4.1` (28/05/2021)

Corrección en la función `OTools::curlRequest`. En el caso de que una llamada cURL falle, la ejecución devuelve el valor `false` y la función estaba preparada para devolver un `string`.

## `7.4.0` (24/05/2021)

Añado el método `getCacheContainer` a los módulos, servicios y tareas. De este modo no es necesario acceder al objeto global `core`. Por ejemplo:

```php
class api extends OModule {
/**
 * Función para obtener la fecha
 *
 * @url /getDate
 * @param ORequest $req Request object with method, headers, parameters and filters used
 * @return void
 */
public function getDate(ORequest $req): void {
  $this->getCacheContainer()->deleteItem('last_date');
}
```

## `7.3.2` (21/05/2021)

Corrección de estilo al crear nuevos servicios o tareas mediante el comando `php ofw.php add`, se han añadido unos saltos de líneas para separar mejor los namespaces de las clases que se usan.

Corrección al crear un componente de modelo. Al crear un componente de modelo se generan dos archivos, un componente para un objeto de modelo y un componente para crear listados de objetos de modelo. En el listado faltaba por incluir la clase `OTools`.

## `7.3.1` (19/05/2021)

Corrección al crear nuevos servicios o tareas mediante el comando `php ofw.php add`. Los nuevos servicios y tareas no incluían los nuevos namespaces.

## `7.3.0` (12/04/2021)

Cambios en el sistema de logs. Nuevos campos en el archivo `config`:

```json
...
"log": {
	"name": "ofw",         // Nombre del archivo donde se guardarán los logs
	"max_file_size": 50,   // Tamaño máximo del archivo, en MBs
	"max_num_files": 3     // Número máximo de rotaciones del archivo de logs
}
...
```

* `name` indica el nombre del archivo donde se escriben los logs.
* `max_file_size` indica el tamaño máximo (en MB) del archivo de logs. El valor por defecto es 50MB.
* `max_num_files` indica el número de archivos que se rotarán. El valor por defecto es de 3 archivos de rotación.

Si el valor de `max_num_files` es 1, en cuanto el archivo de log llegue a ocupar el valor definido en `max_file_size`, cada nueva línea hará que se borren las más antiguas.

Nuevo campo `name` en el archivo `config`. Este campo por ahora no es más que meramente informativo.

Si el valor `name` del campo `log` está en blanco, se usará el slug del campo `name`. En caso de que este campo no esté presente, el valor por defecto es `Osumi`.

## `7.2.0` (06/04/2021)

Nuevo idioma para el Framework: **Euskara**.

Eskerrik asko [Aitorri](https://mastodon.eus/@altzaporru) itzulpena egiteagatik eta [Librezale](https://telegram.me/librezale) taldeari laguntzeagatik.

También se ha hecho una pequeña corrección por las traducciones que tuviesen saltos de línea y no se estaban interpretando correctamente.

Se ha añadido una cabecera `X-Powered-By` para indicar la versión del Framework.

## `7.1.1` (05/04/2021)

Corrección al minimizar la salida de JSON en entornos de producción. Parece que la función que usaba para minimizar el resultado tenía algún bug y en alguna ocasión se quedaba atascada en un bucle infinito. He quitado esa función y he sustituido la funcionalidad por las funciones nativas `json_decode` / `json_encode` de `PHP`.

## `7.1.0` (31/03/2021)

Nuevo sistema de traducciones. Hasta ahora la clase `OTranslate` era un plugin externo, pero ahora se ha integrado en el propio Framework ya que pasa a ser el sistema por defecto para todos los mensajes internos.

Este nuevo sistema de traducciones usa archivos `po` para gestionar sus traducciones en lugar de usar archivos `php`. De esta forma se estandariza el uso de mensajes y se facilita sus traducciones.

# Nueva clase `OTranslate`

La clase `OTranslate` no solo se ha integrado en el Framework, sino que se ha modificado su funcionamiento. La versión anterior dependía de un archivo `json` que se alojaba en la carpeta `config`. Ahora se pueden ubicar los archivos de las traducciones donde se quiera y el método `load` de esta clase se encarga de cargar y parsear sus datos.

Su funcionamiento para obtener una traducción sería esta:

```php
$tr = new OTranslate();
$tr->load('/ruta/a/archivo.po');

echo $tr->getTranslation('CLAVE_A_BUSCAR');
```

La clase `OTranslate` ahora también tiene métodos para crear un nuevo archivo de traducción, añadir textos y guardarlo. También se puede cargar un archivo ya existente y modificar su contenido:

```php
// Nuevo archivo
$tr = new OTranslate();
$tr->new('/ruta/a/archivo_es.po', 'es'); // Ruta donde guardar el archivo y código de idioma
$tr->setTranslation('CLAVE', 'Texto traducido');
$tr->save();

// Modificar archivo
$tr = new OTranslate();
$tr->load('/ruta/a/archivo.po');
$tr->setTranslation('CLAVE', 'Texto traducido modificado');
$tr->save();

// Guardar el archivo en una nueva ubicación
$tr->save('/ruta/a/nuevo_archivo.po');
```

## `7.0.1` (30/03/2021)

Correcciones menores:

* Corrección para sitios que no usan base de datos: se intentaba inicializar el ODB por defecto para los módulos y la clase no estaba cargada al no tener base de datos.
* Corrección al generar cache de URLs: la función `loadUrls` de la clase `OURL` debería ser estática y no lo era.

## `7.0.0` (16/03/2021)

¡Nueva versión 7.0!

Esta es una nueva versión mayor por que introduce cambios que rompen la estructura y la filosofía de URLs actual y se introducen los nombres de espacios.

A partir de esta versión, el Framework requiere el uso de PHP 8.0 o superior para funcionar ya que el sistema de enrutamiento actual se basa en las nuevas anotaciones de PHP 8.0

### Rutas

En la versión anterior las rutas se definían usando bloques de comentarios en cada función de los módulos. Por ejemplo:

```php
/**
 * Módulo API de prueba
 *
 * @type json
 * @prefix /api
 */
class api extends OModule {
	/**
	 * Función para obtener la fecha
	 *
	 * @url /getDate
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	public function getDate(ORequest $req): void {
		...
	}
}
```

A partir de esta versión, se usará el nuevo sistema de anotaciones de PHP 8.0. Por ejemplo:

```php
/**
 * Módulo API de prueba
 */
#[ORoute(
	type: 'json',
	prefix: '/api'
)]
class api extends OModule {
	/**
	 * Función para obtener la fecha
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	#[ORoute('/getDate')]
	public function getDate(ORequest $req): void {
		...
	}
}
```

Los anteriores comentarios ahora se traducen como parámetros de la función `ORoute`:

```
@url  /ejemplo
@type json
@prefix /api
@filter testFilter

#[ORoute(
	'/ejemplo',
	type: 'json',
	prefix: '/api',
	filter: 'testFilter'
)]
```

### Nombres de espacios

Con la idea de homogeneizar el código base del Framework y organizar mejor la aplicación, se ha organizar todo el código en nombres de espacios. Las tareas encargadas de crear nuevos módulos, servicios, componentes o tareas también se han actualizado para que los incorporen. Ahora en cada archivo hay que incluir los nombres de espacio al que corresponda el archivo y los nombres de espacio de las funciones que se quieren usar.

Estos son los nombres de espacio para los archivos que puede crear un usuario del Framework:

* `OsumiFramework\App\Model`: Espacio para las clases de modelo de la base de datos del usuario.
* `OsumiFramework\App\Module`: Espacio para las clases de los módulos que componen la aplicación.
* `OsumiFramework\App\Service`: Espacio para las clases de servicios que se utilizan en los módulos.
* `OsumiFramework\App\Task`: Espacio para las tareas propias de la aplicación.

Las clases internas del Framework ahora se organizan del siguiente modo:

* `OsumiFramework\OFW\Cache`
  * `OCache`
  * `OCacheContainer`
  * Estas clases se han rehecho basándose en [PSR-6](https://www.php-fig.org/psr/psr-6).
* `OsumiFramework\OFW\Core`
  * `OConfig`
  * `OCore`
  * `OModule`
  * `OPlugin`
  * `OService`
  * `OTask`
  * `OTemplate`
  * `OUpdate`
* `OsumiFramework\OFW\DB`
  * `ODB`
  * `ODBContainer`
  * `OModel`
* `OsumiFramework\OFW\Log`
  * `OLog`
* `OsumiFramework\OFW\Routing`
  * `ORoute`
  * `ORouteCheck`: Esta clase substituye a la librería anteriormente usada para el enrutamiento, aunque sigue estando basada en ella.
  * `OUrl`
* `OsumiFramework\OFW\Tools`
  * `OColors`
  * `OForm`
  * `OTools`
* `OsumiFramework\OFW\Web`
  * `OCookie`
  * `ORequest`
  * `OSession`

De este modo las clases internas quedan organizadas en espacios separados y se asegura su uso correcto al tener que inicializarlas. Por ejemplo, un módulo que utiliza una clase de servicio, tendría este aspecto ahora:

```php
namespace OsumiFramework\App\Module;

use OsumiFramework\OFW\Core\OModule;
use OsumiFramework\OFW\Web\ORequest;
use OsumiFramework\OFW\Routing\ORoute;
use OsumiFramework\App\Service\userService;
```

Al actualizar una aplicación automáticamente se modificarán todos los archivos con las nuevas sintaxis.
