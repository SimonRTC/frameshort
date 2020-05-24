<?php

namespace Sketcher\Services;

class Exceptions {
    
    private $Exceptions;

    public function __construct() {
        $this->Exceptions = [
            "SERVICE_NOT_FOUND" => "exceptions/not-found"
        ];
    }
    
    /**
     * Throw exceptions model
     *
     * @param  object $Response
     * @param  string $Exception
     * @return void
     */
    public function Throw(\Sketcher\Response $Response, string $Exception): void {
        $Response->load($this->Exceptions[$Exception]);
        return;
    }

}

?>