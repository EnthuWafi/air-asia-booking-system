<?php

require("../includes/functions.inc.php");

session_start();

login_required();

if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
</head>

<body>
    <h1>Dashboard</h1>
    <hr>
    <div class="d-flex flex-row">
        <a class="navbar-brand" href="/index.php">
            <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
        </a>
        <a>Flight Search</a>
        <?php
        echo "<h1>Hello, {$_SESSION["username"]}</h1>";
        ?>
        <a class="nav-link me-auto" href="/content/logout.php">Log out</a>
</div>
</body>

</html>