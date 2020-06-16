ROADMAP
=======

## 5.8

* Estructura `app/module` en vez de `controller + template`

Por ejemplo:

* app
  * config
  * module
    * api
      * api.php
      * template
        * login.json
        * register.json
    * home
      * home.php
      * template
        * home.html
        * register.html

Y dejar carpeta `app/template` para `layout` y `partials`

## 5.9

* Opción `static` en `urls.json` para crear URLs estáticas, que ni siquiera pasen por `OTemplate`, no tengan `action` pero si están en un módulo.
* Comprobación carpetas del framework, si no existe `app` se crea, si no existe `config` se crea...
* Permitir que no haya `config.json`