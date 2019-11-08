<?php

namespace Controllers;

class Auth extends \App\Core\Auth {

    private $views;
    private $Databases;
    private $Auth;
    private $view;
    
    public function __construct(\App\Router\views $views,  \App\Databases $Databases, \App\Core\Auth $Auth) {
        $this->db           = function() use ($Databases) { return $Databases->PDO($Databases->databases['website']); };
        $this->views        = $views;
        $this->Auth         = $Auth;
    }

    public function signin($subsite, $method) {
        $this->views->SetProtectedPage(false, '/');
        $this->views->SetSubsite($subsite);
        ($method == 'POST'? $this->login(): null);
        $this->views->Create('Auth/Login');
    }

    public function signup($subsite, $method) {
        $this->views->SetProtectedPage(false, '/');
        $this->views->SetSubsite($subsite);
        ($method == 'POST'? $this->register(): null);
        $this->views->Create('Auth/Register');
    }

    public function SessionLogout(): bool {
        $auth = $this->Auth;
        $auth->Logout(((int)$_COOKIE['SESSION']));
        header('Location: /');
        return true;
    }

    private function login() {
        $user = $this->GetUserByUsername($_POST);
        if ($user) {
            $cb = $this->SetAuthUser($user);
            if (!empty($cb)) {
                header('Location: /');
            } else {
                $this->views->Pusher->SetNotification('YOU_ARE_BANNED');
            }
        } else {
            $this->views->Pusher->SetNotification('USER_LOGIN_ERROR');
        }
    }

    public function register() {
        $cb = $this->AddClient($_POST);
        if ($cb != 'ALREADY_TAKEN' && $cb != 'REGISTER_FAILED') {
            $user = $this->GetUserByUsername([ 'username' => $cb ], false);
            if ($user) {
                $cb = $this->SetAuthUser($user);
                if (!empty($cb)) {
                    header('Location: /');
                } else {
                    $this->views->Pusher->SetNotification('YOU_ARE_BANNED');
                }
            } else {
                $this->views->Pusher->SetNotification('REGISTER_FAILED');
            }
        } else {
            $this->views->Pusher->SetNotification($cb);
        }
    }

}

?>