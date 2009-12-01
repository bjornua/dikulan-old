<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    function _initAutoloader(){
        require_once 'Zend/Loader/Autoloader.php';
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Model_');
    }

}

