<?php

namespace Controllers;

class Errors {

    private $views;
    private $ErrorsViews;
    
    public function __construct(\App\Router\views $views) {
        $this->views        = $views;
        $this->ErrorsViews  = [
            'NotFound'  => 'Errors/404',
            'Forbidden' => 'Errors/403'
        ];
    }

    public function Forbidden() {
        http_response_code(403);
        $this->CreateView('Forbidden');
    }

    public function NotFound() {
        http_response_code(404);
        $this->CreateView('NotFound');
    }

    private function CreateView(string $view) { 
        $this->views->header();
        $this->views->load($this->ErrorsViews[$view]);
        $this->views->footer();
     }

}

?>