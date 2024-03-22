<?php

    $host = "127.0.0.1";        //default IP of MariaDB
    $username = "username";     //default username for MariaDB   
    $password = "password";     //default password for MariaDB
    
    if (sizeof($argv) <= 1) {
        echo "Error in Command.  Missing Directive instruction\n";
        exit (helpInstructions());
    } else {
        menu($argv[1]);
        while (true) {
    
            $line = readline("Directive: ");
    
            if (($line === false) OR ($line === null) OR (empty($line))) {
                break;
            }
            menu($line);
        }
    }
    
    function menu($direct) {
        switch ($direct) {
            case "--help":
                helpInstructions();
                break;
            case str_starts_with($direct, "--file"):
                echo $direct."\n";
                break;
            case "--create_table":
                echo "create table\n";
                break;
            case "--dry_run":
                echo "dry run\n";
                break;
            case str_starts_with($direct, "-u"):
                $user = explode(" ", $direct);
                echo (count($user)!= 2) ? count($user)."\n" : count($user)."\n" ;
                break;
            case str_starts_with($direct, "-p"):
                $passw = explode(" ", $direct);
                echo count($passw) != 2 ? $direct."\n" : "missing for invalid password\n";
                break;
            case str_starts_with($direct, "-h"):
                $hostadd = explode(" ", $direct);
                echo count($hostadd) != 2 ? $direct."\n" : "missing or invalid hostname\n";
                break;
            case str_starts_with($direct, "-d"):
                $datab = explode(" ", $direct);
                echo count($datab) != 2 ? $direct."\n" : "missing or invalid database name\n";
                break;
            default:
                echo "imvalid directive command\n";
                break;
        }
    }




    function helpInstructions()
    {
        $text = "\n     |>> ** USER_UPLOAD DIRECTIVES ** <<|     \n".
            "   --help = Display these instructions for directive usage\n".
            "   --file [csv filename] = this is the name of the CSV file to be parsed into the database\n".
            "   --create_table = will create the users db table\n".
            "   --dry_run = Peforms a dry run on the csv file, but doesn't actually write any data to the Db\n";
            "   -u = set the database username\n".
            "   -p = set the database password\n".
            "   -h = set the database host\n".
            "   -d = set the database name\n";

        echo $text."\n";
    }

?>