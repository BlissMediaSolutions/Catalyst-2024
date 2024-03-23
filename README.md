# Catalyst IT Coding Challenge

#### Author:
* Danielle Walker

#### Designed / Tested on:
* Debian 11 (Bullseye)   
* PHP 8.1.27
* MariaDB 10.5.23

#### Makes use of:
* RedBean ORM (for database)

#### Assumptions
* If the Help directive was included (with all other directives) we assume the user needs help & display the Help without trying to access the database or process any data.
* MSQL\MariaDB Host address wont contain spaces unless its encapsulated within quotation marks
* Database Name wont contain spaces unless its encapsulated within quoations marks 
* Username wont contain spaces unless its encapsulated within quotation marks
* Password wont contain spaces unless its encapsulated within quotation marks
* CSV filename wont contain spaces unless its encapsulated within quotation marks.
* If Create_table directive is excluded & then table exists, then data will be appended to it.

#### Usage:
The file `rb-mysql.php' needs to be included in the same directory as where the script will run (so it can then use RedBean ORM).
To display the Help instructions:
`php user_upload.php --help`

