<?php
require 'constants.php';

//connect to database
$connection = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if(mysqli_errno($connection)){
    die(mysql_error($connection));
}