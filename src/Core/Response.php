<?php

namespace Frameshort;

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
    public function Load(string $ModelName, array $Binded = [], array $Schedule = []): void {
        $ModelPath = \realpath( $this->Path . "/" . trim($ModelName, "/") . ".php" );
        if (!empty($ModelPath) && $ModelPath !== false) {
            [ $header, $footer ] = $this->GetComponents();
            $Sandbox = function () use ($header, $ModelPath, $footer, $Binded, $Schedule) {
                $_DATAS_ = $Binded;
                require (!empty($header)? $header: __PATH__ . "/src/Components/header.php");
                $_SCHEDULED_ = $this->ScheduleObjects($Schedule);
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
    
    /**
     * Schedule objects (Used for execute heavy tasks after send header)
     *
     * @param  array $Schedule
     * @return array
     */
    private function ScheduleObjects(array $Schedule): array {
        foreach ($Schedule as $i=>$Object) {
            $Schedule[$i] = $Object();
        }
        return $Schedule;
    }

}

?>