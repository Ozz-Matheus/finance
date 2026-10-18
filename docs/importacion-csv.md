# Importar y exportar gastos CSV desde Filament

En **Gastos** aparecen los botones **Importar CSV** y **Exportar CSV**. Además, dentro de **Ingresos & Extras > Ver un ingreso mensual > Gastos**, encontrarás **Importar CSV del mes** y **Exportar CSV del mes**.

### Trabajar con un mes, por ejemplo septiembre de 2026

- **Desde Ingresos & Extras:** abre **septiembre de 2026** y, en el manejador de gastos, usa **Exportar CSV del mes**. Se descargan todos los gastos asociados a ese ingreso mensual, **no solo la página visible**.
- **Importar CSV del mes:** desde ese mismo ingreso se abre la página de importación con **2026-09** como período obligatorio. Si el CSV incluye cualquier gasto de otra fecha mensual, se rechaza el archivo completo antes de escribir.
- **Desde Gastos (Bills):** pulsa **Exportar CSV** y elige **Mes y año: Septiembre 2026** en el formulario. Opcionalmente combina fechas, categoría o método de pago.
- En el listado de Gastos también se añadió un filtro **Mes y año** para ver los movimientos del período. Los filtros del listado y los del cuadro de exportación son **independientes**.
- Si no seleccionas ningún criterio en **Exportar CSV** de Bills, se descargarán **todos los gastos de la base de datos**, no solo los visibles en la tabla.



## Importar

1. Ve a **Gastos > Importar CSV > Seleccionar CSV y validar**.
2. Revisa la vista previa: movimientos listos, duplicados, conflictos y meses sin ingreso.
3. Si no hay bloqueos, pulsa **Confirmar importación**. Solo entonces se guarda algo.

El archivo debe ser **UTF-8**, separado por comas (también acepta punto y coma), con estas columnas **en este orden**:

~~~csv
source,external_id,name,cost,date,type,category_id,payment_method_id
nu-credito,sep26-0001,"Mercado, supermercado",249.90,2026-09-12,Gasto,3,2
nu-debito,sep26-0002,Google One,99.00,2026-09-25,Extra,2,6
~~~

Los IDs del ejemplo deben ajustarse a tu base de datos.

- source: alias estable, como nu-credito, nu-debito o albo-mama.
- external_id: referencia estable única dentro de la fuente. Consérvala al reimportar para evitar duplicados.
- name: concepto legible.
- cost: importe positivo con punto decimal y máximo 2 decimales, sin símbolos ni separador de miles.
- date: fecha AAAA-MM-DD.
- type: exactamente Gasto, Extra o Ahorro.
- category_id y payment_method_id: IDs existentes en Finance.

Máximo **500 movimientos** y **2 MB** por archivo. El mes de cada gasto debe tener un ingreso mensual registrado.

La vista previa **no guarda datos**. Al confirmar se vuelve a validar y se usa la misma transacción que la API. Si existe conflicto, mes sin ingreso o referencias repetidas dentro del lote, no se importa nada. Los duplicados exactos se omiten.

## Exportar

En **Gastos > Exportar CSV** puedes seleccionar mes y año, filtrar por fecha desde/hasta, categoría y método de pago. Sin filtros descarga todos los gastos. El archivo incluye las mismas ocho columnas, con codificación UTF-8 y BOM para Excel.

**Gastos manuales antiguos:** Los registros creados antes de la integración pueden no tener source ni external_id. Se exportan, pero con esos dos campos vacíos. Son útiles para análisis y reporte, pero antes de reimportarlos necesitarías asignar identificadores estables; reimportar sin ellos falla para evitar duplicados accidentales. El CSV no sustituye un respaldo SQL.

## Pruebas locales

~~~bash
php artisan test --filter=BillCsvImportTest
php artisan test --filter=BillImportApiTest
~~~

No hacen falta nuevas migraciones para usar los dos botones. Se conserva la API existente para integraciones futuras.
