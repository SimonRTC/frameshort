<?php

namespace Sketcher\Services;

class Welcome {
        
    /**
     * Welcome page (Hello World!)
     *
     * @param  object $Response
     * @param  array $Binded
     * @return void
     */
    public function Welcome(\Sketcher\Response $Response, array $Binded = []): void {
        $Response->load("welcome", $Binded);
        return;
    }

}

?>