# Catalyst IT Coding Challenge

#### Author:
* Danielle Walker

#### Designed / Tested on:
* Debian 11 (Bullseye)   
* PHP 8.1.27
* MariaDB 10.5.23

#### Makes use of:
* RedBean ORM (for database)

#### Assumptions:
* If the Help directive was included (with all other directives) we assume the user needs help & just display the Help without trying to access the database or process any data.
* The directives for: database, host, username, password, filename & create_table should all be 'required'.  If any are excluded it will supply an error message with Help displayed.
* When performing a 'dry_run' then all required directives are present.  Its assumed a dry-run doesn't need to check the database connection & its just concerned with parsing data.
* For MySQL\MariaDB Host - its assumed the user understands IP addresses & knows what Port its listening on.
* Its assumed the database (as specifed in directive) will already exist.  This script wont create a database.
* Its assumed username\password supplied in directives will already have appropriate access\permissions for the database.
* Database Name wont contain spaces unless its encapsulated within quoations marks 
* Username wont contain spaces unless its encapsulated within quotation marks
* Password wont contain spaces unless its encapsulated within quotation marks
* CSV filename wont contain spaces unless its encapsulated within quotation marks.
* Performing a `dry_run` on the csv will require all directives - the same as when not performing a dry_run.
* `create_table` directive is excluded & the table exists, then data will be appended to it.

#### Limiations:
* While the script will check if a file exists (as given in directive) & that the directive filename has a CSV extension - it wont actually check the encoding of the specifed CSV file in the directive.  i.e: It wont validate if its UTF-8 string encoding.

#### Usage:
The file rb-mysql.php needs to be included in the same directory as where the script will run (so it can then use RedBean ORM).
To display the Help instructions:
`php user_upload.php --help`

