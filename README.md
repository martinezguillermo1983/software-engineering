# software-engineering

SETUP INSTRUCTIONS

This webapp runs over a WAMP, LAMP or MAMP configuration. Make sure your Apache REWRITE module is enabled and follow these 4 steps:

1- Uncompress the zip file and copy the folder 'software-engineering-master' into your 'www' folder (or whatever is the folder your 'localhost' points to).

2- Besides the PHP code, you'll also need to setup the MySQL database. Create a new database called 'flight_finder' and import the file basicDatabase.sql in it.

3- The project has a file called config.php where you can adjust your DB credentials (db_username and db_password) to make them match with your local MySQL user.

4- If all the above was properly done, just open the browser, navigate to 'http://localhost/software-engineering-master' and the site should show up.
