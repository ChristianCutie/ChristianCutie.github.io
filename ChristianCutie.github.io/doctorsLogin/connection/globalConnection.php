<?php

function connection(){
    
$host = "localhost";
$username = "root";
$password = "";
$database = "sampledatabase_db";
$port = 3306;

    $con = new mysqli($host, $username, $password, $database, $port);

    if($con->connect_error){
    
        die("Connection failed: " . $con->connect_error);
    }
    else{
       return $con;
    }
}
?>