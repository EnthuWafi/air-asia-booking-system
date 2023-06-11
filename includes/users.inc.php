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
function checkUser($username, $user_type): bool
{
    if (!($user_type == "customers" || $user_type == "admins")) {
        die();
    }
    $sql = "SELECT * FROM users WHERE username = ? AND user_type = '".$user_type."'";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$username]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}


//verify user (return customer/admin)
function verifyUser($username, $password, $user_type) {
    if (!($user_type == "customers" || $user_type == "admins")) {
        die();
    }
    $sql = "SELECT us.*, ut.*
        FROM users us
         INNER JOIN ".$user_type." ut ON us.user_id = ut.user_id 
         WHERE us.username = ?";

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
        die();
    }

    return null;
}
