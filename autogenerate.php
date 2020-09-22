<?php

/**
 * Installation of composer components
 */

if (!realpath("./vendor/")) {
    $_PROD_         = AskQuestion("Would you like to generate default components in production mode?", true);
    $COMPOSER_PHAR  = AskQuestion("Enter your \"composer.phar\" location ? (let empty for use path shortcut)");
    
    exec((!empty($COMPOSER_PHAR)? trim($COMPOSER_PHAR): "composer") . " install --" . ($_PROD_? "no-": null) . "dev");
    sleep(1);
    exec((!empty($COMPOSER_PHAR)? trim($COMPOSER_PHAR): "composer") . " dump-autoload");
    exec((!empty($COMPOSER_PHAR)? trim($COMPOSER_PHAR): "composer") . " dump-autoload -o");
}

/**
 * Temporary files initialisation
 */

if (!realpath("./tmp/")) {
    mkdir("./tmp/");
    file_put_contents("./tmp/logs", null);
    echo "\n\e[34m > Temporary file as been created. \e[33m\n\n";
}

if (!realpath("./tmp/cache/")) {
    mkdir("./tmp/cache/");
    echo "\n\e[34m > Temporary file cache as been created. \e[33m\n\n";
}

/**
 * SQL Dump (file creation)
 */

if (!realpath("./tmp/sql-export/")) {
    mkdir("./tmp/sql-export/");
    file_put_contents("./tmp/sql-export/dump.sql", null);
    echo "\n\e[34m > Database export as been initialised. \e[33m\n\n";
}

echo "\n\n\e[32m >> End of initialization. \e[33m\n\n";

/**
 * Ask question to client with CLI
 */

function AskQuestion(string $phrase, bool $boolean = false) {
    echo "\n";
    $_RESPONSE_ = null;
    if ($boolean) {
        while ($_RESPONSE_ === null) {
            $_RESPONSE_ = readline("{$phrase} [Y/n] ");
            $_RESPONSE_ = ($_RESPONSE_ == "Y" || $_RESPONSE_ == "y"? true: ($_RESPONSE_ == "N" || $_RESPONSE_ == "n"? false: null));
            if ($_RESPONSE_ === null) {
                echo "\n \e[31m Invalid response format. \e[33mPlease retry. \e[39m\n\n";
            }
        }
    } else {
        $_RESPONSE_ = readline("{$phrase}: ");
    }
    return $_RESPONSE_;
}

?>