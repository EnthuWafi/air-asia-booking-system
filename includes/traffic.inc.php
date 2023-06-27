<?php

function createTraffic() {
    $sql = "INSERT INTO traffic(timestamp) VALUES(DEFAULT)";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to create traffic session!");
    }

    return false;
}

function retrieveTraffic() {
    $sql = "SELECT timestamp FROM traffic
ORDER BY timestamp ASC";
    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql);
        CloseConn($conn);

        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to retrieve traffic!");
    }

    return null;
}

function retrieveTrafficCount() {
    $sql = "SELECT COUNT(traffic_id) as 'count' FROM traffic
ORDER BY timestamp ASC";
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
        die("Error: unable to retrieve traffic count!");
    }

    return null;
}