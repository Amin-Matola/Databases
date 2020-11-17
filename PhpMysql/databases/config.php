<?php


/* *
 * This file defines the necessary headers for 
 * database connection and
 * For email transmision
 * 
 * Author       : AMIN MATOLA
 * Author URI   : http://github.com/Amin-Matola
 * */


/* *
 * Set the address of the host
 * */
 define("HOST", "localhost");

/* *
 * Set the name of the database used to save data
 * */
define("DATABASE", "forms");


/* *
 * Set the name of the user with previledges in the above database
 * */
define("USERNAME", "root");


/* *
 * Set the password for the above database
 * */
define("PASSWORD", "");


/* *
 * This is optional... You can set it using the table() function as:
 * 
 * $table = table("mytable", [colums]);
 * */
define("TABLE", "store");


/* *
 * Set the email that should be used as the from address for the outgoing emails
 * */
define("ADMIN_EMAIL", "admin@codenug.com");


/* *
 * Who should your outgoing emails be carbon-copied to?
 * */
define("CC_EMAIL", "");



/* *
 * The End -- Stop Editing
 * */
