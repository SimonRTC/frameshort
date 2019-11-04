<?php

namespace App\Router;

class views {

    private $Pusher;
    private $PushMessage;
    private $Auth;
    public $Buffer;
    public $ClientAuth;
    public $Subsite;
    public $bufferisation;

    public function __construct(\App\Pusher $Pusher, \App\Core\Auth $Auth) {
        $this->path             = realpath(__DIR__ . '/../..').'/';
        $this->Subsite          = null;
        $this->Auth             = $Auth;
        $this->Pusher           = $Pusher;
        $this->Buffer           = [];
        $this->bufferisation    = false;
        $this->ClientAuth       = $this->CheckAuth();
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
        if (file_exists($opened)) {
            if ($G['Pusher']->IsNotificationInStandBy()) {
                if ($this->bufferisation) {
                    $this->Bufferisation($opened);
                } else {
                    require $opened;
                }
            }
        }
        $opened2    = $this->path . 'views/' . $view . '.php';
        if (file_exists($opened2)) { 
            if ($this->bufferisation) {
                $this->Bufferisation($opened2);
            } else {
                require $opened2;
            }
            return true;
         }
        return false;
    }

    public function header() {
        $G      = $this->LoadDefaultVariables();
        $path   = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/header.php';
        if ($this->bufferisation) {
            $this->Bufferisation($path);
        } else {
            require $path;
        }
    }

    public function footer() {
        $G  = $this->LoadDefaultVariables();
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/footer.php';
        if ($this->bufferisation) {
            $this->Bufferisation($path);
        } else {
            require $path;
        }
    }

    public function Display() {
        foreach ($this->Buffer as $buffer) {
            echo $buffer();
        }
    }

    private function LoadDefaultVariables() {
        return [
            'auth'          => $this->ClientAuth,
            'Pusher'        => $this->Pusher,
        ];
    }

    private function Bufferisation($path) {
        $Buffer = new \App\Core\Buffer;
        $Buffer->Prepare('require');
        $Buffer->SetOptions([ 'path' => $path ]);
        $Buffer->Start();
        array_push($this->Buffer, function() use ($Buffer) { $Buffer->Show(); });
    }

}

?>