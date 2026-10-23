# Pruebas automatizadas de v3

## Alcance inicial

La suite esta aislada en:

- `tests/Unit/v3` para calculos y contratos sin MySQL.
- `tests/Feature/v3` para comportamiento HTTP exclusivo de rutas v3.

Todas las clases usan el grupo PHPUnit `v3`. No se modifico `phpunit.xml` ni las pruebas del sistema anterior.

El codigo y las pruebas deben ser compatibles con PHP 7.1.33 y Laravel 5.4. No se permite sintaxis introducida en PHP 7.2 o versiones posteriores.

## Cobertura inicial

- Pesos, rangos y calculo ponderado de la matriz de madurez.
- Integridad de los umbrales de monitoreo.
- Existencia y contenido minimo de la documentacion obligatoria.
- Conversion segura de Markdown para la modal documental.
- Politica de versiones y componentes protegidos durante el despliegue.
- Redireccion de las rutas administrativas v3 cuando no existe sesion.
- Contrato, acceso sin sesion y controles estaticos de las APIs v3.

Estas pruebas no escriben en MySQL.

## Ejecucion exclusiva

Desde el servidor o un ambiente que tenga el `vendor` y `bootstrap` del proyecto:

```text
php vendor/bin/phpunit --group v3
```

Antes de publicar cambios de la API tributaria, ejecutar tambien:

```text
php artisan v3:api-check --json
```

Este comando consulta esquema y muestras limitadas de documentos. No escribe datos,
no genera XML, no firma, no envia correo y no realiza solicitudes al SRI. Debe devolver
`estado: aprobado`; la salida se conserva como evidencia del despliegue.

En Windows tambien puede usarse el ejecutable correspondiente dentro de `vendor\\bin`.

Para ejecutar por directorios:

```text
php vendor/bin/phpunit tests/Unit/v3 tests/Feature/v3
```

No se recomienda ejecutar toda la suite existente para validar solamente v3, porque incluye pruebas del sistema anterior.

## Restricciones del ambiente de desarrollo

La copia Git local excluye `bootstrap` y `vendor`. Ademas, el PHPUnit global disponible es antiguo e incompatible con PHP 8.2. Por eso, en esta copia se valida sintaxis y se ejecuta directamente la logica pura; la suite PHPUnit completa debe ejecutarse en el ambiente que conserva las dependencias del proyecto.

## Siguientes pruebas

1. Autorizacion administrativa con usuario autenticado.
2. Validaciones de entrada de ventas y compras.
3. Cuadre y reverso de asientos.
4. Movimientos y stock de inventario.
5. Conciliacion bancaria.
6. Emision y anulacion de documentos electronicos.

Las pruebas que escriban datos deben usar una base exclusiva de testing, transacciones y datos ficticios. Nunca deben apuntar al MySQL de produccion.
