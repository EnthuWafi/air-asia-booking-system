<?php

session_start();
require("../../includes/functions.inc.php");

customer_login_required();



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //cancel booking
                if (isset($_POST["cancel"])) {
                    $bookingID = htmlspecialchars($_POST["booking_id"]);
                    $booking = retrieveBooking($bookingID);
                    require("../../mail.inc.php");
                    //send to literally every admin
                    $user = $_SESSION["user_data"];
                    $amount = number_format((float)$booking["booking_cost"], 2, ".", ",");

                    $subject = "Request for Booking #{$booking["booking_reference"]} Cancellation";
                    $content = "<p>Dear every Admin,</p>
                    
                    <p>A cancellation has been requested by the user <strong>{$user["username"]}</strong> for the following booking:</p>
                    
                    <ul>
                        <li><strong>Booking Reference:</strong> {$booking["booking_reference"]}</li>
                        <li><strong>Amount Paid:</strong> RM{$amount}</li>
                    </ul>
                    
                    <p>Please process the cancellation and refund according to the applicable policies and procedures.</p>
                    
                    <p>Thank you for your attention to this matter.</p>
                    ";
                    $admins = retrieveAllAdminUsers();
                    foreach ($admins as $admin) {
                        sendMail($admin["email"], $subject, $content) or throw new Exception("Message wasn't sent");
                    }

                    makeToast("success", "Request successfully sent!", "Success");
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

    header("Location: /account/manage-my-bookings.php");
    die();
}

$bookings = retrieveAllUserBookings($_SESSION["user_data"]["user_id"]);
$token = getToken();

$countBooking = 0;
displayToast();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | My Bookings</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Manage My Bookings") ?>

            <!--  BOOKINGS HERE todo -->

            <div class="container py-4 px-4 pb-5 mt-3">
                <div class="row mx-3 my-2">
                    <span class="fs-1"><span id="count-booking">0</span> bookings found</span>
                </div>
                <hr>
                <?php
                if ($bookings != null) {
                    foreach ($bookings as $booking) {
                        $flights = retrieveBookingFlights($booking["booking_id"]);

                        if (empty($flights)) {
                            continue;
                        }

                        //status = upcoming, departed, in progress
                        $today = date("Y-m-d H:i:s");
                        $departureDate = $flights[0]["departure_time"];
                        $arrivalDate = $flights[0]["arrival_time"];
                        $departureUnformatted = date_create($departureDate);
                        $arrivalUnformatted = date_create($arrivalDate);
                        $departureFormatted = date_format($departureUnformatted, "d M Y");


                        $isReturnTicket = $booking["trip_type"] === "RETURN" and isset($flights[1]);
                        if ($isReturnTicket) {
                            $departureReturnDate = $flights[1]["departure_time"];
                            $departureReturnUnformatted = date_create($departureReturnDate);
                            $departureReturnFormatted = date_format($departureReturnUnformatted, "d M Y");
                        }


                        //elements
                        $arrow = $isReturnTicket ? "<i class='bi bi-arrow-left-right'></i>" : "<i class='bi bi-arrow-right'></i>";
                        $returnDepartureText = $isReturnTicket ? "Return: {$departureReturnFormatted}" : "";

                        //just borrowing hehe
                        $flightDiv = admin_bookingFlightsDisplay($flights);

                        $statusLower = strtolower($booking["booking_status"]);

                        echo "
<div class='shadow p-5 bg-body rounded my-2' id='{$booking["booking_id"]}'>
    <div class='row'>
        <div class='col'>
            <div class='row justify-content-between'>
                <div class='col-auto col-sm-auto'>
                    <div class='row'>
                        <span class='fs-2'>
                              {$flights[0]["origin_airport_code"]} {$arrow} {$flights[0]["destination_airport_code"]}                  
                        </span>
                    </div>
                    <div class='row'>
                        <span class='text-muted'> Departure: {$departureFormatted}</span>
                        <span class='text-muted'> {$returnDepartureText}</span>
                    </div>
                </div>
                <div class='col-auto text-end mt-2'>
                    
                    <div class='row'>
                        <strong>Booking Reference No:</strong><em class='fs-4'>{$booking["booking_reference"]}</em>
                    </div>
                    <div class='row'>
                        <strg>Booking Status:</strg><em class='$statusLower'>{$booking["booking_status"]}</em>
                    </div>
                </div>
            </div>
            <div class='row mt-5'>
                <h2>Flights Included: </h2>
            </div>
            <div class='row mt-2'>
                {$flightDiv}
            </div>
            <div class='row mt-3'>
                
                <div class=\"dropdown text-end\">
                  <button class=\"btn btn-dark dropdown-toggle\" type=\"button\" id=\"dropdown\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
                    Options
                  </button>
                  <ul class=\"dropdown-menu\" aria-labelledby=\"dropdown\">
                    <li><a class=\"dropdown-item\" data-bs-toggle='modal' data-bs-target='#cancelStatic' onclick='updateElement({$booking["booking_id"]}, \"cancel\", \"booking_id\")'>
                    <i class='bi bi-x-circle'></i> Cancel</a></li>
                    <li><a class=\"dropdown-item\" href=\"/account/view-booking.php?booking_id={$booking["booking_id"]}\"><i class='bi bi-eye'></i> View</a></li>
                  </ul>
                </div>
                
            </div>
        </div>
    </div>
</div>";
                        $countBooking++;
                    }
                }
                ?>

            </div>

            <!-- modal cancel -->
            <div class='modal fade' id='cancelStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header bg-light-subtle'>
                            <h5 class='modal-title' id='staticBackdropLabel'>Cancel booking?</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body bg-warning-subtle'>
                            <div class="px-3" id="modal-cancel-view">
                                <div class="mb-1">
                                    <span class="fw-bolder">Warning</span>
                                </div>
                                <span class="text-black mt-3">This action will notify administrator!<br>It may take a little while.</span>
                            </div>
                            <div id="wait" class="align-middle ms-auto me-auto py-4">
                                <div class="spinner-border text-warning ms-4" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">This will take a little while...</span>
                            </div>
                            <form id="cancel" method="post" action="/account/manage-my-bookings.php" >
                                <input type="hidden" name="booking_id" value="">
                                <input type="hidden" name="token" value="<?= $token ?>">
                            </form>
                        </div>
                        <div class='modal-footer bg-light-subtle'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                            <button type='submit' form="cancel" name="cancel" value="1" onclick="waitAnim()" class='btn btn-danger'>I understand</button>
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
<script>
    const modalView = document.getElementById("modal-cancel-view");
    const wait = document.getElementById("wait");


    function waitAnim() {
        wait.style.display = "block"; // Show the wait element
        modalView.style.display = "none"; // Hide the modalView element
    }
    wait.style.display = "none";

    $('#count-booking').html("<?= $countBooking ?>");
</script>
</body>
</html>
