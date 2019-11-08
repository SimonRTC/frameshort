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

    /**
     * __construct
     *
     * @param  object $Auth
     * @param  object $Pusher
     *
     * @return void
     */
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

    /**
     * ClientAuth
     *
     * @return array
     */
    public function ClientAuth(): ?array {
        if (!empty($_COOKIE['SESSION'])) {
            $session = (int)$_COOKIE['SESSION'];
            return $this->Auth->GetClient($session);
        }
        return null;
    }

    /**
     * SetSubsite
     *
     * @param  string $subsite
     *
     * @return bool
     */
    public function SetSubsite(?string $subsite): bool {
        $this->Subsite = $subsite;
        return true;
    }

    /**
     * load
     *
     * @param  string $view
     * @param  array  $data
     *
     * @return void
     */
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

    /**
     * header
     *
     * @return void
     */
    public function header() {
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/header.php';
        $this->Bufferisation($path);
    }

    /**
     * footer
     *
     * @return void
     */
    public function footer() {
        $path = $this->path . 'components'. (!empty($this->Subsite)? '/' . $this->Subsite: null) .'/footer.php';
        $this->Bufferisation($path);
    }

    /**
     * Create
     *
     * @param  string $view
     * @param  array  $datas
     *
     * @return void
     */
    public function Create(string $view, ?array $datas = null) { 
        $this->header();
        $this->load($view, $datas);
        $this->footer();
        return $this->Call();
    }

    /**
     * SetProtectedPage
     *
     * @param  bool   $Protected
     * @param  string $redirect
     *
     * @return void
     */
    public function SetProtectedPage(bool $Protected, string $redirect = '/login') {
        $this->ProtectedPage    = $Protected;
        $this->Redirect         = $redirect;
    }

    /**
     * Call
     *
     * @return void
     */
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

    /**
     * LoadDefaultVariables
     *
     * @return void
     */
    private function LoadDefaultVariables() {
        return [
            'auth'          => $this->ClientAuth,
            'Pusher'        => $this->Pusher,
        ];
    }

    /**
     * Bufferisation
     *
     * @param  string $path
     *
     * @return void
     */
    private function Bufferisation(string $path) {
        $Buffer = new \App\Core\Buffer;
        $Buffer->Prepare('require');
        $Buffer->Inject([ 'G' => $this->LoadDefaultVariables(), 'data' => $this->data ]);
        $Buffer->SetOptions([ 'path' => $path ]);
        $Buffer->Start();
        array_push($this->Buffer, function() use ($Buffer) { $Buffer->Show(null); });
    }

}

?>