<?php

namespace Console;

class Kernel {

    private $types;

    public function __construct() {
        $this->types    = [
            "DANGER"        => "\e[31m",
            "WARNING"       => "\e[33m",
            "INFO"          => "\e[36m",
            "SUCCESS"       => "\e[32m",
            "DEFAULT"       => "\e[39m"
        ];
    }
    
    /**
     * Ask yes or no question
     *
     * @param  string $question
     * @return bool
     */
    public function AskYesOrNoQuestion(string $question): bool {
        while (true) {
            $response = readline(" {$question} [Y/n]: ");
            if ($response === "Y" || $response === "y" || $response === "N" || $response === "n") {
                $response = ($response === "Y" || $response === "y"? true: false);
                break;
            } else {
                echo " \e[33mPlease type \"Y\" or \"N\".\e[39m\n\r";
            }
        }
        return $response;
    }
    
    /**
     * Trace in console
     *
     * @param  string $type
     * @param  string $content
     * @return void
     */
    public function Trace($type = "DEFAULT", $content): void {
        $type       = $this->types[(!empty($type)? $type: "DEFAULT")];
        $datetime   = (new \DateTime())->format("Y-m-d H:i:s");
        echo " > [\e[94m{$datetime}{$this->types["DEFAULT"]}] - {$type}{$content}{$this->types["DEFAULT"]}\n\r";
        return;
    }
    
    /**
     * parse request arguments
     *
     * @param  array $args
     * @return array
     */
    public function GetArguments(array $args): array {
        unset($args[0], $args[1], $args[2]);
        $args       = array_values($args);
        $arguments  = [];
        foreach ($args as $i => $arg) {
            preg_match("/--(.*)/", $arg, $match);
            if (isset($match[1]) && !empty($match[1])) {
                $_arg                   = (!empty($args[($i+1)])? $args[($i+1)]: true);
                $_arg                   = (!preg_match("/-(.*)/", $_arg) && !preg_match("/--(.*)/", $_arg)? $_arg: true);
                $arguments[$match[1]]   = $_arg;
            } else {
                preg_match("/-(.*)/", $arg, $match);
                if (isset($match[1]) && !empty($match[1])) {
                    $_arg                   = (!empty($args[($i+1)])? $args[($i+1)]: true);
                    $_arg                   = (!preg_match("/-(.*)/", $_arg) && !preg_match("/--(.*)/", $_arg)? $_arg: true);
                    $arguments[$match[1]]   = $_arg;
                }
            }
        }
        return $arguments;
    }

}

?>