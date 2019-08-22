<?php

namespace App;

class Databases {
    
    public $databases;
    
    public function __construct() {
        $this->databases = $this->GetDatabase();
    }

    public function PDO(array $database): object {
        return new \PDO($database['dsn'], $database['username'], $database['password'], $database['options']);
    }

    private function GetDatabase(): array {
        $parse      = file_get_contents( realpath(__DIR__ . '/..') . '/config/databases.json' );
        $parse      = json_decode($parse, true);
        $databases  = [];
        foreach ($parse as $key=>$database) {
            $database = [ 'dbname' => $database['dbname'], 'host' => $database['host'], 'charset' => $database['charset'], 'user' => $database['user'], 'password' => $database['password'] ];
            $databases = array_merge($databases, [ $key => [ 'dsn' => 'mysql:dbname=' . $database['dbname'] . ';host=' . $database['host'] . ';charset=' . $database['charset'], 'username' => $database['user'], 'password' => $database['password'], 'options' => null ] ]);
        }
        return $databases;
    }
}

?>