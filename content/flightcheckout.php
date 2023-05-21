<?php

session_start();
require("../includes/functions.inc.php");


login_required();

if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
}

echo $_POST["flight_id"];
if (isset($_POST["flight_id"])){
    $_SESSION["flight_id"] = $_POST["flight_id"]; 
}
//Prevent user from continuing
// if (empty($_SESSION["flight_id"])){
//     header("Location: /index.php");
//     die();
// }

?>
<!DOCTYPE html>
<html>

<head>
    <title>Flight Check-out</title>
</head>

<body>
    <div class="d-flex flex-row">
        <a class="navbar-brand" href="/index.php">
            <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
        </a>
    </div>
    <h1>Flight Check-out</h1>
    <hr>
    
</body>
<script>

</script>

</html>