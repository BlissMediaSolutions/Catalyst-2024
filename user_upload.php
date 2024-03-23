<?php

require 'rb-mysql.php';

$host = $username = $password = $database = $filename = $errorMsg = $dryrun = "";
$dir = "";

if ($argc <= 1) {
    echo "Error in Command.  Missing Directive instruction\n";
    exit (helpInstructions());
} else {
    $dir = "--help";
    if (checkDirectiveExists($dir, $argv, $argc) == TRUE) { //If directives includes Help - then just display help & exit.
        exit (helpInstructions());
    } else {
        if (!checkBaseDirectives()) {    //If BaseDirectives failed, then we'll display Help as a courtesy & exit.
            echo "\n";
            exit(helpInstructions());
        } else {
            if ($dryrun) {
                readCSV();
                exit;
            }
            if (!createDBConnection()) {  //IF Database connection failed, then we'll display Help as a courtesy & exit.
                exit(helpInstructions());
            } else if (!$dryrun) {          //If its a dry run, then we're not using\testing the database
                //do something
                createTable();
                R::close();
            }
        }
    }
}

//Simple function to check if a given directive exists
function checkDirectiveExists($dir, $argv, $argc) {
    return (in_array($dir, $argv));
}

//Check to see if the base required directives to function exist
//CreateTable direective is optional, because if table exists it will just append data
function checkBaseDirectives() {
    $sOpts = "d:h:u:p:";
    $lOpts = array("file:","create_table::","dry_run::");
    $direct = getopt($sOpts, $lOpts);
    global $database, $host, $username, $password, $filename, $errorMsg, $dryrun;

    /* TO DO: NEED TO HANDLE BAD $direct VALUES i.e:--file some file.csv - which has no quotation marks */

    if (in_array("d", array_keys($direct)))
        $database = $direct['d'];
    else
        $errorMsg .= "Error - Missing directive & name for database\n";

    if (in_array("h", array_keys($direct)))
        $host = $direct['h'];
    else
        $errorMsg .= "Error - Missing directive & name for database host\n";

    if (in_array("u", array_keys($direct)))
        $username = $direct['u'];
    else
        $errorMsg .= "Error - Missing directive & name for database user\n";

    if (in_array("p", array_keys($direct)))
        $password = $direct['p'];
    else 
        $errorMsg .= "Error - Missing directive & password for database password\n";

    if (in_array("file", array_keys($direct)))
        $filename = $direct['file'];
    else
        $errorMsg .= "Error - Missing directive & filename for CSV file";

    if (in_array("dry_run", array_keys($direct)))
        $dryrun = TRUE;
    else
        $dryrun = FALSE;

    if ((!empty($errorMsg)) OR ($errorMsg != NULL)) {
        echo $errorMsg;
        return FALSE;
    } else {
        return TRUE;
    }
}

//function to read the CSV and format the data in an array
function readCSV() {
    //If the file doesn't exist then supply error
    if (!file_exists($GLOBALS['filename']))
        exit("Error - Unable to locate ".$GLOBALS['filename'].". Please check the file exists\n");

    //We want to exclude the firstline, so add a flag for it.
    $firstline = TRUE;
    
    if (($file = fopen($GLOBALS['filename'], "r")) !== FALSE) {
        while (($csvData = fgetcsv($file, 1000, ',')) !== FALSE) {
            if (!$firstline) {
            //format our CSV data & replace existing array data
                $csvData[0] = trim(ucwords(strtolower($csvData[0])));
                $csvData[1] = trim(ucwords(strtolower($csvData[1])));
                if (filter_var(trim(strtolower($csvData[2])), FILTER_VALIDATE_EMAIL))
                    $data[2] = trim(strtolower($csvData[2]));
                else
                    $data[2] = "";
                
                if (($GLOBALS['dryrun']) == TRUE)
                    echo "firstname:".$csvData[0]."  lastname:".$csvData[1]."  email:".$csvData[2]."\n";
            }
            $firstline = FALSE;
        }
    }
}

function createTable() {
    try {
        if ($users = R::load('users') != 0);
            R::trash($users);
        
        $users = R::dispense('users');
        $users->email;
        $users->firstname;
        $users->lastname;
        $id = R::store($users);

    } catch (Exception $e) {
        echo "an error occured: ".$e;
    }

}

//Create the Database Connection using directive values.  We still use a try\catch incase the RedBean file is missing (RedBean doesn't pass PDO errors back)
function createDBConnection() {  
    try {
        R::setup('mysql:host='.$GLOBALS['host'].";dbname=".$GLOBALS['database'].",".$GLOBALS['username'].",".$GLOBALS['password']);
        
        //Test the RedBean Connection, if it fails then display error message & help instructions
        if (!$isConnected = R::testConnection()) {
            echo "An Error has occured with the database connection.  Please check the database name & host and that the user account has access\n";
        }

        if ($isConnected == FALSE)
            return FALSE;
        else {
            return TRUE;
        }
        //Sometimes its easier to test with PDO - so below line can be used for that.
        //$db = new PDO('mysql:host='.$GLOBALS['host'].';dbname='.$GLOBALS['database'].','.$GLOBALS['username'].','.$GLOBALS['password']);
    } catch (Exception $e) {
        echo "Problem with database connection: ".$e->getmessage();
        exit(helpInstructions());
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
            "-d [database] = set the database name\n";

        echo $text."\n";
    }

?>