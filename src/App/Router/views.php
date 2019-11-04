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
    public $data;

    public function __construct(\App\Pusher $Pusher, \App\Core\Auth $Auth) {
        $this->path             = realpath(__DIR__ . '/../..').'/';
        $this->Subsite          = null;
        $this->Auth             = $Auth;
        $this->Pusher           = $Pusher;
        $this->Buffer           = [];
        $this->bufferisation    = false;
        $this->data             = null;
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
        (!$this->bufferisation? $G = $this->LoadDefaultVariables(): null);
        $this->data = (!empty($data)? $data: false);
        $path       = $this->path . 'views/' . $view . '.php';
        if (file_exists($path)) { 
            ($this->bufferisation? $this->Bufferisation($path): require $path);
            return true;
         }
        return false;
    }

    public function header() {
        (!$this->bufferisation? $G = $this->LoadDefaultVariables(): null);
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/header.php';
        ($this->bufferisation? $this->Bufferisation($path): require $path);
    }

    public function footer() {
        (!$this->bufferisation? $G = $this->LoadDefaultVariables(): null);
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/footer.php';
        ($this->bufferisation? $this->Bufferisation($path): require $path);
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
        $Buffer->Inject([ 'G' => $this->LoadDefaultVariables(), 'data' => $this->data ]);
        $Buffer->SetOptions([ 'path' => $path ]);
        $Buffer->Start();
        array_push($this->Buffer, function() use ($Buffer) { $Buffer->Show(null); });
    }

}

?>