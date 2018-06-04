CHANGELOG
=========

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