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

    public function signin($subsite, $method) {
        if (empty($this->views->ClientAuth)) {
            $this->views->SetSubsite($subsite);
            ($method == 'POST'? $this->login(): null);
            $this->CreateView('Auth/Login');
        } else {
            header('Location: /');
        }
    }

    public function signup($subsite, $method) {
        if (empty($this->views->ClientAuth)) {
            $this->views->SetSubsite($subsite);
            ($method == 'POST'? $this->register(): null);
            $this->CreateView('Auth/Register');
        } else {
            header('Location: /');
        }
    }

    public function SessionLogout(): bool {
        $auth = $this->Auth;
        $auth->Logout(((int)$_COOKIE['SESSION']));
        header('Location: /');
        return true;
    }

    public function CreateView($view): bool {
        $this->views->header();
        $cb = $this->views->load($view);
        $this->views->footer();
        return $cb;
    }

    private function login() {
        $user = $this->GetUserByUsername($this->PostedDatas);
        if ($user) {
            $cb = $this->SetAuthUser($user);
            if (!empty($cb)) {
                header('Location: /');
            } else {
                $this->views->SetPushMessage('YOU_ARE_BANNED');
            }
        } else {
            $this->views->SetPushMessage('USER_LOGIN_ERROR');
        }
    }

    public function register() {
        $cb = $this->AddClient($this->PostedDatas);
        if ($cb != 'ALREADY_TAKEN' && $cb != 'REGISTER_FAILED') {
            $user = $this->GetUserByUsername([ 'username' => $cb ], false);
            if ($user) {
                $cb = $this->SetAuthUser($user);
                if (!empty($cb)) {
                    header('Location: /');
                } else {
                    $this->views->SetPushMessage('YOU_ARE_BANNED');
                }
            } else {
                $this->views->SetPushMessage('REGISTER_FAILED');
            }
        } else {
            $this->views->SetPushMessage($cb);
        }
    }

}

?>