<?php
include "config.php";

$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(mysqli_connect_errno()) {
    die("DB connection failed: " . mysqli_connect_errno() . " (" . mysqli_connect_errno() . ")"
    );
}