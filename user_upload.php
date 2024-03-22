<?php

    $host = "127.0.0.1";        //default IP of MariaDB
    $username = "username";     //default dummy username for MariaDB   
    $password = "password";     //default dummy password for MariaDB
    $database = "database";     //default dummy name for database
    $filename = "somefile.csv"  //default dummy name for CSV file
    
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
            case str_starts_with($direct, "--file"):  //assumes file name (or location) wont contain spaces
                $file = explode(" ", $direct)
                echo count($file) != 2 ? "missing or invalid filename\n" : processCSV($file[1]);
                break;
            case "--create_table":
                //todo create database table
                echo "database table created\n";
                break;
            case "--dry_run":
                //todo perform dry run
                echo "dry run\n";
                break;
            case str_starts_with($direct, "-u"): //username may contain spaces - so will need to rejoin elements
                $user = explode(" ", $direct);
                echo count($user)!= 2 ? "missing for invalid password\n" : count($user)."\n";
                break;
            case str_starts_with($direct, "-p"):  //password may contain spaces - so will need to rejoin elements
                $passw = explode(" ", $direct);
                echo count($passw) !> 2 ? "missing for invalid password\n" : $direct."\n";
                break;
            case str_starts_with($direct, "-h"):  //assumes hostname wont contain spaces
                $hostadd = explode(" ", $direct);
                echo count($hostadd) != 2 ? "missing or invalid hostname\n" : "Host name changed to: ".$host = $hostadd[1];
                break;
            case str_starts_with($direct, "-d"):  //assumes database name wont contain spaces
                $datab = explode(" ", $direct);
                echo count($datab) != 2 ? "missing or invalid database name\n" : "Database name changed to: ".$database = $datab[1]."\n";
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