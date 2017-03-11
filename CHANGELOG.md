CHANGELOG
=========

## `2.9.1` (11/03/2017)

1. Corrección en Otemplate


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
