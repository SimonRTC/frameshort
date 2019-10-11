<?php

namespace App;

class Databases {
    
    private $Configuration;
    public $databases;
    
    public function __construct(\App\Configuration $Configuration) {
        $this->Configuration    = $Configuration;
        $this->databases        = $this->GetDatabase();
    }

    public function PDO(array $database): object {
        return new \PDO($database['dsn'], $database['username'], $database['password'], $database['options']);
    }

    private function GetDatabase(): array {
        $databases = $this->Configuration->Databases;
        foreach ($databases as $key=>$database) {
            $database = [ 'dbname' => $database['dbname'], 'host' => $database['host'], 'charset' => $database['charset'], 'user' => $database['user'], 'password' => $database['password'] ];
            $databases = array_merge($databases, [ $key => [ 'dsn' => 'mysql:dbname=' . $database['dbname'] . ';host=' . $database['host'] . ';charset=' . $database['charset'], 'username' => $database['user'], 'password' => $database['password'], 'options' => null ] ]);
        }
        return $databases;
    }
}

?>