<?php

namespace App\Router;

class views {

    private $Pusher;
    private $PushMessage;
    private $Auth;
    public $ClientAuth;
    public $Subsite;

    public function __construct(\App\Pusher $Pusher, \App\Core\Auth $Auth) {
        $this->path         = realpath(__DIR__ . '/../..').'/';
        $this->Subsite      = null;
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

    public function SetSubsite($subsite): bool {
        $this->Subsite = $subsite;
        return true;
    }

    public function load(string $view, array $data = null) {
        $data       = (!empty($data)? $data: false);
        $G          = $this->LoadDefaultVariables();
        $opened     = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/pusher.php';
        if (file_exists($opened)) { ($G['Pusher']->IsNotificationInStandBy()? require $opened: null); }
        $opened2    = $this->path . 'views/' . $view . '.php';
        if (file_exists($opened2)) { require $opened2; return true; }
        return false;
    }

    public function header() {
        $G  = $this->LoadDefaultVariables();
        require $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/header.php';
    }

    public function footer() {
        $G  = $this->LoadDefaultVariables();
        require $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/footer.php';
    }

    private function LoadDefaultVariables() {
        return [
            'auth'          => $this->ClientAuth,
            'Pusher'        => $this->Pusher,
        ];
    }

}

?>