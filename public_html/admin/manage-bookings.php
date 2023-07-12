<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //todo update transaction
                if (isset($_POST["update"])) {
                    $bookingID = htmlspecialchars($_POST["booking_id"]);
                    $bookingStatus = htmlspecialchars($_POST["status"]);

                    updateBooking($bookingID, $bookingStatus) or throw new Exception("Couldn't update transaction status");

                    //notify user here via mail
                    require_once("../../mail.inc.php");
                    $booking = retrieveBooking($bookingID);
                    $fullName = $booking["user_fname"]." ".$booking["user_lname"];

                    $bookingReference = $booking["booking_reference"];
                    $date = date_create($booking["date_created"]);
                    $dateFormatted = date_format($date, "d M Y");

                    $cost = number_format((float)$booking['booking_cost'], 2, ".", ",");
                    $tripType = ucfirst(strtolower($booking['trip_type']));

                    $subject = "";
                    $content = "";
                    if ($bookingStatus === "PENDING"){
                        $subject = "Your Booking Request #{$bookingReference} is Pending";
                        $content = "<p>Dear {$fullName},</p>
                <p>Your booking with reference number <strong>{$bookingReference}</strong> is currently pending review. We are processing your request and will provide an update shortly.</p>
                <p>Thank you for choosing our airline.</p>";
                    }
                    else if ($bookingStatus === "COMPLETED") {
                        $subject = "Your Booking #{$bookingReference} is Confirmed";
                        $content = "<p>Dear {$fullName},</p>
                <p>We are pleased to inform you that your booking with reference number <strong>{$bookingReference}</strong> has been successfully confirmed. Please find the details below:</p>
                <ul>
                    <li>Booking Reference: {$bookingReference}</li>
                    <li>Date Created: {$dateFormatted}</li>
                    <li>Total Cost: {$cost}</li>
                    <li>Trip Type: {$tripType}</li>
                </ul>
                <p>Thank you for choosing our airline. We look forward to serving you.</p>";
                    }
                    else if ($bookingStatus === "REJECTED") {
                        $subject = "Your Booking Request #{$bookingReference} has been Rejected";
                        $content = "<p>Dear {$fullName},</p>
                <p>We regret to inform you that your booking request with reference number <strong>{$bookingReference}</strong> has been rejected. If you have any questions or require further assistance, please contact our customer support.</p>
                <p>Thank you for considering our airline.</p>";
                    }
                    else if ($bookingStatus === "REFUNDED") {
                        $subject = "Your Booking #{$bookingReference} has been Refunded";
                        $content = "<p>Dear {$fullName},</p>
                <p>We would like to inform you that your booking with reference number <strong>{$bookingReference}</strong> has been refunded. The refunded amount will be credited to your original payment method. If you have any questions or concerns, please don't hesitate to reach out to us.</p>
                <p>Thank you for your understanding.</p>";
                    }
                    else {
                        throw new Exception("Order status does not exist!");
                    }

                    $body = "<h1>Dear {$fullName},</h1>
                             {$content}
                             <p>Sincerely,</p>
                             <p>AirAsia Team</p>";

                    sendMail($booking["booking_email"], $subject, $body) or throw new Exception("Message wasn't sent!");

                    makeToast("success", "Transaction status successfully updated!", "Success");
                }

                //todo delete booking
                if (isset($_POST["delete"])) {
                    $bookingID = htmlspecialchars($_POST["booking_id"]);

                    deleteBooking($bookingID) or throw new Exception("Couldn't delete booking");
                    makeToast("success", "Booking successfully deleted!", "Success");
                }
            }
            else{
                makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
            }
        }
        else {
            throw new exception("Token not found");
        }
    }
    catch (exception $e){
        makeToast("error", $e->getMessage(), "Error");
    }

    header("Location: /admin/manage-bookings.php");
    die();
}

displayToast();

$bookingsCount = retrieveCountBookings()["count"] ?? 0;
$bookings = retrieveAllBookings();

$token = getToken();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Admin Dashboard</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Manage Bookings") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3"><?= $bookingsCount ?> bookings found</span>
                        </div>
                        <div class="row mt-3">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Booking Reference</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Trip Type</th>
                                    <th scope="col">Cost</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Update Transaction</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                admin_displayBookings($bookings);
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- modal delete -->
                    <div class='modal fade' id='deleteStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title'>Delete user?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Danger</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="" name="delete" value="1" class='btn btn-danger'>I understand</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal update -->
                    <div class='modal fade' id='updateStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title'>Update bookings transaction?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-warning-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">The customer will be notified by the change if you proceed.
                                            <br>Proceed with caution.</span>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-update" form="" name="update" value="1" class='btn btn-danger'>I understand</button>
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
<script type="text/javascript" src="/assets/js/modal.js"></script>
</body>

</html>