<?php

namespace Sketcher;

class Response {
    
    public $Service;

    public function __construct(?string $Service) {
        $this->Service  = $Service;
        $this->Path     = \realpath( __PATH__ . "/src/Models/" . (!empty($this->Service)? "{$this->Service}/": null) );
    }
    
    /**
     * Load service
     *
     * @param  string $ModelName
     * @param  array $Binded
     * @return void
     */
    public function Load(string $ModelName, array $Binded = []): void {
        $ModelPath = \realpath( $this->Path . "/" . trim($ModelName, "/") . ".php" );
        if (!empty($ModelPath) && $ModelPath !== false) {
            [ $header, $footer ] = $this->GetComponents();
            $Sandbox = function () use ($header, $ModelPath, $footer, $Binded) {
                $_DATAS_ = $Binded;
                require (!empty($header)? $header: __PATH__ . "/src/Components/header.php");
                require $ModelPath;
                require (!empty($footer)? $footer: __PATH__ . "/src/Components/footer.php");
            };
            $Sandbox();
        } else {
            http_response_code(500);
            echo "<b>FATAL INTERNAL ERROR</b>: Model \"{$ModelName}\" not found.";
        }
        return;
    }
    
    /**
     * Return current service components
     *
     * @return array
     */
    private function GetComponents(): array {
        $header = \realpath( __PATH__ . "/src/Components/" . (!empty($this->Service)? "{$this->Service}/": null) . "header.php" );
        $footer = \realpath( __PATH__ . "/src/Components/" . (!empty($this->Service)? "{$this->Service}/": null) . "footer.php" );
        return [ $header, $footer ];
    }

}

?>