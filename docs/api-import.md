# API de importación de gastos (v1)

Esta API permite que una integración externa registre gastos en Finance sin modificar la interfaz Filament. **No procesa PDFs ni conecta ChatGPT por sí sola**: el cliente extrae y revisa los movimientos, obtiene los IDs de categoría y método de pago, y después llama a esta API.

## Seguridad y despliegue

1. Publicar Finance bajo **HTTPS válido** antes de enviar tokens o datos bancarios. El sitio HTTP actual no debe usarse para estos envíos.
2. Configurar en el archivo `.env` del servidor un secreto de al menos 32 caracteres:

   ```dotenv
   FINANCE_IMPORT_API_TOKEN=PEGA_AQUI_UN_TOKEN_ALEATORIO_DE_64_CARACTERES
   ```

   Generar uno con `php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"`. No subirlo al repositorio ni pegarlo en chats.
3. Ejecutar `php artisan migrate` y después `php artisan config:cache` tras desplegar.
4. Enviar `Authorization: Bearer <TOKEN>` y `Accept: application/json` en cada petición. Todas las rutas están limitadas a 30 peticiones/minuto por IP.
5. Este primer paso usa un **token de integración validado con middleware propio**, sin agregar dependencias. Puede sustituirse por tokens con permisos individuales de Laravel Sanctum en una segunda fase.
6. Mantener la aprobación humana antes del envío definitivo. La API no hace cargos ni se conecta a bancos.

## Endpoints

| Método | Ruta | Función |
| --- | --- | --- |
| GET | `/api/categories` | IDs y nombres de categorías |
| GET | `/api/payment-methods` | IDs y nombres de métodos de pago |
| POST | `/api/bills/import/preview` | Validación y detección de duplicados sin insertar |
| POST | `/api/bills/import` | Inserta un lote ya aprobado |

Cada gasto requiere que exista un registro de ingreso (`revenues`) para su mes.

## Ejemplo: vista previa

```bash
curl --request POST 'https://finanzas.phoenixdev.mx/api/bills/import/preview' \
  --header 'Authorization: Bearer TU_TOKEN' \
  --header 'Content-Type: application/json' \
  --header 'Accept: application/json' \
  --data '{
    "items": [{
      "source": "banco-debito",
      "external_id": "mov-20261014-0001",
      "name": "Supermercado",
      "cost": "249.90",
      "date": "2026-10-14",
      "type": "Gasto",
      "category_id": 1,
      "payment_method_id": 2
    }]
  }'
```

Después de revisar el resultado, enviar el mismo JSON a `POST /api/bills/import`.

**Campos importantes:**
- `source`: alias estable de banco/cuenta sin incluir el número completo. Letras, números, guiones o guion bajo.
- `external_id`: referencia **estable y única por movimiento dentro de esa cuenta**. Preferir el ID de operación del banco; si no existe, calcular un identificador determinista a partir del estado de cuenta, la fila y el número de ocurrencia. No usar únicamente fecha + importe + concepto: dos compras legítimas podrían coincidir.
- `cost`: importe positivo de máximo 2 decimales (MXN).
- `type`: `Gasto`, `Extra` o `Ahorro`.
- `category_id` y `payment_method_id`: IDs existentes.
- Máximo 100 movimientos por petición.

## Respuestas

`preview` devuelve `summary` y `items` con un `status` por fila:

- `ready`: listo para insertar.
- `duplicate`: ya registrado con exactamente los mismos datos; se omite al importar.
- `conflict`: misma referencia, datos distintos; revisar manualmente.
- `missing_revenue`: falta crear el ingreso mensual en Finance.
- `repeated_in_batch`: el lote repite una referencia.

La importación es **atómica**: si aparece `conflict`, `missing_revenue` o `repeated_in_batch`, no inserta nada y devuelve HTTP 422. Los registros `duplicate` se omiten. En éxito, devuelve `created` y `skipped_duplicates` (HTTP 201 si creó alguno, HTTP 200 si todos eran duplicados). Si ocurre una colisión concurrente, devuelve HTTP 409 para repetir la revisión.

## Importante para la futura integración con ChatGPT

Una API pública autenticada no habilita automáticamente llamadas desde este chat. Se necesita una integración/app autorizada capaz de llamar la API. Nunca se debe compartir el token dentro del estado de cuenta o en el chat. La lectura del PDF, clasificación, exclusión de transferencias internas y confirmación de los gastos se hacen **antes** del POST de importación.
