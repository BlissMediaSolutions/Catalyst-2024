<?php

$host = $username = $password = $database = $filename = $errorMsg = $dryrun = $createT = "";
$dir = "";

/* Main program start */
if ($argc <= 1) {
    echo "Error in Command.  Missing Directive instruction\n";
    exit (helpInstructions());
} else {
    $dir = "--help";
    if (checkDirectiveExists($dir, $argv, $argc) == TRUE) { //If directives includes Help - then just display help & exit.
        exit (helpInstructions());
    } else {
        if (!checkBaseDirectives($argc)) {    //If BaseDirectives failed, then we'll display Help as a courtesy & exit.
            echo "\n";
            exit(helpInstructions());
        } else {
            if ($dryrun) {
                readCSV();
                exit();
            }
            //if (!createDBConnection()) {  //IF Database connection failed, then we'll display Help as a courtesy & exit.
            //echo "about to run createDBConnection()\n";
            $db = createDBConnection();
            if ($db === FALSE) {
                exit(helpInstructions());
            } else if (!$dryrun) {          //If its a dry run, then we're not using\testing the database
                //do something
                createTable($db);
            }
        }
    }
}
/* Main program end */

//Simple function to check if a given directive exists
function checkDirectiveExists($dir, $argv, $argc) {
    return (in_array($dir, $argv));
}

//Check to see if the base required directives to function exist & set boolean flag for optional directives.
//create_table directive is required as email field is unique but then requires an empty space for invalid emails (so table gets dropped & recreated each time)
function checkBaseDirectives($argc) {
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

    //check if create_table directive is present - as its required.
    if (in_array("create_table", array_keys($direct)))
        $createT = TRUE;
    else
        $errorMsg .= "Error - Missing directive to create database table\n";

    //dry_run directive is OPTIONAL. Set a flag value as to if its present
    if (in_array("dry_run", array_keys($direct)))
        $dryrun = TRUE;
    else
        $dryrun = FALSE;

    //Basic check to see if $filename has .csv extension in name. **This doesn't actually check if its actually a properly structured\encoded CSV file.
    $ext = new SplFileIno($GLOBALS['filename']);
    If (strtolower($ext->getExtension()) != ("csv") OR (empty($ext)) OR ($ext == NULL))
        $errorMsg .= "Error - Invalid filename.  Only CSV files are supported & filename must include extension\n";

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
        exit("Error - Unable to locate ".$GLOBALS['filename']." file. Please check the file actually exists\n");

    //We want to exclude the firstline, so add a flag for it.
    $firstline = TRUE;
   
    if (($file = fopen($GLOBALS['filename'], "r")) !== FALSE) {
        while (($csvData = fgetcsv($file, 1000, ',')) !== FALSE) {
            if (!$firstline) {
                //format our CSV data & replace existing array data emails if invalid
                $csvData[0] = trim(ucwords(strtolower($csvData[0])));
                $csvData[1] = trim(ucwords(strtolower($csvData[1])));
                (filter_var(trim(strtolower($csvData[2])), FILTER_VALIDATE_EMAIL)) ? $csvData[2] = trim(strtolower($csvData[2])) : $csvData[2] = "";

                if (($GLOBALS['dryrun']) == TRUE)
                    echo "firstname:".$csvData[0]."  lastname:".$csvData[1]."  email:".$csvData[2]."\n";
            }
            $firstline = FALSE;
        }
    }
}

function createTable($db) {
    try {
        echo "try dropping table...\n";
        $result = mysqli_query($db, "USE ".$GLOBALS['database']);
        $result = mysqli_query($db, "DROP TABLE IF EXISTS users"); 
        //$result = $db.query("USE ".$GLOBALS['database']);
        //$result2 = $db.query("DROP TABLE IF EXISTS ".$GLOBALS['database']);
        var_dump($result2);
        echo "Table should be dropped...\n";


        //$create = $db.query("CREATE DATABASE IF NOT EXISTS ".$GLOBALS['database']);
        //$result = mysqli_query($db, "CREATE DATABASE IF NOT EXISTS ".$GLOBALS['database']);
        $sqlquery = "CREATE TABLE users (firstname VARCHAR(30), lastname VARCHAR(30), email VARCHAR(50)";
        $result = mysqli_query($db, $sqlquery);
        //$create = $db.query($sqlquery);
        echo "Creating new database table...\n"; 
        mysqli_close($db);
        $db = null;

    } catch (Exception $e) {
        echo "An Error occured while trying to create the table: ".$e;
    }

}

//Create the Database Connection using directive values.  We still use a try\catch incase the RedBean file is missing (RedBean doesn't pass PDO errors back)
function createDBConnection() {  
    try {
        //$con = mysqli_connect("127.0.0.1:3306","adminer","password","catalyst");  //THIS IS TESTED WORKING with hardcoded

        echo "Trying to create db connection...\n";
        //$db = new PDO("mysql:host=".$GLOBALS['host'].";dbname=".$GLOBALS['database'],$GLOBALS['username'],$GLOBALS['password']);
        $con = mysqli_connect($GLOBALS['host'],$GLOBALS['username'],$GLOBALS['password'],$GLOBALS['database']);
        if (mysqli_connect_errno()) {
            echo "Failed to connect to Database: " .mysqli_connect_error();
            exit();
        }
        
        echo "connected to database...\n";
        return $con;
        //$conn = mysqli_connect($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        //if ($conn) {
        //    echo "Creating new database connection created\n";
        //    return $conn;
        //}
    } catch (Exception $e) {
        echo "Problem with database connection: ".$e->getmessage();
        return FALSE;
        //exit(helpInstructions());
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
            "e.g: `php user_upload.php --file some file.csv -u root -p password -d something -h localhost`  - this will fail\n".
            "`php user_upload.php -- file \"some file.csv\" -u root -p passwword -d something -h localhost`  - this is correct with quoation marks\n"; 

        echo $text."\n";
    }

?>