<?php

spl_autoload_register(function ($class) {
    $class = realpath(__DIR__ . "/{$class}.php");
    if ($class !== false) {
        include $class;
    }
});

?>