# Catalyst IT Coding Challenge

#### Author:
* Danielle Walker

#### Designed for / Tested on:
* Debian 11 (Bullseye)   
* PHP 8.1.27
* MariaDB 10.5.23

#### Assumptions:
* The order in which directives are entered should not matter.
* If the Help directive was included (with all other directives) we assume the user needs help & just display the Help without trying to access the database or process any data.
* The directives for: database, host, username, password, filename & create_table should all be 'required'.  If any are excluded it will supply an error message with Help displayed.
* When performing a 'dry_run' then all required directives are present.  A dry_run will test the connection to the database, but it will not drop or create a table.
* The CSV file being used is valid - it will check the file exists & has a CSV extension, but it will not check the structure or encoding of the file.
* For MySQL\MariaDB Host - its assumed the user understands IP addresses & knows what Port its listening on.
* Its assumed the database (as specifed in directive) will already exist.  This script wont create a database.
* Its assumed username\password supplied in directives will already have appropriate access\permissions for the database.
* Database Name wont contain spaces unless its encapsulated within quoations marks. 
* Username wont contain spaces unless its encapsulated within quotation marks.
* Password wont contain spaces unless its encapsulated within quotation marks.
* CSV filename wont contain spaces unless its encapsulated within quotation marks.
* Performing a `dry_run` on the csv will require all directives - the same as when not performing a dry_run.
* The user has some experience running PHP Scripts in a CLI.

#### Limiations:
* While the script will check if a file exists (as given in directive) & that the directive filename has a CSV extension - it wont actually check the structure or encoding of the specifed CSV file in the directive.  i.e: It wont validate if its UTF-8 string encoding.
* Having a directive repeated may cause issues i.e: `php user_upload.php --dry_run -d catalyst -h 127.0.0.1:3306 -u root -p password --file users.csv --create_table --dry_run`.  While directives are validated to an extent, this does not include them being repeated.
* This hasn't been tested against EOL versions of PHP ~ like PHP versions 3,4 & 5.
* This hasn't been tested on a Windows OS or Mac OS.
* While getOpt is preferred for reading directives passed (due to requirements), it does experience issues when having directives with no value - like create_table & dry_run.  Thus these need to be checked in argv array as well.
* Name & surname fields in the databse only accept 30 characters.  Email field in the database only accepts 50 characters. (these can be easily changed in the code).

#### Usage:
When using the script, various directives (or arguments) need to be supplied - most of these are required.  These are:
* --file [csv filename] *required     - csv file to be processed
* --create_table *required        
* --dry_run                           - perform a dry run of the CSV file without writing to database
* -d [database name] *required        - the database name
* -h [hostname ] *required            - the host address for the database
* -u [user name] *required            - username for access to the database
* -p [password ] *required            - password for access to the database
* --help                              - display Help instructions on the screen.
* Using\including dry_run directive is optional

To display the Help instructions:
* `php user_upload.php --help`
To perform a dry_run (this requires all directives):
* `php user_upload.php -d catalyst -h 127.0.0.1:3306 -u root -p password --file users.csv --create_table --dry_run`
To process the CSV and write to the database:
* `php user_upload.php -d catalyst -h 127.0.0.1:3306 -u root -p password --file users.csv --create_table`
Whenever the Help directive is included like with this, then only the help instructions get displayed and nothing else will happen.
* `php user_upload.php -d catalyst -h 127.0.0.1:3306 -u root -p password --file users.csv --dry_run --help`
If (required) directives are excluded, but Help directive is included - then only Help instructions display.
* `php user_upload.php -d catalyst -h 127.0.0.1:3306 --help`
If required directive(s) is missing, an appropriate error will be displayed for what directives are missing.

#### Future Improvements \ upgrades:
* Use an ORM for database queries (RedBean not appropriate due to limitations)
* Check the structure & encoding of the CSV file
* Could have used a Class for Database queries - but PDO already an object, & having a wrapper for PDO didn't appear to make much sense.
* Reduce\replace Global variables - possibly change these to Enum.

#### Update - since refactor commits
* Now making use of Directive class
* setter (and getter) functions for Directive class not really needed - done for completeness & expandability.
* MIME type of data file is now checked that its CSV
* Removing Global variables
