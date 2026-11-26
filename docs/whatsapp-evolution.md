# WhatsApp con Evolution API

## Configuración

El módulo usa exclusivamente Evolution API v2 con la instancia `delicias`.

```dotenv
WHATSAPP_ENABLED=false
WHATSAPP_PROVIDER=evolution
EVOLUTION_API_URL=http://127.0.0.1:8080
EVOLUTION_API_KEY=...
EVOLUTION_INSTANCE=delicias
EVOLUTION_TIMEOUT=15
EVOLUTION_WEBHOOK_SECRET=...
```

No guardar la API key en el repositorio. En producción, `EVOLUTION_API_URL` debe ser accesible desde el servidor Laravel y Evolution debe poder alcanzar la URL pública del webhook.

## Instalación

```bash
php artisan migrate --force
php artisan db:seed --class=WhatsappSeeder --force
php artisan config:clear
```

La migración crea mensajes, plantillas, automatizaciones, ajustes, eventos de webhook y los campos de consentimiento de pedidos. El seeder crea la plantilla de reseña y deja la automatización global deshabilitada si `WHATSAPP_ENABLED=false`.

## Cola

Los mensajes automáticos y las pruebas manuales se encolan en `whatsapp`. En un servidor con Supervisor:

```ini
[program:delicias-whatsapp]
command=php /ruta/a/delicias/artisan queue:work whatsapp --queue=whatsapp --sleep=3 --tries=3 --timeout=120
directory=/ruta/a/delicias
autostart=true
stopwaitsecs=3600
```

En Hostinger sin Supervisor, configurar un cron cada minuto:

```cron
* * * * * cd /ruta/a/delicias && php artisan queue:work whatsapp --queue=whatsapp --stop-when-empty --tries=3 --timeout=120 >/dev/null 2>&1
```

## Webhook

La conexión desde el panel configura automáticamente:

```text
POST /webhooks/evolution
```

Si se define `EVOLUTION_WEBHOOK_SECRET`, la URL configurada incluye `?token=...`. El endpoint registra cada evento una sola vez, guarda mensajes entrantes de texto y actualiza el estado de mensajes salientes cuando Evolution informa una confirmación.

## Flujo automático

1. El checkout exige consentimiento explícito para comunicaciones WhatsApp y guarda fecha e IP.
2. Al cambiar un pedido de pendiente a entregado, el sistema busca la automatización activa.
3. Se crea un registro idempotente de mensaje y se despacha `SendWhatsappMessageJob` en la cola `whatsapp`.
4. El Job envía `POST /message/sendText/{instance}` y reintenta hasta tres veces.
5. `review_invite_sent_at` solo se completa después de una respuesta exitosa de Evolution.

Mientras `WHATSAPP_ENABLED=false`, los pedidos no generan envíos automáticos. El panel permite activar o desactivar las automatizaciones globalmente.

## Verificación local

Desde el panel privado de WhatsApp se puede consultar el estado, generar QR, conectar o desconectar la instancia, editar plantillas, probar mensajes y revisar errores/reintentos. Los tests usan `Http::fake()` y no envían mensajes reales.
