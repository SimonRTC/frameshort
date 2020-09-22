<?php

namespace Frameshort;

class ConfigBuilder {

    private $_VARS_;

    public function __construct() {
        $this->_VARS_ = [ "DATABASES_JSON_EXPORT" ];
    }

    /**
     * Build configuration form environment variables
     * 
     * @return void
     */
    public function Build(): void {
        $_CONF_ = $this->GetSourceEnv();
        if (!empty($_CONF_["DATABASES_JSON_EXPORT"])) {
            $DATABASES = urldecode($_CONF_["DATABASES_JSON_EXPORT"]);
            if (!empty($DATABASES)) {
                file_put_contents(__PATH__ . "/src/conf/databases.json", $DATABASES);
            }
        }
    }

    /**
     * Parse environment variables
     * 
     * @return array Environment variables
     */
    private function GetSourceEnv(): array {
        $_ENV_  = getenv();
        $VARS   = [];
        foreach ($_ENV_ as $keyname => $value) {
            $keyname = explode("__", $keyname);
            if (!empty($keyname[1]) && $keyname[0] == "FRAMESHORT") {
                if (in_array($keyname[1], $this->_VARS_)) {
                    $VARS[$keyname[1]] = $value;
                }
            }
        }
        return $VARS;
    }

}

?>