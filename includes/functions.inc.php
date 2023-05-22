<?php
require('connection.inc.php');
require('config.inc.php');

//functions
function current_page() {
    return htmlspecialchars($_SERVER["PHP_SELF"]);
}

//Requires login to access the site
function login_required() {
    if (empty($_SESSION["user_id"])){
        header("Location: /index.php");
        die();
    }
}

//Requires user to not be logged in to access the site (For instance, like Login page or Register page)
  function login_forbidden() {
    if (isset($_SESSION["user_id"])){
        header("Location: /index.php");
        die();
    }
}


// SQL commands
/* USER RELATED */
//create user
function createUser($username, $password, $email) {
    $sql = "INSERT INTO users(username, password, email) VALUES (?, ?, ?)";
    $conn = OpenConn();

    try {
        if ($conn->execute_query($sql, [$username, password_hash($password, PASSWORD_DEFAULT), $email])) {
            echo "You have created an account!";
        }
    }
    catch (mysqli_sql_exception){
        echo "An error occured!";
    }
    CloseConn($conn);
}
 
//check user exists
function checkUser($username) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$username]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}


//verify user (return id)
function verifyUser($username, $password) {
    $sql = "SELECT * FROM users WHERE username = ?";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$username]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_id = trim($row["user_id"]);
        //check password
        if (password_verify($password, $row["password"])){
            return $user_id;
        }       
    }
    
    return null;
}

/* FLIGHTS RELATED */
//retrieve all airport
function retrieveAirports() {
    $sql = "SELECT * FROM airports";
    $conn = OpenConn();

    $result = $conn->execute_query($sql);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);      
    }
    
    return null;
}

//retrieve flights that matches parameters 
function retrieveFlights($origin, $destination, $departure_time) {
    $sql = "SELECT fl.*, ao.airport_name as 'origin_airport_name', ad.airport_name as 'destination_airport_name'
    FROM flights fl
    INNER JOIN airports ao ON fl.origin_airport_id = ao.airport_id AND ao.airport_code = ?
    INNER JOIN airports ad ON fl.destination_airport_id = ad.airport_id AND ad.airport_code = ?
    WHERE DATEDIFF(departure_time, ?) = 0 AND departure_time > NOW()";

    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$origin, $destination, $departure_time]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);      
    }
    
    return null;
}

function retrieveFlight($flight_id) {
    $sql = "SELECT * FROM flights WHERE flight_id = ?";

    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$flight_id]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);      
    }
    
    return null;
}