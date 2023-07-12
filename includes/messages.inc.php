<?php

function createMessage($type, $email, $message) {
    $sql = "INSERT INTO messages(message_type, message_email, message_content)
            VALUES (?, ?, ?)";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$type, $email, $message]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to create message!");
    }

    return false;
}

function deleteMessage($messageID) {
    $sql = "DELETE FROM messages WHERE message_id = ?";

    $conn = OpenConn();

    try {
        $result = $conn->execute_query($sql, [$messageID]);
        CloseConn($conn);

        if ($result) {
            return true;
        }
    }
    catch (mysqli_sql_exception){
        createLog($conn->error);
        die("Error: unable to delete message!");
    }

    return false;
}

function retrieveAllMessage() {
    $sql = "SELECT * FROM messages";

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
        die("Error: cannot retrieve messages!");
    }

    return null;
}