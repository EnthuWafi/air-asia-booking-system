<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if (!isset($_GET["booking_id"])) {
    makeToast("warning", "Booking doesn't exist!", "Warning");
    header("Location: /index.php");
    die();
}

$bookingID = $_GET["booking_id"];
$flight = retrieveBookingByUser($bookingID, $_SESSION["user_data"]["user_id"]);

if (empty($flight)) {
    makeToast("warning", "Booking doesn't exist!", "Warning");
    header("Location: /index.php");
    die();
}

displayToast();
?>
<!DOCTYPE html>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/invoice.css">
    <title><?= config("name") ?> | View Booking</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("View Booking") ?>

            <div class="container py-5">
                <div class="row mt-4">
                    <div class="col">
                        <div class="card" id="invoice">
                            <div class="card-body p-3 my-5">
                                <h3 class="text-center card-title">Booking Invoice</h3>

                                <?php book_invoiceBooking($flight); ?>

                                <div class="text-center">
                                    <button class="btn btn-danger d-print-none me-2 px-4" onclick="history.back();"><i class="bi bi-arrow-left-circle"></i> Back</button>
                                    <?php
                                    if ($flight["booking_status"] === "COMPLETED") {
                                        ?>
                                        <a class="btn btn-danger d-print-none px-4" href="/account/view-tickets.php?booking_id=<?= $flight["booking_id"] ?>"><i class="bi bi-ticket"></i> Tickets</a>
                                    <?php
                                    }
                                    ?>

                                    <button class="btn btn-danger d-print-none px-4" onclick="window.print();"><i class="bi bi-printer"></i> Print</button>
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
