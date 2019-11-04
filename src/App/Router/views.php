<?php

namespace App\Router;

class views {

    private $Auth;
    public $Pusher;
    public $Buffer;
    public $ProtectedPage;
    public $Redirect;
    public $Subsite;
    public $data;
    public $ClientAuth;

    public function __construct(\App\Core\Auth $Auth, \App\Pusher $Pusher) {
        $this->path             = realpath(__DIR__ . '/../..').'/';
        $this->Subsite          = null;
        $this->Auth             = $Auth;
        $this->Pusher           = $Pusher;
        $this->Buffer           = [];
        $this->data             = null;
        $this->ProtectedPage    = 'nothing-to-do';
        $this->Redirect         = null;
        $this->ClientAuth       = $this->ClientAuth();
    }

    public function ClientAuth(): ?array {
        if (!empty($_COOKIE['SESSION'])) {
            $session = (int)$_COOKIE['SESSION'];
            return $this->Auth->GetClient($session);
        }
        return null;
    }

    public function SetSubsite($subsite): bool {
        $this->Subsite = $subsite;
        return true;
    }

    public function load(string $view, array $data = null) {
        $this->data = (!empty($data)? $data: false);
        $path       = $this->path . 'views/' . $view . '.php';
        $pusher     = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/pusher.php';
        if (file_exists($path) && file_exists($pusher)) { 
            ($this->Pusher->IsNotificationInStandBy()? $this->Bufferisation($pusher): null);
            $this->Bufferisation($path);
            return true;
         }
        return false;
    }

    public function header() {
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/header.php';
        $this->Bufferisation($path);
    }

    public function footer() {
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/footer.php';
        $this->Bufferisation($path);
    }

    public function Create(string $view, ?array $datas = null) { 
        $this->header();
        $this->load($view, $datas);
        $this->footer();
        return $this->Call();
    }

    public function SetProtectedPage(bool $Protected, string $redirect = '/login') {
        $this->ProtectedPage    = $Protected;
        $this->Redirect         = $redirect;
    }

    public function Call() {
        if ($this->ProtectedPage == 'nothing-to-do' || $this->ProtectedPage == false && empty($this->ClientAuth) || $this->ProtectedPage == true && !empty($this->ClientAuth)) {
            foreach ($this->Buffer as $buffer) {
                echo $buffer();
            }
        } else {
            header('Location: ' . $this->Redirect);
            exit();
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