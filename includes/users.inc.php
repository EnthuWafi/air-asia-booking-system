<?php
require_once("functions.inc.php");
/* USER RELATED */
//create customer/admin
function createUser($username, $password, $email, $user_type) {
    if (!($user_type == "customers" || $user_type == "admins")) {
        die();
    }

    $conn = OpenConn();
    $sqlQueryFirst = "INSERT INTO users(username, password, email, user_type) VALUES (?, ?, ?, ?)";
    $sqlQueryFirstID = "SET @last_user_id = LAST_INSERT_ID()"; //@last_user_id
    //check user types
    if ($user_type == "customers") {
        $sqlQuerySecond = "INSERT INTO customers(user_id) VALUES (@last_user_id)";
    }
    else {
        //dumb algorithm to get unique admin code
        $sqlQuerySecond = "INSERT INTO admins(user_id, admin_code) VALUES 
        (@last_user_id, CONCAT(DATE_FORMAT(NOW(),'%Y%c%d%s'), @last_user_id))";
    }

    try {
        $conn->execute_query($sqlQueryFirst, [$username, password_hash($password, PASSWORD_DEFAULT), $email, $user_type]);
        $conn->query($sqlQueryFirstID);
        if ($conn->execute_query($sqlQuerySecond)){
            CloseConn($conn);
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        header("Location: /index.php");
        die();
    }

    return false;
}
//check user exists
function checkUser($username): bool
{
    $sql = "SELECT * FROM users WHERE username = ?";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$username]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function checkUserType($userID){
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$userID]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row["user_type"];
    }
    return null;
}

//verify user (return customer/admin)
function verifyUser($username, $password) {
    $sql = "SELECT us.* FROM users us WHERE us.username = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$username]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            //check password
            if (password_verify($password, $row["password"])){
                return $row;
            }
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;
}


//retrieve bookings
function retrieveAllUserBookings($userId) {
    $sql = "SELECT b.* FROM bookings b
            WHERE b.user_id = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userId]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;
}

//counts (specifically for admins)
function retrieveCountBookings() {
    $sql = "SELECT COUNT(booking_id) as 'count' FROM bookings";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count bookings!");
    }

    return null;
}

function retrieveCountFlights() {
    $sql = "SELECT COUNT(flight_id) as 'count' FROM flights";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count flights!");
    }

    return null;
}

function retrieveCountUsers() {
    $sql = "SELECT count(user_id) as 'count' FROM users";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count users!");
    }

    return null;
}

function retrieveCountAdminUsers() {
    $sql = "SELECT count(user_id) as 'count' FROM users where user_type = 'admin'";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count users!");
    }

    return null;
}
function retrieveCountCustomerUsers() {
    $sql = "SELECT count(user_id) as 'count' FROM users where user_type = 'customer'";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve count users!");
    }

    return null;
}

function retrieveIncome() {
    $sql = "SELECT sum(b.booking_cost) as 'income' FROM bookings b
            WHERE b.booking_status = 'COMPLETED'";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql,);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve income!");
    }

    return null;
}

function retrieveAllAdminUsers() {
    $sql = "SELECT u.*, a.admin_code
            FROM users u
            INNER JOIN admins a on u.user_id = a.user_id";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get users!");
    }

    return null;
}

function retrieveAllCustomerUsers() {
    $sql = "SELECT u.*, c.customer_phone, c.customer_dob
            FROM users u
            INNER JOIN customers c on u.user_id = c.user_id";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get users!");
    }

    return null;
}