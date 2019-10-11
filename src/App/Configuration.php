<?php

namespace App;

use Symfony\Component\Yaml\Yaml;

class Configuration {

    private $Path;
    public $routes;
    public $Databases;

    public function __construct() {
        $this->Path         = realpath(__DIR__ . '/..');
        $this->routes       = $this->GetRoutes();
        $this->Databases    = $this->GetDatabases();
    }

    private function GetDatabases(): ?array {
        $parse = file_get_contents( $this->Path . '/config/databases.json' );
        $parse = json_decode($parse, true);
        $parse = $this->ParseComments($parse);
        return (!$parse? null: $parse);
    }
    
    private function GetRoutes(): ?array {
        $parse = file_get_contents( $this->Path . '/config/routes.yaml' );
        $parse = Yaml::parse($parse);
        return (!$parse? null: $parse);
    }
    
    public function ParseComments(?array $parses): ?array {
        foreach ($parses as $key=>$parse) {
            foreach ($parse as $k=>$p) {
                if ($k == '_comment') {
                    unset($parses[$key][$k]);
                }
            }
        }
        return $parses;
    }

}

?>