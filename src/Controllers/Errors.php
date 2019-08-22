<?php

namespace Controllers;

class Errors {

    private $views;
    private $ErrorsViews;
    
    public function __construct(\App\Router\views $views) {
        $this->views        = $views;
        $this->ErrorsViews  = [
            'NotFound' => 'Errors/404'
        ];
    }

    public function NotFound() {
        header('HTTP/1.1 404 Not Found');
        $this->views->header();
        $this->views->load($this->ErrorsViews[__FUNCTION__]);
        $this->views->footer();
    }

}

?>