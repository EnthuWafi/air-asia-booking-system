<?php
require('connection.inc.php');
require('classes.inc.php');

//html functions
function nav_menu() {
    //TODO
    
}

function footer() {
    //todo
}

function side_bar() {
    //todo
}


function current_page() {
    return htmlspecialchars($_SERVER["PHP_SELF"]);
}







// SQL commands
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
        $user_id = trim($row["id"]);
        //check password
        if (password_verify($password, $row["password"])){
            return $user_id;
        }       
    }
    
    return null;
}


