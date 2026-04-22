# Tienda de Ropa - API REST
API REST para gestión de una tienda de ropa. Expone endpoints para autenticación, categorías, productos, variantes, usuarios y pedidos.

**Stack:** PHP 8.2+ | MySQL 10.4+ | Apache 2.4+ (mod_rewrite)
**Arquitectura:** MVC | JWT (HMAC-SHA256) | JSON | PDO (prepared statements)

---

## Instalación

# 2. Importar base de datos
mysql -u root < database/tiendaropa.sql

# 3. Configurar credenciales en config.php
const MYSQL_USER = 'root';
const MYSQL_PASS = '';
const MYSQL_DB   = 'tiendaropa';
const JWT_SECRET = 'cambiar_en_produccion';

## Estructura del Proyecto

```
tienda/
├── config.php                  # Credenciales BD + JWT
├── router-api.php              # Registro de rutas → Controller
├── .htaccess                   # Rewrite /api/* → router-api.php
│
├── app/
│   ├── controllers/            # Lógica de cada recurso
│   │   ├── AuthController      # Login, registro, perfil, logout
│   │   ├── CategoriaController # CRUD categorías
│   │   ├── ProductoController  # CRUD productos + variantes
│   │   ├── UsuarioController   # Gestión usuarios, roles, password
│   │   └── PedidoController    # Pedidos + productos en pedido
│   ├── models/                 # Queries a BD (PDO)
│   ├── service/
│   │   └── AuthService.php     # Verificación credenciales + generación JWT
│   └── view/
│       └── Api.View.php        # Respuestas JSON + código HTTP
│
├── helpers/
│   ├── JWTAuth.helper.php      # Crear/verificar tokens JWT
│   └── validation.helper.php   # Validaciones reutilizables
│
├── libs/
│   └── Router.php              # Ruteador personalizado
│
├── database/
│   └── tiendaropa.sql          # Schema + datos iniciales
│
├── API_ENDPOINTS.md            # Documentación detallada de endpoints
└── openapi.yaml                # Especificación OpenAPI 3.0
```
## Flujo de una petición
```
Cliente HTTP
    │
    ▼
.htaccess  →  Reescribe /api/productos/1 → router-api.php?resource=productos/1
    │
    ▼
router-api.php  →  Registra rutas con: $router->addRoute(endpoint, verbo, controller, método, authRequerida)
    │                 Ejecuta: $router->route($_GET["resource"], $_SERVER['REQUEST_METHOD'])
    ▼
Router.php  →  Recorre las rutas registradas y busca coincidencia (URL + verbo HTTP)
    │            Extrae parámetros dinámicos (:id, :id_variante, etc.)
    │            Si la ruta requiere auth → verifica JWT via JWTAuth::getAuthUser()
    │            Si el token es inválido/expirado → responde 401 y corta
    ▼
Controller  →  Recibe $params con los valores de la URL
    │            Si necesita rol admin → verifica $userPayload->rol
    │            Valida datos del body (json_decode de php://input)
    │            Llama al Model correspondiente
    ▼
Model  →  Ejecuta queries con PDO (prepared statements)
    │       Retorna datos o resultado de operación
    ▼
ApiView  →  Responde JSON con código HTTP: $this->view->response($data, $statusCode)
```
---

## Autenticación JWT

Login retorna un token JWT. Para rutas protegidas, enviarlo en el header:

```
Authorization: Bearer eyJhbGciOiJIUzI1NiJ9...
```
| Nivel | Descripción |
|-------|-------------|
| 🌐 Pública | Sin token |
| 🔒 Autenticada | Token JWT válido |
| 🔒🛡️ Admin | Token + rol `admin` (verificado en controller) |

El token expira en 1 hora (`JWT_EXPIRATION` en config.php). Contiene: `id_usuario`, `email`, `rol`.
---

## Resumen de Endpoints

**URL base:** `http://localhost/tiendaRopa/tienda/api/`

| Grupo | Endpoints | Acceso |
|-------|-----------|--------|
| **Auth** | `POST auth/login` · `POST auth/registro` · `GET auth/perfil` · `PUT auth/perfil` · `GET auth/verificar` · `POST auth/logout` | 🌐 / 🔒 |
| **Categorías** | `GET listar` · `GET listar/:id` · `POST listar` · `PUT listar/:id` · `DELETE listar/:id` | 🌐 / 🔒🛡️ |
| **Usuarios** | `GET usuarios` · `GET usuarios/:id` · `DELETE usuarios/:id` · `PUT usuarios/:id/cambiar-password` · `PUT usuarios/:id/cambiar-rol` | 🔒 / 🔒🛡️ |
| **Productos** | `GET productos` · `GET productos/:id` · `GET productos/categoria/:id` · `POST productos` · `PUT productos/:id` · `PUT productos/:id/desactivar` · `DELETE productos/:id` | 🌐 / 🔒🛡️ |
| **Variantes** | `GET variantes/:id` · `POST productos/:id/variantes` · `PUT variantes/:id` · `PUT variantes/:id/stock` · `DELETE variantes/:id` | 🌐 / 🔒🛡️ |
| **Pedidos** | `GET pedidos` · `GET pedidos/:id` · `GET pedidos/usuario/:id` · `POST pedidos` · `PUT pedidos/:id` · `DELETE pedidos/:id` · `POST pedidos/:id/productos` · `PUT pedidos/producto/:id` · `DELETE pedidos/producto/:id` | 🔒 / 🔒🛡️ |

**Total: 37 endpoints** — Documentación completa en [API_ENDPOINTS.md](API_ENDPOINTS.md)

---

## Seguridad

- Contraseñas hasheadas con `PASSWORD_BCRYPT`
- Queries con PDO prepared statements (anti SQL injection)
- JWT firmado con HMAC-SHA256
- Validación de inputs (email, DNI, precios, stock)
- Control de acceso por roles en cada controller
- Headers CORS configurados en router-api.php

---

## Inicio rápido

```bash
# Registrarse
curl -X POST http://localhost/tiendaRopa/tienda/api/auth/registro \
  -H "Content-Type: application/json" \
  -d '{"nombre":"Juan","apellido":"Pérez","dni":"12345678","email":"juan@test.com","password":"123456"}'

# Login (guarda el token de la respuesta)
curl -X POST http://localhost/tiendaRopa/tienda/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"juan@test.com","password":"123456"}'

# Usar token en rutas protegidas
curl -X GET http://localhost/tiendaRopa/tienda/api/auth/perfil \
  -H "Authorization: Bearer <token>"
```

---
