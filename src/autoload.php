<?php

    $path = realpath(__DIR__ . '/..');

    require $path . '/vendor/autoload.php';
    require $path . '/src/App/autoloader.php';

    Autoloader::register();

    $containerBuilder       = new \DI\ContainerBuilder();
    $containerBuilder       ->useAutowiring(true);

    $container              = $containerBuilder->build();
    
    $Configuration          = new \App\Configuration;
    $router                 = new \Bramus\Router\Router;
    $route                  = new \App\Router\Routes;
    $route                  ->SetContainer($container);
    

?>