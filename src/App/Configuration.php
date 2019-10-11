<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class Configuration {

    public $routes;

    public function __construct() {
        $this->routes       = $this->GetRoutes();
    }
    
    private function GetRoutes(): array {
        $parse = file_get_contents( realpath(__DIR__ . '/..') . '/config/routes.yaml' );
        $parse = Yaml::parse($parse);
        return (!$parse? []: $parse);
    }
    
}

?>