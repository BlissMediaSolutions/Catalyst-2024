<?php

    $host = "127.0.0.1";        //default IP of MariaDB
    $username = "root";         //default username for MariaDB   
    $password = "password";     //default password for MariaDB
    
    if (sizeof($argv) <= 1)
    {
        fwrite(STDOUT, "Error in Command.  Missing Directive instruction\n");
        exit (helpInstructions());
    }





    function helpInstructions()
    {
        $text = "\n     |>> ** USER_UPLOAD DIRECTIVES ** <<|     \n".
            "   --help = Display these instructions for directive usage\n".
            "   --file [csv filename] = this is the name of the CSV file to be parsed into the database\n".
            "   --file [csv filename] --dry_run = Peforms a dry run on the csv file, but doesn't actually write any data to the Db\n";

        fwrite(STDOUT, $text);
    }

?>