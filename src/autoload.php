<?php

    require realpath(__DIR__ . '/..') . '/vendor/autoload.php';

    $containerBuilder       = new \DI\ContainerBuilder();
    $containerBuilder       ->useAutowiring(true);

    $container              = $containerBuilder->build();
    
    $Configuration          = new \App\Configuration;
    $router                 = new \Bramus\Router\Router;
    $route                  = new \App\Router\Routes;
    $route                  ->SetContainer($container);
    

?>