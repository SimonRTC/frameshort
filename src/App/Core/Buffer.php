<?php

namespace App\Core;


class Buffer {

    public $Action;
    private $Options;
    public $Buffer;
    private $Inject;

    public function __construct() {
        $this->Action               = null;
        $this->Options              = null;
        $this->Buffer               = null;
        $this->Inject               = null;
        $this->ActionsList          = [
            'require'   => function($options, $inject = null) {
                if (!empty($this->Inject)) {
                    foreach ($this->Inject as $key=>$value) {
                        ${$key} = $value;
                    }
                }
                require $options['path'];
            },
            'undefined' => function($options) {
                return $options();
            }
        ];
    }

    public function Prepare(string $name): bool {
        $call = null;
        foreach ($this->ActionsList as $act=>$listed) {
            if ($act == $name) {
                $call = $listed;
                break;
            }
        }
        $this->Action = $call;
        return (!empty($call)? true: false);
    }

    public function Inject(array $datas) {
        $this->Inject = $datas;
    }

    public function SetOptions(array $options): bool {
        $this->Options = $options;
        return true;
    }

    public function Start(): bool {
        $cb = false;
        if (!empty($this->Action)) {
            $action = $this->Action;
            $cb = ob_start();
            $cb = $action($this->Options, $this->Inject);
            $this->Buffer = ob_get_clean();
        }
        return ($cb? true: false);
    }

    public function Show($content = null): void {
        echo (!empty($content)? $content: $this->Buffer);
    }

}

?>