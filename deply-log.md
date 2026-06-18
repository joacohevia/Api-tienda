🚀 Guía de Deploy: API PHP + Angular en Railway + Vercel
Stack: PHP 8.2 (vanilla) + MySQL + Angular
Backend: Railway.app | Frontend: Vercel
Sin Docker (usando Nixpacks nativo de Railway)

1️⃣ Preparación del repositorio
🔹 1.1 Excluir Docker de Git (pero mantenerlo local)
# Agregar al .gitignore
echo "Dockerfile" >> .gitignore
echo "docker-compose.yml" >> .gitignore
echo ".dockerignore" >> .gitignore

# Si ya estaban versionados, quitarlos del índice sin borrar localmente
git rm --cached Dockerfile docker-compose.yml .dockerignore 2>/dev/null || true

# Commit
git commit -m "chore: untrack Docker files for Railway deployment"

2️⃣ Configuración de Railway
🔹 2.1 Crear nixpacks.toml en la raíz del proyecto
# nixpacks.toml
[phases.setup]
nixPkgs = ["php", "phpExtensions.pdo_mysql"]

[start]
cmd = "php -S 0.0.0.0:$PORT router-api.php"

3️⃣ Ajustes en el código PHP
🔹 3.1 Rutas absolutas con __DIR__ en todos los require_once

// ❌ Antes (falla en Railway)
require_once 'config.php';
require_once '../helpers/JWTAuth.helper.php';

// ✅ Después (funciona siempre)
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../helpers/JWTAuth.helper.php';

🔹 3.2 Logging orientado a Railway (php://stderr)
Al inicio de router-api.php:
<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr'); // 👈 Railway lee stderr, no archivos

// Debug inicial (se ve en Railway → Logs)
error_log("✅ router-api.php STARTED | PHP: " . PHP_VERSION . " | DIR: " . __DIR__);

🔹 3.3 Manejo de URLs: soportar ?resource= y rutas limpias
Al final de router-api.php, antes de $router->route(...):

// Soporte dual: ?resource=... o URLs limpias /pedidos/usuario/123
$resource = $_GET['resource'] ?? null;

if (!$resource && isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/' && $_SERVER['PATH_INFO'] !== '') {
  $resource = trim($_SERVER['PATH_INFO'], '/');
}

if (!$resource) {
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $resource = trim($uri, '/');
  
  if ($resource === '' || $resource === 'index.php') {
    http_response_code(200);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'message' => 'API running']);
    exit;
  }
}

error_log("🔍 ROUTING: {$_SERVER['REQUEST_METHOD']} /$resource");
$router->route($resource, $_SERVER['REQUEST_METHOD']);

🔹 3.4 Normalizar rutas en Router::match() (libs/Router.php)
public function match($url, $verb) {
    $url = trim($url, '/');
    $routeUrl = trim($this->url, '/');
    
    if ($this->verb !== strtoupper($verb)) return false;
    
    $partsUrl = explode('/', $url);
    $partsRoute = explode('/', $routeUrl);
    
    if (count($partsRoute) !== count($partsUrl)) return false;
    
    foreach ($partsRoute as $i => $routePart) {
        $urlPart = $partsUrl[$i];
        
        if (str_starts_with($routePart, ':')) {
            $this->params[substr($routePart, 1)] = $urlPart;
            continue;
        }
        
        if ($routePart !== $urlPart) return false;
    }
    return true;
}

🔹 3.6 Headers CORS y JSON en router-api.php
header("Access-Control-Allow-Origin: *"); // 👈 Cambiar por tu dominio de Vercel en prod
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

🔹 4.1 En Railway → Servicio PHP → Variables
MYSQL_HOST=${{RAILWAY_PRIVATE_DOMAIN}}
MYSQL_PORT=3306
MYSQL_DATABASE=railway
MYSQL_USER=${{MYSQLUSER}}
MYSQL_PASSWORD=${{MYSQLPASSWORD}}   # 👈 Sin "_ROOT"
JWT_SECRET=tu_clave_segura_generada_con_openssl
JWT_EXPIRATION=3600
APP_DEBUG=false                      # 👈 Cambiar a true solo para debug

🔹 4.2 En config.php: leer con getenv()
function env($key, $default = null) {
    $value = getenv($key);
    return $value === false ? $default : $value;
}

define('MYSQL_HOST', env('MYSQL_HOST', '127.0.0.1'));
define('MYSQL_PORT', env('MYSQL_PORT', '3306'));
define('MYSQL_USER', env('MYSQL_USER', 'root'));
define('MYSQL_PASS', env('MYSQL_PASSWORD', ''));  // 👈 Coincide con la variable de Railway
define('MYSQL_DB', env('MYSQL_DATABASE', 'railway'));
define('JWT_SECRET', env('JWT_SECRET', 'cambiar_en_produccion'));

5️⃣ Base de datos MySQL
🔹 5.1 Crear servicio MySQL en Railway
Railway → + New → Database → MySQL
Esperar ~30 segundos a que se provisione
🔹 5.2 NO agregar variables manuales en el servicio MySQL
Railway genera automáticamente:
MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQL_DATABASE

7️⃣ Testing y debug
🔹 7.1 Comandos curl útiles
# Health check
curl -k "https://tu-api.up.railway.app/health"

# Listar categorías (URL limpia)
curl -k "https://tu-api.up.railway.app/listar"

# Con debug activado (APP_DEBUG=true)
curl -k "https://tu-api.up.railway.app/pedidos/usuario/1" | jq

🆘 Solución de problemas comunes
Problema               Solución
Failed opening required '/app/config.php' | Verificar que config.php no esté en .gitignore y esté en la raíz del repo
Access denied for user 'root'@'...' | Revisar que MYSQL_PASSWORD=${{MYSQLPASSWORD}} (no MYSQL_ROOT_PASSWORD)
Table 'railway.categorias' doesn't exist | Importar tiendaropa.sql o verificar nombre de la DB
Healthcheck falla pero API responde | Asegurar que /health está registrada como ruta pública
CORS bloquea peticiones desde Angular | Cambiar Access-Control-Allow-Origin: * por https://tu-frontend.vercel.app
Rutas limpias no funcionan | Verificar que Router::match() usa trim($url, '/')