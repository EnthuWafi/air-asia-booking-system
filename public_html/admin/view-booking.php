<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

if (!$_GET || empty($_GET["booking_id"])){
    header("Location: /admin/manage-my-bookings.php");
    die();
}

$booking = retrieveBooking(htmlspecialchars($_GET["booking_id"])) or header("Location: /admin/manage-bookings.php");
$flights = retrieveBookingFlights($booking["booking_id"]);


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
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("View Booking") ?>

            <!-- todo view booking here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-3 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3">Booking Reference Number <span class="text-info">#<?= $booking["booking_reference"] ?></span>
                            </span>
                        </div>
                        <div class="row">
                            <span class="text-muted">Created on <span class="text-body-secondary"><?= formatDateFriendly($booking["date_created"]) ?></span>
                            </span>
                        </div>
                        <div class="container">
                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <h2>Booking Details</h2>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Customer Name:</strong> <?= "{$booking["user_fname"]} {$booking["user_lname"]}" ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Booking Reference:</strong> <?= "{$booking["booking_reference"]}" ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Origin:</strong> <?= "{$flights[0]["origin_airport_state"]} ({$flights[0]["origin_airport_code"]})" ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Destination:</strong> <?= "{$flights[0]["destination_airport_state"]} ({$flights[0]["destination_airport_code"]})" ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Airline:</strong> <?= "{$flights[0]["airline_name"]}" ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <strong>Email:</strong> <?= "{$booking["booking_email"]}" ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Phone Number:</strong> <?= "{$booking["booking_phone"]}" ?>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-12">
                                    <h2>Flights Included</h2>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <?php

                                $flightDiv = admin_bookingFlightsDisplay($flights);
                                echo $flightDiv;

                                ?>
                            </div>
                            <div class="row justify-content-center mt-5 mb-2">
                                <div class="col-auto">
                                    <a type="button" class="btn btn-danger btn-block" style="width: 110px;" href="/admin/manage-bookings.php">
                                        <i class="bi bi-arrow-left-circle"></i> Back
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <a type="button" class="btn btn-danger btn-block" href="/admin/view-payment.php?booking_id=<?= $booking["booking_id"] ?>">
                                        <i class="bi bi-credit-card"></i> Payment
                                    </a>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-danger btn-block"  style="width: 110px;" data-bs-toggle="modal" data-bs-target="#staticInvoice">
                                        <i class="bi bi-receipt"></i> Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="staticInvoice" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog  modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Booking Invoice</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card" id="invoice">
                                        <div class="card-body p-3 my-5">
                                            <?php book_invoiceBooking($booking); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-danger d-print-none" onclick="window.print();window.close();">Print</button>
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