<?php

namespace Controllers;

class Errors {

    private $views;
    
    public function __construct(\App\Router\views $views) {
        $this->views = $views;
    }

    public function NotFound() {
        http_response_code(404);
        $this->views->Create('Errors/404');
    }

}

?>