<?php

namespace Controllers;

class Auth extends \App\Core\Auth {

    private $views;
    private $Databases;
    private $Auth;
    private $view;
    private $PostedDatas;
    
    public function __construct(\App\Router\views $views,  \App\Databases $Databases, \App\Core\Auth $Auth) {
        $this->db           = function() use ($Databases) { return $Databases->PDO($Databases->databases['website']); };
        $this->views        = $views;
        $this->Auth         = $Auth;
        $this->PostedDatas  = (isset($_POST) && !empty($_POST)? $_POST: false);
    }

    public function index($method) {
        $URL = $this->views->ParseUrl();

        if ($URL[0] == 'logout') { $this->CurrentSessionLogout(); }

        if (empty($this->views->ClientAuth)) {

            if ($URL[0] == 'login') { // --> Dont forget to change this value if you edit routes.json
                ($method == 'POST'? $this->login(): null);
                $view = function($view) { return $this->views->load('Auth/Login'); };
                
            } elseif ($URL[0] == 'register') {  // --> Dont forget to change this value if you edit routes.json
                ($method == 'POST'? $this->register(): null);
                $view = function($view) { return $this->views->load('Auth/Register'); };
            }

            $this->views->header();
            $view($this->view);
            $this->views->footer();

        } else {
            header('Location: /');
        }

    }

    private function CurrentSessionLogout() {
        $auth = $this->Auth;
        $auth->Logout(((int)$_COOKIE['SESSION']));
        header('Location: /');
    }

    private function login() {
        $user = $this->GetUserByUsername($this->PostedDatas);
        if ($user) {
            $this->SetAuthUser($user);
            header('Location: /');
        } else {
            $this->views->SetPushMessage('USER_LOGIN_ERROR');
        }
    }

    private function register() {
        // Soon :)
    }

}

?>