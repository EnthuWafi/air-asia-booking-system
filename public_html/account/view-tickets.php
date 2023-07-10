<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();

if (!isset($_GET["booking_id"])) {
    makeToast("warning", "Booking doesn't exist!", "Warning");
    header("Location: /account/manage-my-bookings.php");
    die();
}

try {
    $bookingID = $_GET["booking_id"];
    $booking = retrieveBookingByUser($bookingID, $_SESSION["user_data"]["user_id"]);

    //check if booking completed
    if ($booking["booking_status"] !== "COMPLETED") {
        throw new Exception("Booking is not completed!");
    }
}
catch (exception $e) {
    makeToast("warning", $e->getMessage(), "Warning");
    header("Location: /account/manage-my-bookings.php");
    die();
}



displayToast();
?>
<!DOCTYPE html>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/ticket.css">
    <title><?= config("name") ?> | View Tickets</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("View Tickets") ?>

            <div class="container py-5">
                <div class="row mt-4">
                    <div class="col">
                        <div class="card" id="tickets">
                            <div class="card-body p-3 my-2" id="tickets">

                                <?php book_ticketList($booking); ?>

                                <div class="text-center">
                                    <button class="btn btn-danger d-print-none me-2 px-4" onclick="history.back();">Back</button>
                                    <button class="btn btn-danger d-print-none px-4" onclick="window.print();">Print</button>
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
