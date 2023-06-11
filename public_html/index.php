<?php

require("../includes/functions.inc.php");

session_start();

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="/assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/index.css" rel="stylesheet">
    <!-- BOXICONS -->
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <!-- TOASTR -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <title><?= config("name") ?> | Flights, and More</title>
</head>

<body>
    <?php
    if (isset($_SESSION["alert"])){
        showToastr($_SESSION["alert"]);
        unset($_SESSION["alert"]);
    }
    ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg shadow p-3 bg-white rounded">
        <div class="container-fluid">
            <div class="d-flex">
                <a class="navbar-brand order-first" href="index.php">
                    <img class="img-fluid w-50" src="/assets/img/airasiacom_logo.svg">
                </a>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav me-auto">
                    <a class="nav-link active" href="/index.php">Home</a>
                    <a class="nav-link" href="/flight/search.php">Search Flight</a>
                    <a class="nav-link" href="#">My Bookings</a>
                </div>
                <div id="right-most-no-login" class="navbar-nav ms-auto">
                    <a class="nav-link me-auto" href="/login.php">Log in</a>
                    <a class="nav-link" href="/register.php">Register</a>
                </div>
                <div id="right-most-login" class="navbar-nav ms-auto order-last">
                    <span class="align-bottom d-inline">Hello there, <?= ($_SESSION["user_data"]["username"] ?? "") ?></span>
                    <a class="nav-link me-auto" href="/logout.php">Log out</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div>
        <div class="gradient-primary position-absolute h-50 w-100">
        </div>
        <div class="container pt-4">
            <div class="position-absolute">
                <h1 class="logo-airasia">Start Travelling with AirAsia!</h1>
                <h5 style="color: white;">Get flights and hotels worldwide for your trip with the best deals</h5>
            </div>
            <!-- form here -->
            <form>
            </form>
        </div>
    </div>

    <!-- JS -->
    <script>
        const userData = <?= json_encode(isset($_SESSION["user_data"]) ?? "") ?>;
    </script>
    <script type="module" src="/assets/js/index.js"></script>
</body>

</html>