<?php
    $production = array(
        "phpSettings" => array (
            "display_startup_errors" => "0",
            "display_errors" => "0",
        ),
        "includePaths" => array (
            "library" => "$app_path/../library",
        ),
        "bootstrap" => array (
            "path" => "$app_path/Bootstrap.php",
            "class" => "Bootstrap",
        ),
        "resources" => array (
            "frontController" => array (
                "controllerDirectory" => "$app_path/controllers",
            ),
            "layout" => array (
                "layoutPath" => "$app_path/layouts/scripts",
            ),
            "db" => array (
                "adapter" => "mysqli",
                "params"  => array (
                    "host"     => "", // Configurable
                    "username" => "", // Configurable
                    "password" => "", // Configurable
                    "dbname"   => "", // Configurable
                ),
            ),
        ),
    );
    $development = $production;
    $development["phpSettings"]["display_startup_errors"] = "1";
    $development["phpSettings"]["display_errors"] = "1";

    return array(
        "development" => $development,
        "production" => $production
    );