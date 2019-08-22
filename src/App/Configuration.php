<?php

namespace App;

class Configuration {

    public $routes;

    public function __construct() {
        $this->routes       = $this->GetRoutes();
    }
    
    private function GetRoutes(): array {
        $parse = file_get_contents( realpath(__DIR__ . '/..') . '/config/routes.json' );
        $parse = json_decode($parse, true);
        return (!$parse? []: $parse);
    }
    
}

?>