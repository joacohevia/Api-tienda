<?php

require_once __DIR__ . '/../helpers/JWTAuth.helper.php';
class Route {
    private $url;
    private $verb;
    private $controller;
    private $method;
    private $params;
    private $authRequired;

    public function __construct($url, $verb, $controller, $method, $authRequired = false){
        $this->url = $url;
        $this->verb = $verb;
        $this->controller = $controller;
        $this->method = $method;
        $this->params = [];
        $this->authRequired = $authRequired;
    }
    public function match($url, $verb) {
    $url = trim($url, '/');
    $routeUrl = trim($this->url, '/');
    
    if ($this->verb !== strtoupper($verb)) {
        return false;
    }
    
    $partsUrl = explode('/', $url);
    $partsRoute = explode('/', $routeUrl);
    
    if (count($partsRoute) !== count($partsUrl)) {
        return false;
    }
    
    foreach ($partsRoute as $i => $routePart) {
        $urlPart = $partsUrl[$i];
        
        if (str_starts_with($routePart, ':')) {
            $this->params[$routePart] = $urlPart; // ← con ":" para compatibilidad
            continue;
        }
        
        if ($routePart !== $urlPart) {
            return false;
        }
    }
    
    return true;
    }

public function run() {
    try {
        if($this->authRequired){
            $userPayload = JWTAuth::getAuthUser();
            if(!$userPayload){
                header("Content-Type: application/json");
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(['error' => 'Token inválido, expirado o no proporcionado']);
                return;
            }
        }

        $controller = $this->controller;  
        $method = $this->method;
        $params = $this->params;
       
        (new $controller())->$method($params);
    } catch (Throwable $e) {
        error_log("Route run error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
}
}

class Router {
    private $routeTable = [];
    private $defaultRoute;

    public function __construct() {
        $this->defaultRoute = null;
    }

   public function route($url, $verb) {
    try {
        foreach ($this->routeTable as $route) {
            if($route->match($url, $verb)){
                $route->run();
                return;
            }
        }
        
        if ($this->defaultRoute != null){
            $this->defaultRoute->run();
            return;
        }
        
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Endpoint not found',
            'requested' => "$verb /$url",
            'available_routes' => array_map(fn($r) => "{$r->verb} /{$r->url}", $this->routeTable)
        ]);
        exit;
    } catch (Throwable $e) {
        error_log("Router error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Internal server error']);
        exit;
    }
}
    
    public function addRoute ($url, $verb, $controller, $method, $authRequired = false) {
        $this->routeTable[] = new Route($url, $verb, $controller, $method, $authRequired);
    }

    public function setDefaultRoute($controller, $method) {
        $this->defaultRoute = new Route("", "", $controller, $method);
    }
}
