# Guia de desarrollo seguro para v3

## Principio general

Los cambios deben ser pequenos, reversibles y verificables. No se debe mezclar una correccion funcional con una refactorizacion extensa sin pruebas que protejan el comportamiento existente.

El objetivo de compatibilidad es PHP 7.1.33 con Laravel 5.4. No usar propiedades tipadas, funciones flecha, operador nullsafe, tipos union ni otras construcciones de versiones posteriores.

## Rutas y acceso

- Colocar nuevas rutas internas dentro del grupo protegido de v3.
- Usar verbos HTTP acordes con la accion; una escritura no debe depender de GET.
- Aplicar autorizacion en servidor, no solo ocultar opciones del menu.
- Limitar paneles tecnicos y datos operativos a administradores.

## Entrada y seguridad

- Validar tipo, longitud, formato, obligatoriedad y pertenencia a la empresa.
- No concatenar valores del usuario en SQL crudo.
- No confiar en identificadores, empresa, periodo o totales enviados por la interfaz.
- No registrar claves, tokens, certificados ni solicitudes completas.

## Datos contables

- Usar transacciones cuando una operacion modifica varias tablas.
- Confirmar que debe y haber cuadren antes de confirmar un asiento.
- Conservar empresa, periodo, usuario creador/modificador y estado.
- Evitar eliminar movimientos contabilizados; aplicar la anulacion definida por negocio.
- Registrar la accion relevante en auditoria.

## Controladores, modelos y vistas

- Mantener los controladores enfocados en recibir, validar y responder.
- Extraer reglas nuevas a servicios o clases pequenas cuando sea posible.
- Evitar agregar mas responsabilidades a los modelos de miles de lineas.
- Escapar la salida en Blade; usar salida sin escapar solo para contenido confiable.
- No dejar `dd`, `dump`, `print_r`, `echo` de depuracion o `exit`.

## Verificacion minima de un cambio

1. Revisar el diff y confirmar que no incluye archivos ajenos.
2. Validar sintaxis PHP de cada archivo modificado.
3. Ejecutar pruebas automatizadas relacionadas cuando existan.
4. Probar acceso permitido y denegado.
5. Probar caso correcto, datos invalidos y error de dependencia.
6. Confirmar consistencia en MySQL y registro en auditoria.
7. Revisar `/v3/operacion-monitoreo` despues de la prueba.
8. Actualizar la documentacion afectada.
9. Registrar la revision o modificacion en `MatrizMadurez::revisiones()`, aunque el porcentaje no cambie.

## Definicion de terminado

Un cambio no esta terminado solo porque la pantalla funciona. Debe incluir validacion, autorizacion, manejo de error,
verificacion, trazabilidad, documentacion proporcional al riesgo y actualizacion de la matriz de madurez.
