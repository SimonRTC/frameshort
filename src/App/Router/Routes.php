<?php

namespace App\Router;

class Routes {

    private $container;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        $this->container = false;
    }

    /**
     * SetContainer
     *
     * @param  object $container
     *
     * @return void
     */
    public function SetContainer(object $container) {
        $this->container = $container;
    }

    /**
     * GetController
     *
     * @return object
     */
    private function GetController(): object {
        return function(string $route) {
            $class = '\Controllers\\' . $route; 
            return $this->container->get($class);
        };
    }

    /**
     * load
     *
     * @param  string $route
     *
     * @return object
     */
    public function load(string $route): object {
        $controller = $this->GetController($route);
        return $controller($route);
    }

}

?>