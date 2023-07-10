<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if (!isset($_SESSION["booking_id"])) {
    makeToast("warning", "Booking doesn't exist! PLease make a book first!", "Warning");
    header("Location: /index.php");
    die();
}

try {
    $bookingID = $_SESSION["booking_id"];
    $booking = retrieveBookingByUser($bookingID, $_SESSION["user_data"]["user_id"]);
}
catch (Exception $e) {
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /index.php");
    die();
}



displayToast();
?>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/invoice.css">
    <title><?= config("name") ?> | Booking Confirmation</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Booking Confirm") ?>

            <div class="container py-5">
                <div class="row mb-5">
                    <div class="col text-center">
                        <h1 class="fs-1">Thank you for flying with AirAsia!</h1>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-body p-3 my-5" id="invoice">
                                <h3 class="text-center card-title">Booking Confirmation</h3>

                                <?php book_invoiceBooking($booking); ?>

                                <div class="text-center">
                                    <a href="/" class="btn btn-link d-print-none me-1">Back to Home</a>
                                    <button class="btn btn-danger d-print-none" style="width: 10%;" onclick="window.print()">Print</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <?php footer(); ?>
        </main>
    </div>
</div>

<?php body_script_tag_content();?>
</body>
</html>
