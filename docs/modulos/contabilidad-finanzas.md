# Contexto: Contabilidad y finanzas

## Estado

Mapeado. Los asientos, balances y procesos financieros siguen en el legado.

## Uso

Administra plan de cuentas, diarios, asientos, cartera, bancos, balances, activos, depreciaciones, amortizaciones y planeación financiera.

### Módulos incluidos

- Contabilidad.
- Activos fijos.
- Planeación financiera.
- Amortizaciones.

## Límite técnico

Será propietario del libro contable. Facturación y otros contextos solicitarán la contabilización mediante contratos o eventos; no insertarán detalles contables directamente una vez migrada la operación correspondiente.

Planeación financiera consumirá proyecciones y saldos mediante modelos de lectura, sin depender de controladores contables.

## Soporte

- Verificar cabecera, detalles, empresa, período y balance entre debe y haber.
- No desactivar `ONLY_FULL_GROUP_BY`; corregir las consultas incompatibles.
- Antes de cambiar una cuenta o asiento, revisar cierres y documentos de origen.

## Criterio de migración

Comenzar por consultas y reportes, continuar con generación de asientos por contrato y dejar cierres o reprocesos para la última etapa.
