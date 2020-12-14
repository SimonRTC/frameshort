<?php

namespace Console;

class Help {

    private $Kernel;

    public function __construct(\Console\Kernel &$Kernel) {
        $this->Kernel = $Kernel;
    }

    public function index(): void {
        $this->Kernel->Trace("DEFAULT", "To know how the CLI works, go to the documentation. //github.com/SimonRTC/frameshort/wiki");
        return;
    }

}

?>