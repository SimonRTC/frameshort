<?php

namespace Frameshort;

class Router {
    
    public  $HttpHost;
    public  $RequestMethod;
    public  $RequestUri;
    public  $Response;
    private $Routes;
    private $Namespace;
    private $Service;
    private $Binded;

    public function __construct(string $HttpHost, string $RequestMethod, string $RequestUri) {
        $this->HttpHost         = $HttpHost;
        $this->RequestMethod    = $RequestMethod;
        $this->RequestUri       = $RequestUri;
        $this->Namespace        = null;
        $this->Binded           = [];
        $this->Routes           = \json_decode(\file_get_contents( __PATH__ . '/src/conf/routes.json' ), false);

        /* Load Requested Service */
        $this->Service          = $this->LoadService();
        $this->Response         = $this->RenderService();
    }
    
    /**
     * Load requested service
     *
     * @return void
     */
    private function LoadService(): ?object {
        foreach ($this->Routes as $Route) {
            $Route->method = \explode("|", $Route->method);
            if (in_array($this->RequestMethod, $Route->method)) {
                if ($this->PatternMatched($Route->pattern)) {
                    $this->Namespace = (isset($Route->namespace) && !empty($Route->namespace)? $Route->namespace: null);
                    $Road = function ($Response) use ($Route) {
                        [ $Service, $Callable ] = \explode("::", $Route->service);
                        $Service    = "\Frameshort\Services\\{$Service}";
                        $Road       = new $Service();
                        return $Road->{$Callable}($Response, $this->Binded);
                    };
                    break;
                }
            }
        }
        return (!empty($Road)? $Road: null);
    }
    
    /**
     * Show previously loaded service 
     *
     * @param  object $Service
     * @return void
     */
    private function RenderService(?object $Service = null, ?string $Namespace = null): void {
        $Service    = (!empty($Service)? $Service: $this->Service);
        $Service    = (!empty($Service)? $Service: $this->ThrowException("SERVICE_NOT_FOUND"));
        $Namespace  = (!empty($Namespace)? $Namespace: $this->Namespace);
        $Response   = new \Frameshort\Response($Namespace);
        try {
            $Service($Response);
        } catch (\Throwable $e) {
            $this->PushInLogFile($e);
            $Throwed = $this->ThrowException("INTERNAL_SERVER_ERROR");
            $Throwed($Response);
        }
        return;
    }
    
    /**
     * Parse route pattern
     *
     * @param  string $Pattern
     * @return string
     */
    private function PatternMatched(string $Pattern, ?string $Requested = null): bool {
        $Patterns   = \explode("/", trim($Pattern, "/"));
        $Requesteds = \explode("/", trim(((!empty($Requested)? $Requested: $this->RequestUri)), "/"));
        $Binded     = [];

        if (count($Patterns) == count($Requesteds)) {
            foreach ($Patterns as $i=>$Pattern) {
                preg_match("/{(.*)}/", $Pattern, $match);
                if (!empty($match)) {
                    $Patterns[$i]   = $Requesteds[$i];
                    $Binded         = array_merge($Binded, [
                        "{$match[1]}" => $Requesteds[$i]
                    ]);
                }
            }
        }

        $Pattern    = \implode("/", $Patterns);
        $Requested  = \implode("/", $Requesteds);

        if ($Pattern == $Requested) {
            $this->Binded = $Binded;
            return true;
        }

        return false;
    }
    
    /**
     * Throw exception
     *
     * @param  string $Exception
     * @return object
     */
    private function ThrowException(string $Exception): object {
        $Road = function ($Response) use ($Exception) {
            return (new \Frameshort\Services\Exceptions)->Throw($Response, $Exception);
        };
        return $Road;
    }
    
    /**
     * PushInLogFile
     *
     * @param  Throwable $e
     * @return void
     */
    private function PushInLogFile(\Throwable $e): void {
        $log    = (new \DateTime())->format("Y-d-m\TG:i:s.u\ZP") ." ERROR (500) - \"{$e->getMessage()}\" in \"{$e->getFile()}\" at line {$e->getLine()}";
        $path   = __PATH__ . "/tmp/logs";
        $logs = (file_exists($path)? \explode("\n", \file_get_contents($path)): []);
        array_push($logs, $log);
        $logs = (!empty($logs)? implode("\n", $logs): $log);
        \file_put_contents($path, $logs);
        return;
    }

}

?>