<?php
  $production = array();
  $production['phpSettings']['display_startup_errors'] = 0;
  $production['phpSettings']['display_errors'] = 0;
  $production['includePaths']['library'] = APPLICATION_PATH . "/../library";
  $production['bootstrap']['path'] = APPLICATION_PATH . "/Bootstrap.php";
  $production['bootstrap']['class'] = "Bootstrap";
  $production['resources']['frontController']['controllerDirectory'] = APPLICATION_PATH . "/controllers";
  
  $staging = $production;
  
  $testing = $production;
  $testing['phpSettings']['display_startup_errors'] = 1;
  $testing['phpSettings']['display_errors'] = 1;

  $development = $production;
  $development['phpSettings']['display_startup_errors'] = 1;
  $development['phpSettings']['display_errors'] = 1;
switch(APPLICATION_ENV){
  case 'production':
    return $production;
  case 'staging':
    return $staging;
  case 'testing':
    return $testing;
  case 'development':
    return $development;
}
