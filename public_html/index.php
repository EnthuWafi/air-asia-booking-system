<?php

require("../includes/functions.inc.php");

session_start();

if (isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>airasia | Flights, and More</title>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <div class="d-flex">
                <a class="navbar-brand" href="index">
                    <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <?php
                    if (isset($user)) {
                        ?>
                        <a class="nav-link" href="/content/flightsearch.php">Search Flight</a>
                        <a class="nav-link" href="#">My Bookings</a>
                        <?php
                    }
                    ?>
                    <?php
                    if (isset($user)) {
                        ?>
                            <span>Hello there, <?php echo $_SESSION["username"] ?></span>
                            <a class="nav-link me-auto" href="/logout.php">Log out</a>
                        <?php
                    } else {
                        ?>
                            <a class="nav-link me-auto" href="/login.php">Log in</a>
                            <a class="nav-link" href="/register.php">Register</a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>
    <hr>
    <!-- Content -->


    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>