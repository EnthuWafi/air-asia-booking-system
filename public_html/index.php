<?php

require("../includes/functions.inc.php");

session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content() ?>
    <link href="/assets/css/index.css" rel="stylesheet">

    <title><?= config("name") ?> | Flights, and More</title>
</head>

<body>
    <?php displayToast(); ?>
    <!-- Navigation -->
    <?php nav_bar() ;?>

    <!-- Content -->
    <div>
        <div class="gradient-primary h-50 w-100">
            <div class="container pt-4" style="padding-bottom: 25%;">
                <div class="">
                    <h1 class="text-white">Start Travelling with AirAsia!</h1>
                    <h5 class="text-white"">Get flights and hotels worldwide for your trip with the best deals</h5>
                </div>
                <!-- form here -->
                <div>
                    <form>
                    </form>
                </div>

            </div>
        </div>
        <?php footer() ?>
    </div>


    <?php body_script_tag_content(); ?>
    <!-- JS -->
    <script>
        const userData = <?= json_encode(isset($_SESSION["user_data"]) ?? "") ?>;
    </script>
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/index.js"></script>
</body>

</html>