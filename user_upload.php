<?php

error_reporting(-1);
$host = $username = $password = $database = $filename = $errorMsg = $dryrun = $createT = "";
$dir = $db = "";

/* Main program start */
if ($argc <= 1) {
    echo "Error in Command.  Missing Directive instruction\n";  //No directives were included
    exit (helpInstructions());
} else {
    if (in_array("--help", $argv)) {                 // If directives include Help - then we just display help & exit.
        exit (helpInstructions());
    } else {
        if (!checkBaseDirectives($argc, $argv)) {          //If BaseDirectives failed, then we'll display Help as a courtesy & exit.
            echo "\n";
            exit(helpInstructions());
        } else {                        
            $db = createDBConnection();             //Connect to the database
            if ($GLOBALS['dryrun'] == FALSE)        //We only perform create table if its not a dry_run
                createTable($db);
            readCSV($db);                           //Process the CSV file - for either dryrun or writing to DB.
            $db=null;
            echo "\nclosed database connection...\n";
        }
    }
}
/* Main program end */

//Check to see if the base required directives to function exist & set boolean flag for optional directives.
//create_table directive is required as email field is unique but then requires an empty space for invalid emails (so table gets dropped & recreated each time)
function checkBaseDirectives($argc, $argv) {
    $sOpts = "d:h:u:p:";
    $lOpts = array("file:","create_table:","dry_run::");
    $direct = getopt($sOpts, $lOpts, $count);
    global $database, $host, $username, $password, $filename, $errorMsg, $dryrun, $createT;

    //First check - if Directives are correct with spaces\quoation marks then $argc will equal final index of getOpt
    if ($count != $argc) {
        echo "Error with directives.  Please check there are no invalid spaces\n";
        exit(helpInstructions());
    }

    //Check if database directive is present - as its required
    if (in_array("d", array_keys($direct)))
        $database = $direct['d'];
    else
        $errorMsg .= "Error - Missing directive & name for database\n";

    //check if host directive is present - as its required
    if (in_array("h", array_keys($direct)))
        $host = $direct['h'];
    else
        $errorMsg .= "Error - Missing directive & name for database host\n";

    //check if username directive is present - as its required
    if (in_array("u", array_keys($direct)))
        $username = $direct['u'];
    else
        $errorMsg .= "Error - Missing directive & name for database user\n";

    //check if password directive is present - as its required
    if (in_array("p", array_keys($direct)))
        $password = $direct['p'];
    else 
        $errorMsg .= "Error - Missing directive & password for database password\n";

    //check if file directive is present - as its required
    if (in_array("file", array_keys($direct)))
        $filename = $direct['file'];
    else
        $errorMsg .= "Error - Missing directive & filename for CSV file\n";

    //When multiple directives with no value like dry_run - getOpt can have issues, so we check argv arrary as well.
    //dry_run directive is OPTIONAL. Set a flag value as to if its present
    if ((in_array("dry_run", array_keys($direct))) OR (in_array("--dry_run", $argv)))
        $dryrun = TRUE;
    else
        $dryrun = FALSE;

    //When multiple directives with no value like create_table - getOpt can have issues, so we check argv arrary as well.
    //check if create_table directive is present - as its required.
    if ((in_array("create_table", array_keys($direct))) OR (in_array("--create_table", $argv)))
        $createT = TRUE;
    else
        $errorMsg .= "Error - Missing directive to create database table\n";

    //Basic check to see if $filename has .csv extension in name. **This doesn't actually check if its actually a properly structured\encoded CSV file.
    $ext = new SplFileInfo($filename);
    If (strtolower($ext->getExtension()) != ("csv") OR (empty($ext)) OR ($ext == NULL))
        $errorMsg .= "Error - Invalid filename.  Only CSV files are supported & filename must include the extension\n";

    if ((!empty($errorMsg)) OR ($errorMsg != NULL)) {
        echo $errorMsg;
        return FALSE;
    } else {
        return TRUE;
    }
}

//function to read the CSV and format the data in an array.  Also responsible for inserting data into table.
function readCSV($dBase) {
    //If the file doesn't exist then return error & exit
    if (!file_exists($GLOBALS['filename']))
        exit("Error - Unable to locate ".$GLOBALS['filename']." file. Please check the file actually exists\n");

    $errString = "";
    $count = 0;
    
    //We assume firstline is header & want to exclude the firstline, so add a flag for it.
    $firstline = TRUE;

    if ($GLOBALS['dryrun'] == TRUE)
        echo "Performing a dry_run of the CSV data (this is roughly what would be written to the database)...\nNote - Data which breaches database constraint violations will still appear below\n\n";
   
    if (($file = fopen($GLOBALS['filename'], "r")) !== FALSE) {
        while (($csvData = fgetcsv($file, 1000, ',')) !== FALSE) {
            if (!$firstline) {
                //cleanup & format our CSV data in the array
                $csvData[0] = trim(ucwords(strtolower($csvData[0])));
                $csvData[1] = trim(ucwords(strtolower($csvData[1])));
                $csvData[2] = trim(strtolower($csvData[2]));
                
                if ($GLOBALS['dryrun'] == FALSE) {
                    try {
                        //Find & escape apostrophes in strings - just for writing to the database cause we don't want this for dryrun
                        $fname = str_replace("'","''",$csvData[0]);  
                        $flastname = str_replace("'","''",$csvData[1]);
                        $femail = str_replace("'","''",$csvData[2]);
                        //insert into database if email is valid
                        if (filter_var($csvData[2], FILTER_VALIDATE_EMAIL)) {
                            $dBase->exec("INSERT INTO users (name, surname, email) VALUES ('$fname','$flastname','$femail')");  //Execute SQL and insert data into database
                            $count += 1;        //count the number of records inserted
                        } else
                            fwrite(STDOUT, "Error - Invalid email address and unable to write record to database:\nfirstname:".$csvData[0]." lastname:".$csvData[1]." email:".$csvData[2]."\n");    
                    
                    } catch (PDOException $e) {
                        fwrite(STDOUT, "Error inserting userdata into database:\n".$e->getmessage()."\n");      //We had an error on the database inserting this data
                    }
                }

                if ((($GLOBALS['dryrun']) == TRUE) && (filter_var($csvData[2], FILTER_VALIDATE_EMAIL)))
                    echo "firstname:".$csvData[0]."  lastname:".$csvData[1]."  email:".$csvData[2]."\n";    //We're performing a Dryrun - only display data with valid email
                elseif ((($GLOBALS['dryrun']) == TRUE) && (!filter_var($csvData[2], FILTER_VALIDATE_EMAIL)))
                    $errString .= "firstname:".$csvData[0]."  lastname:".$csvData[1]."  email:".$csvData[2]."\n";   //We're performing a Dryrun - data with invalid email added to error string
            }
            $firstline = FALSE;
        }
    }
    if ((!empty($errString)) && ($errString != NULL))
        echo "\nThe following data would not be written to the database due to invalid email addresses:\n".$errString;

    if (($GLOBALS['dryrun'] == FALSE) && ($count > 0))
        echo "\n".$count." records written to database.\n";
}

function createTable($db) {
    try {
        //Drop table if Exists. Recreate table & add Unique index constraint.
        $statements = array(
            "DROP TABLE IF EXISTS users;",
            "CREATE TABLE users (name VARCHAR(30), surname VARCHAR(30), email VARCHAR(50));",
            "CREATE UNIQUE INDEX email_idx ON users (email);"
        );

        foreach ($statements as $statement) {
            $db->exec($statement);
        }

        if ($GLOBALS['dryrun'] == FALSE)
            echo "Database table prep completed...\n";

    } catch (PDOException $e) {
        exit ("Problem with database table users: ".$e->getmessage()."\n");
    }

}

//Create the Database Connection using directive values.  Try\catch to capture connection errors
function createDBConnection() {  
    try {
        $db = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['database'],$GLOBALS['username'],$GLOBALS['password']);
        
        echo "connected to database...\n";
        return $db;
        
    } catch (PDOException $e) {
        exit ("Problem with database connection: ".$e->getmessage()."\n");
    }
}

function helpInstructions()
    {
        $text = "\n     |>> ** USER_UPLOAD DIRECTIVES ** <<|     \n".
            "--help = Display these instructions for directive usage\n".
            "--file [csv filename] = this is the name of the CSV file to be parsed into the database\n".
            "--create_table = will create the users db table\n".
            "--dry_run = Peforms a dry run on the csv file, but doesn't actually write any data to the Db\n".
            "-u [username] = set the database username\n".
            "-p [password] = set the database password\n".
            "-h [host] = set the database host\n".
            "-d [database] = set the database name\n".
            "\n NOTE - If a username, passwword, database or file contains a space, then it needs to be ecapsulated\n".
            "for example:\n`php user_upload.php --file some file.csv -u root -p password -d something -h localhost`  - this will fail\n".
            "`php user_upload.php --file \"some file.csv\" -u root -p passwword -d something -h localhost`  - this is correct with quoation marks\n"; 

        echo $text."\n";
    }


?>