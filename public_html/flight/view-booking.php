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
$booking = retrieveBooking($bookingID);

displayToast();
?>
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
                        <div class="card">
                            <div class="card-body p-3 my-5">
                                <h3 class="text-center card-title">Booking Invoice</h3>

                                <?php book_invoiceBooking($booking); ?>

                                <div class="text-center">
                                    <button class="btn btn-danger d-print-none me-2" style="width: 10%;" onclick="history.back();">Back</button>
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
