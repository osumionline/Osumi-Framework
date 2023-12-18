ROADMAP
=======

## 8.4.0

La tarea updateUrls mira la carpeta de los plugins y mira en sus archivos de configuración a ver si hay la propiedad `hasUrls`.

Si es `true` se combina su archivo de urls con el global.

En el archivo `urls.cache.json` se incluye la propiedad `moduleBase`. Los módulos normales tiene el valor `app`, los de los plugins tienen el valor `plugin/nombrePlugin`. Así los plugins pueden tener sus urls y módulos propios.

La parte estática irá en `/web/ofw/nombrePlugin`

## 8.x

* Opción `static` en `OModuleAction` para crear URLs estáticas, que ignoren la acción y ni siquiera pasen por `OTemplate`.
* Comprobación carpetas del framework, si no existe `app` se crea, si no existe `config` se crea...
* Permitir que no haya `config.json`
