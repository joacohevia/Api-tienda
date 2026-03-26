# 🔌 Endpoints de API - Tienda de Ropa

## 📍 URL Base
```
http://localhost/tiendaRopa/tienda/api/
```

## 🔑 Autenticación
La API utiliza **JWT (JSON Web Token)**. Para acceder a rutas protegidas, enviar el token en el header:
```
Authorization: Bearer <token>
```

### Niveles de acceso
| Icono | Nivel | Descripción |
|-------|-------|-------------|
| 🌐 | Pública | Sin autenticación |
| 🔒 | Autenticada | Requiere JWT válido |
| 🔒👤 | Usuario propio | JWT + solo su propio recurso |
| 🔒🛡️ | Admin | JWT + rol `admin` |

---

## 🔐 AUTENTICACIÓN (6 endpoints)

### 1️⃣ POST `/auth/login` 🌐
**Autenticar usuario con email y contraseña, retorna JWT**

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "usuario@ejemplo.com",
  "password": "miContraseña123"
}
```

**Respuesta (200):**
```json
{
  "mensaje": "Login exitoso",
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "usuario": {
    "id_usuario": 5,
    "nombre": "Juan",
    "apellido": "Pérez",
    "email": "usuario@ejemplo.com",
    "dni": "12345678",
    "rol": "cliente",
    "creado": "2026-03-10 10:30:00"
  }
}
```

**Errores:**
- `400`: Email o contraseña no proporcionados / formato de email inválido
- `401`: Email o contraseña incorrectos

---

### 2️⃣ POST `/auth/registro` 🌐
**Registrar nuevo usuario (siempre como `cliente`)**

```http
POST /api/auth/registro
Content-Type: application/json

{
  "nombre": "Juan",
  "apellido": "Pérez",
  "dni": "12345678",
  "email": "juan@ejemplo.com",
  "password": "password"
}
```

**Validaciones:**
- Todos los campos son obligatorios
- DNI: numérico, máximo 10 dígitos
- Email: formato válido (`FILTER_VALIDATE_EMAIL`)
- Password: mínimo 6 caracteres

**Respuesta (201):**
```json
"Usuario registrado exitosamente. Por favor inicia sesión."
```

**Errores:**
- `400`: Campo requerido faltante / DNI inválido / email inválido / password corta
- `409`: Email o DNI ya registrados

---

### 3️⃣ GET `/auth/perfil` 🔒
**Obtener perfil del usuario autenticado**

```http
GET /api/auth/perfil
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
{
  "id_usuario": 5,
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "usuario@ejemplo.com",
  "dni": "12345678",
  "rol": "cliente",
  "creado": "2026-03-10 10:30:00"
}
```

**Errores:**
- `401`: Token inválido o expirado
- `404`: Usuario no encontrado

---

### 4️⃣ PUT `/auth/perfil` 🔒
**Actualizar perfil del usuario autenticado**

```http
PUT /api/auth/perfil
Authorization: Bearer <token>
Content-Type: application/json

{
  "nombre": "Juan",
  "apellido": "García",
  "dni": "12345678",
  "email": "nuevo@ejemplo.com"
}
```

> **Nota:** El rol no puede ser modificado desde este endpoint.

**Respuesta (200):**
```json
"Perfil actualizado exitosamente"
```

**Errores:**
- `400`: Campos requeridos faltantes / formato de email inválido
- `401`: Token inválido o expirado
- `404`: Usuario no encontrado
- `409`: Email ya en uso por otro usuario

---

### 5️⃣ GET `/auth/verificar` 🔒
**Verificar estado de autenticación**

```http
GET /api/auth/verificar
Authorization: Bearer <token>
```

**Respuesta Autenticado (200):**
```json
{
  "autenticado": true,
  "usuario": {
    "id_usuario": 5,
    "email": "usuario@ejemplo.com",
    "rol": "cliente"
  }
}
```

**Respuesta Sin Token válido (200):**
```json
{
  "autenticado": false
}
```

---

### 6️⃣ POST `/auth/logout` 🔒
**Cerrar sesión (el cliente debe eliminar el token)**

```http
POST /api/auth/logout
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
{
  "mensaje": "Sesión cerrada exitosamente. Elimine el token del lado del cliente."
}
```

---

## 📂 CATEGORÍAS (5 endpoints)

### 1️⃣ GET `/listar` 🌐
**Listar todas las categorías**

```http
GET /api/listar
```

**Respuesta (200):**
```json
[
  {
    "id_categoria": 1,
    "nombre": "Remeras",
    "descripcion": "Remeras de todos los talles"
  },
  {
    "id_categoria": 2,
    "nombre": "Pantalones",
    "descripcion": "Pantalones variados"
  }
]
```

**Errores:**
- `404`: No hay categorías disponibles

---

### 2️⃣ GET `/listar/{id}` 🌐
**Obtener categoría por ID**

```http
GET /api/listar/1
```

**Respuesta (200):**
```json
{
  "id_categoria": 1,
  "nombre": "Remeras",
  "descripcion": "Remeras de todos los talles"
}
```

**Errores:**
- `400`: ID no proporcionado
- `404`: Categoría no encontrada

---

### 3️⃣ POST `/listar` 🔒🛡️
**Crear nueva categoría**

```http
POST /api/listar
Authorization: Bearer <token>
Content-Type: application/json

{
  "nombre": "Vestidos",
  "descripcion": "Vestidos elegantes"
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| nombre | string | ✅ | — |
| descripcion | string | ❌ | Default: `""` |

**Respuesta (201):**
```json
"Categoría creada exitosamente"
```

**Errores:**
- `400`: Nombre no proporcionado
- `403`: Acceso denegado (no es admin)

---

### 4️⃣ PUT `/listar/{id}` 🔒🛡️
**Actualizar categoría**

```http
PUT /api/listar/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "nombre": "Remeras Premium",
  "descripcion": "Remeras de calidad premium"
}
```

**Respuesta (200):**
```json
"Categoría actualizada exitosamente"
```

**Errores:**
- `400`: ID o nombre no proporcionados
- `403`: Acceso denegado (no es admin)
- `404`: Categoría no encontrada

---

### 5️⃣ DELETE `/listar/{id}` 🔒🛡️
**Eliminar categoría**

```http
DELETE /api/listar/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Categoría eliminada exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Categoría no encontrada

---

## 👥 USUARIOS (6 endpoints)

### 1️⃣ GET `/usuarios` 🔒🛡️
**Listar todos los usuarios**

```http
GET /api/usuarios
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
[
  {
    "id_usuario": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "dni": "12345678",
    "email": "juan@ejemplo.com",
    "rol": "cliente",
    "creado": "2026-03-10 10:30:00"
  }
]
```

**Errores:**
- `403`: Acceso denegado (no es admin)
- `404`: No hay usuarios disponibles

---

### 2️⃣ GET `/usuarios/{id}` 🔒🛡️
**Obtener usuario por ID**

```http
GET /api/usuarios/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
{
  "id_usuario": 1,
  "nombre": "Juan",
  "apellido": "Pérez",
  "dni": "12345678",
  "email": "juan@ejemplo.com",
  "rol": "cliente",
  "creado": "2026-03-10 10:30:00"
}
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Usuario no encontrado

---

### 3️⃣ PUT `/usuarios/{id}/cambiar-password` 🔒👤
**Cambiar contraseña**
> Un cliente solo puede cambiar **su propia** contraseña. Un admin puede cambiar la de **cualquier** usuario.

```http
PUT /api/usuarios/1/cambiar-password
Authorization: Bearer <token>
Content-Type: application/json

{
  "passwordActual": "pass123",
  "passwordNueva": "newpass456"
}
```

**Respuesta (200):**
```json
"Contraseña actualizada exitosamente"
```

**Errores:**
- `400`: ID, contraseña actual o nueva no proporcionados
- `401`: Contraseña actual incorrecta
- `403`: Acceso denegado (no es admin ni el propio usuario)
- `404`: Usuario no encontrado

---

### 4️⃣ PUT `/usuarios/{id}/cambiar-rol` 🔒🛡️
**Cambiar el rol de un usuario**

```http
PUT /api/usuarios/1/cambiar-rol
Authorization: Bearer <token>
Content-Type: application/json

{
  "rol": "admin"
}
```

| Campo | Tipo | Requerido | Valores válidos |
|-------|------|-----------|-----------------|
| rol | string | ✅ | `admin`, `cliente` |

**Respuesta (200):**
```json
"Rol actualizado exitosamente a admin"
```

**Errores:**
- `400`: ID o rol no proporcionados / rol inválido / usuario ya tiene ese rol
- `403`: Acceso denegado (no es admin)
- `404`: Usuario no encontrado

---

### 5️⃣ DELETE `/usuarios/{id}` 🔒🛡️
**Eliminar usuario**

```http
DELETE /api/usuarios/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Usuario eliminado exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Usuario no encontrado

---

## 📦 PRODUCTOS (7 endpoints)

### 1️⃣ GET `/productos` 🌐
**Listar todos los productos activos**

```http
GET /api/productos
```

**Respuesta (200):**
```json
[
  {
    "id_producto": 1,
    "id_categoria": 1,
    "nombre": "Remera Azul",
    "descripcion": "Remera de algodón",
    "precio_base": 49.99,
    "marca": "Nike",
    "activo": 1
  }
]
```

**Errores:**
- `404`: No hay productos disponibles

---

### 2️⃣ GET `/productos/{id}` 🌐
**Obtener producto con sus variantes**

```http
GET /api/productos/1
```

**Respuesta (200):**
```json
{
  "id_producto": 1,
  "id_categoria": 1,
  "nombre": "Remera Azul",
  "descripcion": "Remera de algodón",
  "precio_base": 49.99,
  "marca": "Nike",
  "activo": 1,
  "variantes": [
    {
      "id_variante": 1,
      "talle": "M",
      "color": "Azul",
      "precio": 49.99,
      "stock": 10
    }
  ]
}
```

**Errores:**
- `400`: ID no proporcionado
- `404`: Producto no encontrado

---

### 3️⃣ GET `/productos/categoria/{id}` 🌐
**Listar productos activos por categoría**

```http
GET /api/productos/categoria/1
```

**Respuesta (200):**
```json
[
  {
    "id_producto": 1,
    "nombre": "Remera Azul",
    "precio_base": 49.99,
    "activo": 1
  }
]
```

**Errores:**
- `400`: ID de categoría no proporcionado
- `404`: La categoría no existe

---

### 4️⃣ POST `/productos` 🔒🛡️
**Crear nuevo producto**

```http
POST /api/productos
Authorization: Bearer <token>
Content-Type: application/json

{
  "id_categoria": 1,
  "nombre": "Remera Premium",
  "descripcion": "100% algodón",
  "precio_base": 79.99,
  "marca": "Nike",
  "img": "remera.jpg",
  "activo": 1
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| id_categoria | integer | ✅ | Debe existir |
| nombre | string | ✅ | — |
| precio_base | float | ✅ | Mayor a 0 |
| descripcion | string | ❌ | Default: `""` |
| marca | string | ❌ | Default: `""` |
| img | string | ❌ | Default: `""` |
| activo | integer | ❌ | Default: `1` |

**Respuesta (201):**
```json
{
  "id_producto": 5,
  "mensaje": "Producto creado exitosamente"
}
```

**Errores:**
- `400`: Campos requeridos faltantes / precio inválido
- `403`: Acceso denegado (no es admin)
- `404`: La categoría no existe

---

### 5️⃣ PUT `/productos/{id}` 🔒🛡️
**Actualizar producto**

```http
PUT /api/productos/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "id_categoria": 1,
  "nombre": "Remera Premium Azul",
  "descripcion": "Algodón premium",
  "precio_base": 89.99,
  "marca": "Nike",
  "img": "remera_v2.jpg",
  "activo": 1
}
```

| Campo | Tipo | Requerido |
|-------|------|-----------|
| id_categoria | integer | ✅ |
| nombre | string | ✅ |
| precio_base | float | ✅ |
| descripcion | string | ❌ |
| marca | string | ❌ |
| img | string | ❌ |
| activo | integer | ❌ |

**Respuesta (200):**
```json
"Producto actualizado exitosamente"
```

**Errores:**
- `400`: Campos requeridos faltantes / precio inválido
- `403`: Acceso denegado (no es admin)
- `404`: Producto o categoría no encontrados

---

### 6️⃣ PUT `/productos/{id}/desactivar` 🔒🛡️
**Desactivar producto (soft delete)**

```http
PUT /api/productos/1/desactivar
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Producto desactivado exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Producto no encontrado

---

### 7️⃣ DELETE `/productos/{id}` 🔒🛡️
**Eliminar producto (hard delete)**

```http
DELETE /api/productos/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Producto eliminado exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Producto no encontrado

---

## 🎨 VARIANTES DE PRODUCTOS (5 endpoints)

### 1️⃣ GET `/variantes/{id_variante}` 🌐
**Obtener variante por ID**

```http
GET /api/variantes/1
```

**Respuesta (200):**
```json
{
  "id_variante": 1,
  "id_producto": 1,
  "talle": "M",
  "color": "Azul",
  "precio": 49.99,
  "stock": 10
}
```

**Errores:**
- `400`: ID no proporcionado
- `404`: Variante no encontrada

---

### 2️⃣ POST `/productos/{id}/variantes` 🔒🛡️
**Crear variante de producto**

```http
POST /api/productos/1/variantes
Authorization: Bearer <token>
Content-Type: application/json

{
  "talle": "M",
  "color": "Rojo",
  "precio": 59.99,
  "stock": 15
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| precio | float | ✅ | Mayor a 0 |
| stock | integer | ✅ | Mayor o igual a 0 |
| talle | string | ❌ | Default: `null` |
| color | string | ❌ | Default: `null` |

**Respuesta (201):**
```json
{
  "id_variante": 5,
  "mensaje": "Variante creada exitosamente"
}
```

**Errores:**
- `400`: Precio o stock inválidos
- `403`: Acceso denegado (no es admin)
- `404`: El producto no existe

---

### 3️⃣ PUT `/variantes/{id_variante}` 🔒🛡️
**Actualizar variante**

```http
PUT /api/variantes/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "talle": "L",
  "color": "Verde",
  "precio": 59.99,
  "stock": 20
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| precio | float | ✅ | Mayor a 0 |
| stock | integer | ✅ | Mayor o igual a 0 |
| talle | string | ❌ | Mantiene valor anterior si no se envía |
| color | string | ❌ | Mantiene valor anterior si no se envía |

**Respuesta (200):**
```json
"Variante actualizada exitosamente"
```

**Errores:**
- `400`: Precio o stock inválidos
- `403`: Acceso denegado (no es admin)
- `404`: Variante no encontrada

---

### 4️⃣ PUT `/variantes/{id_variante}/stock` 🔒🛡️
**Actualizar solo el stock de una variante**

```http
PUT /api/variantes/1/stock
Authorization: Bearer <token>
Content-Type: application/json

{
  "stock": 20
}
```

**Respuesta (200):**
```json
"Stock actualizado exitosamente"
```

**Errores:**
- `400`: Stock no proporcionado o negativo
- `403`: Acceso denegado (no es admin)
- `404`: Variante no encontrada

---

### 5️⃣ DELETE `/variantes/{id_variante}` 🔒🛡️
**Eliminar variante**

```http
DELETE /api/variantes/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Variante eliminada exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Variante no encontrada

---

## 🛒 PEDIDOS (9 endpoints)

### 1️⃣ GET `/pedidos` 🔒🛡️
**Listar todos los pedidos (admin)**

```http
GET /api/pedidos
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
[
  {
    "id_pedido": 10,
    "id_usuario": 5,
    "fecha": "2026-03-17 10:30:00",
    "estado": "pendiente",
    "total": 149.97
  }
]
```

**Errores:**
- `403`: Acceso denegado (no es admin)
- `404`: No hay pedidos disponibles

---

### 2️⃣ GET `/pedidos/usuario/{id}` 🔒
**Listar pedidos de un usuario**

```http
GET /api/pedidos/usuario/5
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
[
  {
    "id_pedido": 10,
    "id_usuario": 5,
    "fecha": "2026-03-17 10:30:00",
    "estado": "pendiente",
    "total": 149.97
  }
]
```

**Errores:**
- `400`: ID de usuario no proporcionado
- `500`: Error al obtener pedidos

---

### 3️⃣ GET `/pedidos/{id}` 🔒
**Obtener pedido con detalles (productos, usuario)**

```http
GET /api/pedidos/10
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
{
  "pedido": {
    "id_pedido": 10,
    "id_usuario": 5,
    "fecha": "2026-03-17 10:30:00",
    "estado": "pendiente",
    "total": 149.97
  },
  "usuario": {
    "id_usuario": 5,
    "nombre": "Juan",
    "email": "juan@ejemplo.com"
  },
  "productos": [
    {
      "id_pedido_producto": 1,
      "id_variante": 1,
      "cantidad": 2,
      "precio_unitario": 49.99,
      "subtotal": 99.98,
      "producto": {
        "nombre": "Remera Azul",
        "talle": "M",
        "color": "Azul"
      }
    }
  ]
}
```

**Errores:**
- `400`: ID no proporcionado
- `404`: Pedido no encontrado

---

### 4️⃣ POST `/pedidos` 🔒
**Crear nuevo pedido**

```http
POST /api/pedidos
Authorization: Bearer <token>
Content-Type: application/json

{
  "id_usuario": 5,
  "estado": "carrito"
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| id_usuario | integer | ✅ | Debe existir en la BD |
| estado | string | ❌ | Default: `carrito`. Valores: `carrito`, `pendiente`, `pagado`, `enviado`, `cancelado` |

**Respuesta (201):**
```json
{
  "id_pedido": 10,
  "mensaje": "Pedido creado exitosamente"
}
```

**Errores:**
- `400`: ID de usuario no proporcionado / estado inválido
- `404`: El usuario no existe

---

### 5️⃣ PUT `/pedidos/{id}` 🔒🛡️
**Actualizar estado del pedido**

```http
PUT /api/pedidos/10
Authorization: Bearer <token>
Content-Type: application/json

{
  "estado": "pagado"
}
```

**Estados válidos:** `carrito`, `pendiente`, `pagado`, `enviado`, `cancelado`

**Respuesta (200):**
```json
"Estado del pedido actualizado exitosamente"
```

**Errores:**
- `400`: ID o estado no proporcionados / estado inválido
- `403`: Acceso denegado (no es admin)
- `404`: Pedido no encontrado

---

### 6️⃣ DELETE `/pedidos/{id}` 🔒🛡️
**Eliminar pedido**

```http
DELETE /api/pedidos/10
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
"Pedido eliminado exitosamente"
```

**Errores:**
- `400`: ID no proporcionado
- `403`: Acceso denegado (no es admin)
- `404`: Pedido no encontrado

---

### 7️⃣ POST `/pedidos/{id}/productos` 🔒
**Agregar producto al pedido**

```http
POST /api/pedidos/10/productos
Authorization: Bearer <token>
Content-Type: application/json

{
  "id_variante": 1,
  "cantidad": 2,
  "precio_unitario": 49.99
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| id_variante | integer | ✅ | Debe existir |
| cantidad | integer | ✅ | Mayor a 0 |
| precio_unitario | float | ✅ | Mayor a 0 |

**Validaciones adicionales:**
- La variante debe existir
- Debe haber stock disponible suficiente

**Respuesta (201):**
```json
{
  "mensaje": "Producto agregado al pedido exitosamente",
  "nuevo_total": 99.98
}
```

**Errores:**
- `400`: Campos requeridos faltantes / cantidad o precio inválidos / stock insuficiente
- `404`: Pedido no encontrado / variante no existe

---

### 8️⃣ PUT `/pedidos/producto/{id_pedido_producto}` 🔒
**Actualizar cantidad de un producto en el pedido**

```http
PUT /api/pedidos/producto/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "cantidad": 3
}
```

| Campo | Tipo | Requerido | Nota |
|-------|------|-----------|------|
| cantidad | integer | ✅ | Mayor o igual a 0 |

**Respuesta (200):**
```json
{
  "mensaje": "Producto del pedido actualizado exitosamente",
  "nuevo_total": 149.97
}
```

**Errores:**
- `400`: ID no proporcionado / cantidad inválida
- `404`: Producto del pedido no encontrado

---

### 9️⃣ DELETE `/pedidos/producto/{id_pedido_producto}` 🔒
**Eliminar producto del pedido**

```http
DELETE /api/pedidos/producto/1
Authorization: Bearer <token>
```

**Respuesta (200):**
```json
{
  "mensaje": "Producto eliminado del pedido exitosamente",
  "nuevo_total": 0
}
```

**Errores:**
- `400`: ID no proporcionado
- `404`: Producto del pedido no encontrado

---

## 📊 Resumen de Endpoints

| Categoría | GET | POST | PUT | DELETE | Total |
|-----------|-----|------|-----|--------|-------|
| Autenticación | 2 | 3 | 1 | - | 6 |
| Categorías | 2 | 1 | 1 | 1 | 5 |
| Usuarios | 2 | - | 2 | 1 | 5 |
| Productos | 3 | 1 | 2 | 1 | 7 |
| Variantes | 1 | 1 | 2 | 1 | 5 |
| Pedidos | 3 | 2 | 2 | 2 | 9 |
| **TOTAL** | **13** | **8** | **10** | **6** | **37** |

---

## 🔑 Códigos HTTP Utilizados

| Código | Significado | Cuándo |
|--------|-------------|--------|
| `200` | OK | Operación exitosa |
| `201` | Created | Recurso creado exitosamente |
| `400` | Bad Request | Datos inválidos, incompletos o lógica de negocio violada |
| `401` | Unauthorized | Token inválido/expirado o credenciales incorrectas |
| `403` | Forbidden | No tiene permiso (rol insuficiente o recurso ajeno) |
| `404` | Not Found | Recurso no existe |
| `409` | Conflict | Violación de unicidad (email/DNI ya existe) |
| `500` | Server Error | Error interno del servidor |

---

**Última actualización:** 25 de marzo de 2026
