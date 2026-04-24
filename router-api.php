<?php
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_COMPILE_ERROR])) {
        error_log("FATAL ERROR: " . $error['message'] . " in " . $error['file'] . ":" . $error['line']);
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
});
// Debug logging (se verá en Railway → Logs)
error_log("🚀 router-api.php iniciado - PID: " . getmypid());
error_log("📁 __DIR__ = " . __DIR__);
error_log("🔍 REQUEST: " . $_SERVER['REQUEST_METHOD'] . " " . ($_GET['resource'] ?? 'null'));
// Deshabilitar errores HTML en producción (para no romper JSON)
error_reporting(E_ALL);      // ← Reporta TODOS los errores
ini_set('display_errors', 0); // ← NO los muestra en el HTML/JSON (0 = apagado)
ini_set('log_errors', 1);     // ← Sí los guarda en un archivo (1 = encendido)
ini_set('error_log', 'php-errors.log'); // ← Nombre del archivo de log
ini_set('error_log', 'php://stderr');
// 🌐 HEADERS CORS para swuagger
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8"); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ . '/app/controllers/HealthController.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/libs/Router.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/CategoriaController.php';
require_once __DIR__ . '/app/controllers/UsuarioController.php';
require_once __DIR__ . '/app/controllers/PedidoController.php';
require_once __DIR__ . '/app/controllers/ProductoController.php';
//Las rutas publicas puede acceder cualquiera mientras las que necesitan
//auth solo los usuario, que a su vez se verifica que sean admin si corresponde
$router = new Router();
$router->addRoute("health", "GET", "HealthController", "check");
#                 endpoint      verbo     controller           método en el controller
//las query params son parametro que llegan desde la url y se toman por metod get desde el controller

// ==================== RUTAS DE AUTENTICACIÓN ====================
// Publicas
$router->addRoute("auth/login", "POST", "AuthController", "login");
$router->addRoute("auth/registro", "POST", "AuthController", "registro");
// Protegidas
$router->addRoute("auth/perfil", "GET", "AuthController", "perfil", true);//client
$router->addRoute("auth/perfil", "PUT", "AuthController", "actualizarPerfil", true);//client
$router->addRoute("auth/verificar", "GET", "AuthController", "verificar", true);
$router->addRoute("auth/logout", "POST", "AuthController", "logout", true);

//Rutas de categoria PUBLICAS
$router->addRoute("listar", "GET", "CategoriaController", "listar");
$router->addRoute("listar/:id", "GET", "CategoriaController", "obtener");
// Protegidas
$router->addRoute("listar", "POST", "CategoriaController", "crear", true);
$router->addRoute("listar/:id", "PUT", "CategoriaController", "actualizar", true);
$router->addRoute("listar/:id", "DELETE", "CategoriaController", "eliminar", true);

// ==================== RUTAS DE USUARIOS (protegidas, solo admin) ====================
$router->addRoute("usuarios", "GET", "UsuarioController", "listar", true);
$router->addRoute("usuarios/:id", "GET", "UsuarioController", "obtener", true);
$router->addRoute("usuarios/:id", "DELETE", "UsuarioController", "eliminar", true);
$router->addRoute("usuarios/:id/cambiar-password", "PUT", "UsuarioController", "cambiarPassword", true);//client y admin
$router->addRoute("usuarios/:id/cambiar-rol", "PUT", "UsuarioController", "cambiarRol", true);

// ==================== RUTAS DE PRODUCTOS ====================
// Publicas 
$router->addRoute("productos", "GET", "ProductoController", "listar");
$router->addRoute("productos/:id", "GET", "ProductoController", "obtener");
$router->addRoute("productos/categoria/:id", "GET", "ProductoController", "listarPorCategoria");
$router->addRoute("variantes/:id_variante", "GET", "ProductoController", "obtenerVariante");

// Protegidas 
$router->addRoute("productos", "POST", "ProductoController", "crear", true);
$router->addRoute("productos/:id", "PUT", "ProductoController", "actualizar", true);
$router->addRoute("productos/:id", "DELETE", "ProductoController", "eliminar", true);
$router->addRoute("productos/:id/desactivar", "PUT", "ProductoController", "desactivar", true);
$router->addRoute("productos/:id/variantes", "POST", "ProductoController", "crearVariante", true);
$router->addRoute("variantes/:id_variante", "PUT", "ProductoController", "actualizarVariante", true);
$router->addRoute("variantes/:id_variante", "DELETE", "ProductoController", "eliminarVariante", true);
$router->addRoute("variantes/:id_variante/stock", "PUT", "ProductoController", "actualizarStockVariante", true);

// ==================== RUTAS DE PEDIDOS (todas protegidas) ====================
$router->addRoute("pedidos", "GET", "PedidoController", "listar", true);
$router->addRoute("pedidos", "POST", "PedidoController", "crear", true);
$router->addRoute("pedidos/:id", "GET", "PedidoController", "obtener", true);
$router->addRoute("pedidos/:id", "PUT", "PedidoController", "actualizarEstado", true);
$router->addRoute("pedidos/:id", "DELETE", "PedidoController", "eliminar", true);
$router->addRoute("pedidos/usuario/:id", "GET", "PedidoController", "listarPorUsuario", true);
$router->addRoute("pedidos/:id/productos", "POST", "PedidoController", "agregarProducto", true);
$router->addRoute("pedidos/producto/:id_pedido_producto", "PUT", "PedidoController", "actualizarProducto", true);
$router->addRoute("/pedidos/producto/descontar-stock/:id_variante", "PUT", "PedidoController", "actualizarStock", true);
$router->addRoute("pedidos/producto/:id_pedido_producto", "DELETE", "PedidoController", "eliminarProducto", true);
// ejecuta la ruta (sea cual sea)
$resource = $_GET["resource"] ?? "health";
$router->route($resource, $_SERVER['REQUEST_METHOD']);
