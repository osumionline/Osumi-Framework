ROADMAP
=======

## ~~5.9~~

* ~~Opción `static` en `urls.json` para crear URLs estáticas, que ni siquiera pasen por `OTemplate`, no tengan `action` pero si están en un módulo.~~
* ~~Comprobación carpetas del framework, si no existe `app` se crea, si no existe `config` se crea...~~
* ~~Permitir que no haya `config.json`~~

*NOTA: Las funcionalidades previstas para esta versión se incluirán en la proxima versión mayor.*

## 6.0

* Task `add` para crear nuevos módulos/acciones/partials.

Por ejemplo `php ofw.php add module api` o `php ofw.php add action api getUser`.

* Quitar task `updateUrls`.
* Información de `urls.json` en anotaciones phpDoc.
* Quitar `urls.json`.

La nueva versión prescindiría del archivo `urls.json` ya que esta información estaría en cada clase de módulo o las funciones de las acciones. Por ejemplo:

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
 	 * @param ORequest $req Request object with method, headers, parameters and filters used
 	 * @return void
 	 */
 	 function getUserList(ORequest $req): void { ... }
}
```

Las propiedades de la clase se aplicarían a cada acción, pero si una acción tiene una propiedad tendría prioridad.