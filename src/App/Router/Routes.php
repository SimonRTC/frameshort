<?php

namespace App\Router;

class Routes {

    private $container;

    public function __construct() {
        $this->container = false;
    }

    public function SetContainer(object $container) {
        $this->container = $container;
    }

    private function GetController(): object {
        return function(string $route) {
            $class = '\Controllers\\' . $route; 
            return $this->container->get($class);
        };
    }

    public function load(string $route): object {
        $controller = $this->GetController($route);
        return $controller($route);
    }

}

?>