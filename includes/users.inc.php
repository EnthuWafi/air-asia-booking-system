<?php
require_once("functions.inc.php");
/* USER RELATED */
//create customer/admin
function createUser($fname, $lname, $username, $password, $email, $user_type) {
    if (!($user_type == "customer" || $user_type == "admin")) {
        die();
    }

    $conn = OpenConn();
    $sqlQueryFirst = "INSERT INTO users(user_fname, user_lname, username, password, email, user_type) VALUES (?, ?, ?, ?, ?, ?)";
    $sqlQueryFirstID = "SET @last_user_id = LAST_INSERT_ID()"; //@last_user_id
    //check user types
    if ($user_type == "customer") {
        $sqlQuerySecond = "INSERT INTO customers(user_id) VALUES (@last_user_id)";
    }
    else {
        //dumb algorithm to get unique admin code
        $sqlQuerySecond = "INSERT INTO admins(user_id, admin_code) VALUES 
        (@last_user_id, CONCAT(DATE_FORMAT(NOW(),'%Y%c%d%s'), @last_user_id))";
    }

    try {
        $conn->execute_query($sqlQueryFirst, [$fname, $lname, $username, password_hash($password, PASSWORD_DEFAULT), $email, $user_type]);
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
function checkUser($username, $email): bool
{
    $sql = "SELECT * FROM users WHERE username = ? or email = ?";
    $conn = OpenConn();

    $result = $conn->execute_query($sql, [$username, $email]);
    CloseConn($conn);

    if (mysqli_num_rows($result) > 0) {
        return true;
    }
    return false;
}

function returnUserType($userID){
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
    $sql = "SELECT us.* FROM users us WHERE us.username = ? OR us.email = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$username, $username]);
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
            return mysqli_fetch_all($result, MYSQLI_BOTH);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;
}

//retrieve bookings
function retrieveAllUserBookingsLIMIT5($userId) {
    $sql = "SELECT b.* FROM bookings b
            WHERE b.user_id = ?
            LIMIT 5";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userId]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_BOTH);
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

function retrieveCountBookingsMonthly() {
    $currentYear = date('Y');
    $currentMonth = date('m');
    $sql = "SELECT COUNT(booking_id) as 'count' FROM bookings
            WHERE YEAR(date_created) = $currentYear AND MONTH(date_created) = $currentMonth";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
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

function retrieveCountUsersMonthly() {
    $currentYear = date('Y');
    $currentMonth = date('m');
    $sql = "SELECT COUNT(user_id) as `count` FROM users WHERE YEAR(registration_date) = $currentYear AND MONTH(registration_date) = $currentMonth";
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

function retrieveIncomeMonthly() {
    $currentYear = date('Y');
    $currentMonth = date('m');
    $sql = "SELECT sum(b.booking_cost) as 'income' FROM bookings b
            WHERE b.booking_status = 'COMPLETED' AND YEAR(date_created) = $currentYear AND MONTH(date_created) = $currentMonth";
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

function retrieveAllCustomerUsersLIMIT5() {
    $sql = "SELECT u.*, c.customer_phone, c.customer_dob
            FROM users u
            INNER JOIN customers c on u.user_id = c.user_id
            ORDER BY u.registration_date DESC
            LIMIT 5";

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

function retrieveUser($userID) {
    $sql = "SELECT us.* FROM users us 
            WHERE us.user_id = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;

}

function retrieveCustomer($userID) {
    $sql = "SELECT us.*, c.* FROM users us 
            INNER JOIN customers c on us.user_id = c.user_id
            WHERE us.user_id = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;
}

function retrieveAdmin($userID) {
    $sql = "SELECT us.*, a.* FROM users us
            INNER JOIN admins a on us.user_id = a.user_id
            WHERE us.user_id = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the admin!");
    }

    return null;

}


function deleteUser($userID) {
    $sql = "DELETE FROM users WHERE user_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to delete user!");
    }

    return false;
}

function updateUser($userID, $fname, $lname, $username, $email) {
    $sql = "UPDATE users 
        SET user_fname = ?, user_lname = ?, username = ?, email = ?
        WHERE user_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$fname, $lname, $username, $email, $userID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to update user details!");
    }

    return false;
}

function retrieveUserTotalSpend($userID) {
    $sql = "SELECT sum(b.booking_cost) as 'income' FROM bookings b
            WHERE b.booking_status = 'COMPLETED' AND b.user_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$userID]);
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

function retrieveBookingCountUser($userID) {
    $sql = "SELECT COUNT(booking_id) as 'count' FROM bookings WHERE user_id = ?";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve user booking!");
    }

    return null;
}

function retrieveFlightCountUser($userID) {
    $sql = "SELECT COUNT(flight_id) as 'count' 
            FROM flights WHERE flight_id IN (
                SELECT fa.flight_id 
                FROM flight_addons fa
                INNER JOIN passengers p on fa.passenger_id = p.passenger_id
                INNER JOIN bookings b on p.booking_id = b.booking_id AND b.user_id = ?
            )";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$userID]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve user flight count!");
    }

    return null;
}



function retrieveAllCustomerLike($query) {

    $sql = "SELECT us.*, c.* FROM users us
        INNER JOIN customers c ON us.user_id = c.user_id
        WHERE us.email LIKE ? OR us.username LIKE ? OR us.user_fname LIKE ? OR us.user_lname LIKE ?";
    $query = "%{$query}%";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$query, $query, $query, $query]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot customer like query!");
    }

    return null;
}
function retrieveAllAdminLike($query) {
    $query = "%{$query}%";
    $sql = "SELECT us.*, a.* FROM users us
            INNER JOIN admins a on us.user_id = a.user_id
            WHERE us.email LIKE ? OR us.username LIKE ? OR us.user_fname LIKE ? OR us.user_lname LIKE ?
            OR a.admin_code LIKE ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$query, $query, $query, $query ,$query]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot admin like query!");
    }

    return null;
}

function updateAccount($userID, $contact) {
    $sqlFirst = "UPDATE users 
        SET user_fname = ?, user_lname = ?
        WHERE user_id = ?";
    $sqlSecond = "UPDATE customers
    SET customer_phone = ?, customer_dob = ?
    WHERE user_id = ?";

    $conn = OpenConn();

    try {
        $conn->execute_query($sqlFirst, [$contact["first_name"], $contact["last_name"], $userID]);

        $result = $conn->execute_query($sqlSecond, [$contact["phone"], $contact["dob"], $userID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to update user details!");
    }

    return false;
}

function createPasswordReset($email, $token, $expires) {
    $sql = "INSERT INTO password_reset(password_reset_email, password_reset_token, password_reset_expires)
            VALUES(?, ?, ?)";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$email, $token, $expires]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to reset password!");
    }

    return false;
}

function retrieveUserByEmail($email) {
    $sql = "SELECT us.* FROM users us 
            WHERE us.email = ?";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$email]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the user!");
    }

    return null;

}

function retrievePasswordReset($token) {
    $sql = "SELECT pr.* FROM password_reset pr
            WHERE pr.password_reset_token = ? AND pr.password_reset_expires > NOW()";

    $conn = OpenConn();

    try{
        $result = $conn->execute_query($sql, [$token]);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    catch (mysqli_sql_exception) {
        createLog($conn->error);
        die("Error: cannot get the password reset!");
    }

    return null;
}

function updatePassword($userEmail, $password) {
    $sql = "UPDATE users 
SET password = ? WHERE email = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [password_hash($password, PASSWORD_DEFAULT), $userEmail]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to reset password!");
    }

    return false;
}

function updateCheckPasswordReset($token) {
    $sql = "UPDATE password_reset SET check_used = TRUE WHERE password_reset_token = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$token]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to reset password!");
    }

    return false;
}