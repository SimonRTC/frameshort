<?php

namespace Controllers;

class welcome {

    private $views;
    private $view;
    private $databases;
    
    public function __construct(\App\Router\views $views) {
        $this->views        = $views;
    }

    public function index() {
        $this->views->header();
        $this->views->load('welcome');
        $this->views->footer();
    }

}

?>