ROADMAP
=======

## 8.0.2

* `depends` en componentes, cambiarlo de string a array. Así se evitará que parsearlo en cada solicitud.

##  8.0.3

* `actions` en módulos, cambiarlo de string a array. Así se evitará que parsearlo en cada solicitud.

## 8.0.4

* Cambiar `$req->getFilter('login')`, quitar parámetro. Si solo hay un filtro… no hace falta indicar cuál. O implementar múltiples filtros. Por decidir.

## 8.0.5

* Cambiar DTOs a nombre.dto.php

## 8.1

* Investigar `eagerLoader`: recorrer archivos que se van a cargar, analizar líneas de "use ..." e ir incluyendo y analizando recursivamente. Esto haría obsoletos todos los parametros de servicios, componentes, los `loadService` y `loadComponent`... ya que los archivos necesarios se cargarían mirando "lo que se va a usar".
* Opción `static` en `OModuleAction` para crear URLs estáticas, que ignoren la acción y ni siquiera pasen por `OTemplate`.
* Comprobación carpetas del framework, si no existe `app` se crea, si no existe `config` se crea...
* Permitir que no haya `config.json`
