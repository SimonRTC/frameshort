<?php

namespace App\Router;

class views {

    private $Pusher;
    private $PushMessage;
    private $Auth;
    public $ClientAuth;

    public function __construct(\App\Pusher $Pusher, \App\Core\Auth $Auth) {
        $this->path         = realpath(__DIR__ . '/../..').'/';
        $this->Auth         = $Auth;
        $this->Pusher       = $Pusher;
        $this->ClientAuth   = $this->CheckAuth();
    }

    private function CheckAuth() {
        if (!empty($_COOKIE['SESSION'])) {
            $session = (int)$_COOKIE['SESSION'];
            return $this->Auth->GetClient($session);
        }
    }

    public function SetPushMessage(string $code) {
        $this->Pusher->SetNotification($code);
        return null;
    }

    public function ParseUrl(): array {
        $URL    = explode('/', $_SERVER['REQUEST_URI']);
        $R_URL  = [];
        foreach ($URL as $URI) {
            if (!empty($URI)) {
                array_push($R_URL, $URI);
            }
        }
        return $R_URL;
    }

    public function load(string $view) {
        $G  = $this->LoadDefaultVariables();
        ($G['Pusher']->IsNotificationInStandBy()? require $this->path . 'components/pusher.php': null);
        require $this->path . 'views/' . $view . '.php';
    }

    public function header(bool $dashboard = false) {
        $G  = $this->LoadDefaultVariables();
        require $this->path . 'components/' . (!$dashboard ? 'header': 'dashboard/header') . '.php';
    }

    public function footer(bool $dashboard = false) {
        $G  = $this->LoadDefaultVariables();
        require $this->path . 'components/' . (!$dashboard ? 'footer': 'dashboard/footer') . '.php';
    }

    private function LoadDefaultVariables() {
        return [
            'auth'          => $this->ClientAuth,
            'Pusher'        => $this->Pusher,
        ];
    }

}

?>