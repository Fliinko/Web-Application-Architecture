<?php

//Load our autoloader
    require_once 'vendor/autoload.php';

    //Specify our Twig templates location
    $loader = new Twig_Loader_Filesystem('templates');

    //Instantiates our Twig
    $twig = new Twig_Environment($loader);
