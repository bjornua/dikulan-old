<?php
    $production = array();
    $production['phpSettings']['display_startup_errors'] = '0';
    $production['phpSettings']['display_errors'] = '0';
    $production['includePaths']['library'] = APPLICATION_PATH . '/../library';
    $production['bootstrap']['path'] = APPLICATION_PATH . '/Bootstrap.php';
    $production['bootstrap']['class'] = 'Bootstrap';
    $production['resources']['frontController']['controllerDirectory'] = APPLICATION_PATH . '/controllers';
    $production['resources']['layout']['layoutPath'] = APPLICATION_PATH . '/layouts/scripts';
    
    $development = $production;
    $development['phpSettings']['display_startup_errors'] = 1;
    $development['phpSettings']['display_errors'] = 1;
    
    switch(APPLICATION_ENV){
      case 'production':
        return $production;
      case 'development':
        return $development;
    }