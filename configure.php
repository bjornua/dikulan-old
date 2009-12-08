<?php
    function prompt($msg){
        echo "$msg: ";
        $handle = fopen("php://stdin","r");
        $input = fgets($handle);
        $input = substr($input, 0, -1); // Strips newline character
        fclose($handle);
        return $input;
    }
    echo "MySQL configuration:\n";
    $mysql_hostname = prompt("  Address");
    $mysql_username = prompt("  Username");
    $mysql_password = prompt("  Password");
    $mysql_database = prompt("  Database");
    
    echo "\n";

    while(true){
        echo "Available environment configurations:\n";
        echo "  0: Development\n";
        echo "  1: Production\n";
        switch(prompt("Select environment [0]")){
            case "":
            case "0":
                $environment = "development";
                break 2;
            case "1":
                $environment = "production";
                break 2;
        }
    }

    $app_path = realpath(dirname(__FILE__))."/application";
    $std_cfg = include "$app_path/configs/application.template.php";
    $cfg = $std_cfg[$environment];
    $cfg["resources"]["db"]["params"]["host"    ] = $mysql_hostname;
    $cfg["resources"]["db"]["params"]["username"] = $mysql_username;
    $cfg["resources"]["db"]["params"]["password"] = $mysql_password;
    $cfg["resources"]["db"]["params"]["dbname"  ] = $mysql_database;

    $handle = fopen("$app_path/configs/application.php", "w");
    fwrite($handle, "<?php\n");
    fwrite($handle, "return ");
    fwrite($handle, var_export($cfg, true));
    fwrite($handle, ";");

    @mkdir($app_path . "/data", 0770, true);
    @mkdir($app_path . "/data/seatbooking", 0770, true);

    echo "\n";