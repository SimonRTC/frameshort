<?php

namespace App\Router;

class views {

    public function __construct() {
        $this->path = realpath(__DIR__ . '/../..').'/';
    }

    public function load(string $view) {
        require $this->path . 'views/' . $view . '.php';
    }

    public function header(bool $dashboard = false) {
        require $this->path . 'components/' . (!$dashboard ? 'header': 'header_2') . '.php';
    }

    public function footer(bool $dashboard = false) {
        require $this->path . 'components/' . (!$dashboard ? 'footer': 'footer_2') . '.php';
    }

}

?>