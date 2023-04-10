<?php
function OpenConn() 
{
    $host = "localhost";
    $username = "root";
    $password = "";
    $db = "air-asia-booking-system";

    // Create connection
    try {
    $conn = mysqli_connect($host, $username, $password, $db);
    }
    catch(mysqli_sql_exception){
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function CloseConn($conn)
{
    $conn -> close();
}