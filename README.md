# API Print - Microservicio de Impresión Térmica

## 📋 Descripción

**API Print** es un microservicio REST desarrollado con Laravel 8 que permite controlar impresoras térmicas ESC/POS conectadas en red. Diseñado específicamente para integración con impresoras Zebra ZPL (Zebra Programming Language), facilita la automatización de impresión de recibos, etiquetas de envío y documentos desde aplicaciones externas como sistemas de punto de venta (POS) o plataformas de e-commerce.

## 🚀 Tipo de Proyecto

**Microservicio API REST** - Gestión de Impresión Térmica en Red

## 🛠️ Tecnologías Utilizadas

- **Laravel 8.75+** - Framework PHP MVC
- **PHP 7.3+ / 8.0+** - Lenguaje backend
- **MySQL 5.7+** - Base de datos
- **mike42/escpos-php 2.2** - Librería de impresión ESC/POS
- **Guzzle HTTP 7.0** - Cliente HTTP
- **Laravel Sanctum 2.11** - Autenticación API basada en tokens

## 📚 Frameworks y Librerías

### Backend
- **Laravel 8** - Framework MVC principal
- **escpos-php (mike42)** - Comunicación con impresoras térmicas
  - Compatible con ESC/POS
  - Soporte Zebra ZPL
  - Conexiones Windows SMB
- **Fruitcake CORS** - Manejo de CORS para APIs

### Autenticación
- **Laravel Sanctum** - Token-based API authentication (configurado)

### Testing
- **PHPUnit 9.5+** - Framework de testing unitario
- **Mockery** - Librería de mocking
- **Faker** - Generación de datos de prueba

### DevOps
- **Laravel Sail** - Entorno Docker simplificado
- **Laravel Mix 6** - Compilación de assets
- **StyleCI** - Análisis automático de código

## 🏗️ Arquitectura

### Patrón Arquitectónico: MVC + API REST

```
┌────────────────────────────────┐
│    Cliente (Aplicación POS)    │
│     Sistema E-commerce         │
└───────────────┬────────────────┘
                │ HTTP POST (JSON)
                ↓
┌───────────────┴────────────────┐
│   Laravel Router (api.php)     │
│   POST /api/print-receipt      │
└───────────────┬────────────────┘
                │
                ↓
┌───────────────┴────────────────┐
│    PrintController             │
│    - Validar JSON              │
│    - Leer template ZPL         │
│    - Sustituir variables       │
└───────────────┬────────────────┘
                │
                ↓
┌───────────────┴────────────────┐
│    escpos-php Library          │
│    - Conectar a impresora      │
│    - Enviar comandos ESC/POS   │
└───────────────┬────────────────┘
                │
                ↓
┌───────────────┴────────────────┐
│  Impresora Térmica (Red)       │
│  Zebra ZD230-203dpi ZPL        │
│  smb://192.168.139.1/          │
└────────────────────────────────┘
```

## 📁 Estructura del Proyecto

```
api-print/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── PrintController.php    # Controlador principal
│   ├── Models/
│   │   └── User.php
│   └── Providers/
├── config/
│   ├── app.php
│   ├── database.php
│   └── sanctum.php
├── database/
│   └── migrations/                    # Migraciones estándar
├── public/
│   ├── lbl/
│   │   └── label.zpl                  # Template ZPL
│   └── index.php
├── routes/
│   ├── api.php                        # Ruta principal de API
│   └── web.php
├── storage/
│   └── logs/
├── tests/
│   ├── Feature/
│   └── Unit/
├── composer.json
├── package.json
├── phpunit.xml
└── print.sql                          # Dump de base de datos
```

## ✨ Características Principales

### 🖨️ Impresión Basada en Templates
- Utiliza templates ZPL almacenados en `public/lbl/label.zpl`
- Reemplazo dinámico de placeholders
- Soporta variables personalizadas:
  - `[[PARA]]` - Destinatario
  - `[[DE]]` - Remitente
  - `[[DIRECCION]]` - Dirección de envío
  - `[[IDPEDIDO]]` - ID del pedido

### 📡 API Endpoint Único

**POST /api/print-receipt**

Parámetros JSON:
```json
{
  "nombre": "Juan García",
  "direccion": "Calle 123, Apt 4",
  "cliente": "Empresa ABC",
  "idPedido": "PED-20241114-001",
  "cantidad": 2
}
```

Respuesta exitosa:
```json
{
  "correctProcess": true,
  "message": "Impresión realizada correctamente"
}
```

Respuesta con error:
```json
{
  "correctProcess": false,
  "message": "Descripción del error"
}
```

### 🔌 Conexión Multiplex
- Conecta vía SMB/Windows Share
- Ruta: `smb://192.168.139.1/ZDesigner ZD230-203dpi ZPL`
- Soporte para múltiples impresiones (loop configurable)
- Perfil: SP2000 (Sunmi o similar)

### 🔒 Seguridad
- **CORS habilitado** para acceso cross-origin
- **Rate Limiting** (Throttle) en rutas API
- **CSRF Protection** en rutas web
- **Laravel Sanctum** configurado (opcional)

### 🎯 Casos de Uso
- Impresión automática de etiquetas de envío
- Generación de recibos en puntos de venta
- Impresión distribuida en red corporativa
- Integración con sistemas de fulfillment

## 🔧 Instalación

### Prerrequisitos

- PHP 7.3 o superior (recomendado 8.0+)
- Composer
- MySQL 5.7+
- Impresora térmica ESC/POS o Zebra ZPL
- Extensión PHP cURL habilitada

### Pasos

1. Clonar el repositorio
```bash
git clone https://github.com/dannyggg3/api-print.git
cd api-print
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar base de datos en `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=print
DB_USERNAME=root
DB_PASSWORD=
```

5. Ejecutar migraciones
```bash
php artisan migrate
```

6. Configurar impresora
Editar `app/Http/Controllers/PrintController.php`:
```php
$connector = new WindowsPrintConnector("smb://TU_IP/TU_IMPRESORA");
```

7. Crear template ZPL
Colocar archivo `label.zpl` en `public/lbl/` con placeholders:
```zpl
^XA
^FO50,50^A0N,40,40^FD[[PARA]]^FS
^FO50,100^A0N,30,30^FD[[DE]]^FS
^FO50,150^A0N,25,25^FD[[DIRECCION]]^FS
^FO50,200^A0N,25,25^FDPedido: [[IDPEDIDO]]^FS
^XZ
```

8. Iniciar servidor
```bash
php artisan serve
# Servidor corriendo en http://localhost:8000
```

## 💻 Uso

### Ejemplo con cURL

```bash
curl -X POST http://localhost:8000/api/print-receipt \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Juan García",
    "direccion": "Av. Principal 123, Piso 4",
    "cliente": "Tienda ABC",
    "idPedido": "ORD-2024-001",
    "cantidad": 2
  }'
```

### Ejemplo con JavaScript (Axios)

```javascript
const axios = require('axios');

axios.post('http://localhost:8000/api/print-receipt', {
  nombre: 'Juan García',
  direccion: 'Av. Principal 123, Piso 4',
  cliente: 'Tienda ABC',
  idPedido: 'ORD-2024-001',
  cantidad: 2
})
.then(response => {
  console.log('Impresión exitosa:', response.data);
})
.catch(error => {
  console.error('Error:', error.response.data);
});
```

### Ejemplo con PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client();

$response = $client->post('http://localhost:8000/api/print-receipt', [
    'json' => [
        'nombre' => 'Juan García',
        'direccion' => 'Av. Principal 123, Piso 4',
        'cliente' => 'Tienda ABC',
        'idPedido' => 'ORD-2024-001',
        'cantidad' => 2
    ]
]);

$result = json_decode($response->getBody(), true);
```

## 🔌 API Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `/api/print-receipt` | Imprime recibo/etiqueta con datos JSON |

### Middleware Aplicado
- `api` (CORS, Throttle, JSON validation)

## 🧪 Testing

```bash
# Ejecutar tests unitarios
php artisan test

# Con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter=PrintControllerTest
```

## 📊 Configuración de Impresora

### Impresoras Soportadas
- Zebra ZDesigner ZD230 (203 dpi)
- Zebra ZD410
- Cualquier impresora ESC/POS
- Impresoras compatibles con perfil SP2000

### Tipos de Conexión
1. **Windows SMB Share**
```php
$connector = new WindowsPrintConnector("smb://192.168.1.100/ZebraPrinter");
```

2. **Network (TCP/IP)**
```php
$connector = new NetworkPrintConnector("192.168.1.100", 9100);
```

3. **USB (Linux)**
```php
$connector = new FilePrintConnector("/dev/usb/lp0");
```

## 🎯 Personalización de Templates

### Variables Disponibles
- `[[PARA]]` - Nombre del destinatario
- `[[DE]]` - Nombre del remitente
- `[[DIRECCION]]` - Dirección completa
- `[[IDPEDIDO]]` - Identificador del pedido
- `[[CLIENTE]]` - Nombre del cliente

### Agregar Nuevas Variables

1. Editar template `public/lbl/label.zpl`:
```zpl
^FO50,250^A0N,25,25^FDTeléfono: [[TELEFONO]]^FS
```

2. Actualizar controlador:
```php
$template = str_replace('[[TELEFONO]]', $request->telefono, $template);
```

## 📈 Estadísticas del Proyecto

| Métrica | Valor |
|---------|-------|
| Líneas de código core | ~56 (PrintController) |
| Controllers | 1 funcional |
| Models | 1 (User estándar) |
| API Routes | 1 operacional |
| Dependencias PHP | 6 principales |
| Versión Laravel | 8.75+ |

## 🚀 Despliegue en Producción

### Con Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

### Con Servidor Tradicional

1. Configurar virtual host
2. Apuntar document root a `/public`
3. Configurar permisos:
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

4. Optimizar para producción:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔧 Troubleshooting

### Error: "Could not connect to printer"

**Causa:** La impresora no está accesible en la red

**Solución:**
```bash
# Verificar conectividad
ping 192.168.139.1

# Verificar share de Windows
smbclient -L //192.168.139.1 -U username
```

### Error: "Template not found"

**Causa:** Falta el archivo `label.zpl`

**Solución:**
```bash
mkdir -p public/lbl
# Crear template ZPL en public/lbl/label.zpl
```

### Error: "Class 'Mike42\Escpos\...' not found"

**Causa:** Dependencias no instaladas

**Solución:**
```bash
composer install
composer dump-autoload
```

## 🛠️ Mejoras Futuras

- [ ] Soporte para múltiples templates
- [ ] Cola de impresión con Laravel Queues
- [ ] Dashboard web para gestión
- [ ] Autenticación obligatoria con Sanctum
- [ ] Logs de impresión en base de datos
- [ ] Soporte para impresión de imágenes/logos
- [ ] API para consultar estado de impresora
- [ ] Generación dinámica de códigos QR/Barcode

## 📄 Licencia

Este proyecto es parte del portafolio de desarrollo de dannyggg3.

## 👤 Autor

**dannyggg3**
- GitHub: [@dannyggg3](https://github.com/dannyggg3)

---

⭐ Si este proyecto te fue útil, considera darle una estrella
