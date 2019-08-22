<?php

namespace Controllers;

class welcome {

    private $views;
    private $view;
    private $databases;
    
    public function __construct(\App\Router\views $views, \App\Databases $Databases) {
        $this->db           = function() use ($Databases) { return $Databases->PDO($Databases->databases['website']); };
        $this->views        = $views;
        $this->view         = 'welcome';
    }

    public function index() {
        $this->views->header();
        $this->views->load($this->view);
        $this->views->footer();
    }

}

?>