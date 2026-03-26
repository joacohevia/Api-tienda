<?php

require_once 'helpers/JWTAuth.helper.php';

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
        if($this->verb != $verb){
            return false;
        }
        $partsURL = explode("/", trim($url,'/'));
        $partsRoute = explode("/", trim($this->url,'/'));
        if(count($partsRoute) != count($partsURL)){
            return false;
        }
        foreach ($partsRoute as $key => $part) {
            if($part[0] != ":"){
                if($part != $partsURL[$key])
                return false;
            } //es un parametro
            else
            $this->params[$part] = $partsURL[$key];
        }
        return true;
    }
    public function run(){
        // Si la ruta requiere autenticación, verificar JWT
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
    }
}

class Router {
    private $routeTable = [];
    private $defaultRoute;

    public function __construct() {
        $this->defaultRoute = null;
    }

    public function route($url, $verb) {
        //$ruta->url //no compila!
        foreach ($this->routeTable as $route) {
            if($route->match($url, $verb)){
                //TODO: ejecutar el controller//ejecutar el controller
                // pasarle los parametros
                $route->run();
                return;
            }
        }
        //Si ninguna ruta coincide con el pedido y se configuró ruta por defecto.
        if ($this->defaultRoute != null)
            $this->defaultRoute->run();
    }
    
    public function addRoute ($url, $verb, $controller, $method, $authRequired = false) {
        $this->routeTable[] = new Route($url, $verb, $controller, $method, $authRequired);
    }

    public function setDefaultRoute($controller, $method) {
        $this->defaultRoute = new Route("", "", $controller, $method);
    }
}
