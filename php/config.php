<?php

$host = "localhost";
$user = "root";
$pwd = "";
$dbname = "helloworlddb";

$conn = mysqli_connect($host,$user, $pwd, $dbname);
if(!$conn){
    die("Not connected");
}
?>