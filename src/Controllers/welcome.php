<?php

namespace Controllers;

class welcome {

    private $views;
    private $view;
    
    public function __construct(\App\Router\views $views) {
        $this->views = $views;
        $this->view = 'welcome';
    }

    public function index() {
        $this->views->header();
        $this->views->load($this->view);
        $this->views->footer();
    }

}

?>