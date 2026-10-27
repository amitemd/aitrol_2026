# Contexto: Talento humano

## Estado

En migración. El directorio y la búsqueda de empleados ya atraviesan un caso de uso y un adaptador modular; los procesos de nómina permanecen en el legado.

## Uso

Administra empleados, información laboral, horarios, áreas, catálogos de nómina, encuestas y procesos relacionados con talento humano.

### Módulos incluidos

- Recursos humanos.
- Mantenimiento de nómina.

## Límite técnico

Será propietario del perfil laboral y los procesos de nómina. Identidad proporcionará la cuenta de acceso, pero no modificará información laboral. Contabilidad recibirá resultados contabilizables mediante contratos definidos.

## Operación migrada: directorio de empleados

La ruta y la vista existentes delegan la lectura a `ListEmployees` mediante `EmployeeDirectoryRepository`. El adaptador consulta la tabla actual `empleado`, selecciona únicamente los campos visibles y pagina de forma estable por apellido, nombre e identificación.

La búsqueda se envía por `POST`, valida cédula y nombres, guarda temporalmente el filtro en la sesión y redirige al listado. Las variables de búsqueda no quedan expuestas en la URL y la paginación conserva el filtro.

La autorización heredada para los roles 1 y 8 ahora se aplica efectivamente en listado, búsqueda, documentos, creación, edición y actualización.

### Riesgo y reversión

La operación modular es de lectura y no altera empleados. Los formularios de creación y actualización siguen usando el flujo heredado. No se añadió ninguna tabla ni migración de datos.

## Soporte

- Distinguir cuenta de usuario de registro de empleado.
- Revisar estado laboral, empresa, área, horario y período ante diferencias de nómina.
- Restringir el acceso a información personal según permisos y auditoría existentes.

## Criterio de migración

Extraer primero catálogos y consultas de empleados; después cálculos de nómina con casos conocidos y conciliación contra resultados del legado.
