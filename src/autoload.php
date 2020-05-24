<?php

    $path = realpath( __DIR__ . "/.." );
    $path = ($path !== false? $path: null);

    define("__PATH__", $path);

    require __PATH__ . '/vendor/autoload.php';

?>