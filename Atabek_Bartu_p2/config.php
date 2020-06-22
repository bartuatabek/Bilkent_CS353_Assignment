<?php
// Database credentials.
define('DB_SERVER', '139.179.11.31');
define('DB_USERNAME', 'bartu.atabek');
define('DB_PASSWORD', '6LBgstuF');
define('DB_NAME', 'bartu_atabek');

/* Attempt to connect to MySQL database */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("ERROR: Could not connect. " . mysqli_connect_error());
?>