<?php

require("./includes/functions.inc.php");

session_start();

if (isset($_SESSION['user_id'])){
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
        <nav class="navbar">
            <div>
                <img src="assets/img/airasiacom_logo.svg">
            </div>
            <div class="d-lg-flex flex-row">
                <?php
                if (isset($user)){
                    echo <<< HTML
                    <a href="#">Explore</a>
                    <a href="#">My Bookings</a>
                    <a href="#">Check-in</a>
                    <a href="#">Flight Status</a>
                    HTML;
                }
                ?>
            </div>
            <div>
                <?php
                //if logged in
                $nav_user = null;
                if (isset($user)){
                    $nav_user = <<< HTML
                    Hello there, {$user}
                    <a href="content/logout">Log out</a>
                    HTML;
                }
                else {
                    $nav_user = <<< HTML
                    <a href="content/login">Log in</a>
                    <a href="content/register">Register</a>
                    HTML;
                }
                echo $nav_user;
                ?>
            </div>
        </nav>

        <!-- Content -->
        

        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    </body>
</html>