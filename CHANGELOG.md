CHANGELOG
=========

## 2.3 `(17/10/2016)`

1. Añado la posibilidad de usar campos TEXT, en vez de marcarlos como texto y ponerles tamaño grande
2. Añado referencias a otros modelos para crear las foreign keys
3. Añado modificaciones para preparar el backend (20/10/2016)

## 2.2 `(12/10/2016)`

1. Separo librerías externas a la carpeta model/lib
2. Preparo carpetas para librerías PHPMailer y TCPDF pero no las incluyo, son proyectos grandes por si solos y solo se deberían incluir si fuesen necesarios
3. Añado funciones para transacciones en ODB (commit, rollback)

## 2.1 `(11/10/2016)`

1. Añado CHANGELOG
2. Refactorizo todas las clases G_* a O*, p.e. `G_Log` por `OLog`
3. Modifico task/generateModel para que sirva para todas las clases sin tener que añadirlas a mano
