<?php

    $path = (realpath(__DIR__ . '/..') . '/src');
    require $path.'/autoload.php';

    foreach ($Configuration->routes as $rts) {
        if ($rts['host'] == false || $rts['host'] == $_SERVER['HTTP_HOST']) {
            [ $rts['controller'], $rts['function'] ] = explode('::', $rts['controller']);
            $router->match($rts['type'], $rts['pattern'], function($slug = null) use ($route, $rts) { ($route->load($rts['controller']))->{$rts['function']}((!empty($rts['subsite'])? $rts['subsite']: null), $_SERVER['REQUEST_METHOD'], (!empty($slug)? $slug: null)); });
        }
    }

    $router->set404(function() use ($route) { ($route->load('Errors'))->NotFound(); });

    $router->run();

?>