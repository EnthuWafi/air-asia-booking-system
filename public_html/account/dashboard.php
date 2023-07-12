<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

displayToast();

//I FUCKING HATE THIS SHIT
$user = $_SESSION["user_data"];
$name = $user["user_fname"] ?? "";
$today = date_create("now");
$date = date_format($today, "D, d M Y");

$bookingsCount = retrieveBookingCountUser($user["user_id"])["count"] ?? 0;
$totalSpend = retrieveUserTotalSpend($user["user_id"])["income"] ?? 0;
$flightCount = retrieveFlightCountUser($user["user_id"])["count"] ?? 0;

$totalSpendDecimal = number_format((float)$totalSpend, 2, ".", ",");
$bookings = retrieveAllUserBookingsLIMIT5($user["user_id"]);

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Dashboard</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Dashboard") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="row">
                        <div class="col-auto">
                            <span class="h3">Hello there, <?= $name ?? "-" ?></span><br>
                            <span class="lead">Today is <?= $date ?></span>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 gx-4 ms-3">
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $bookingsCount; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-muted">Bookings</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bx bxs-plane icon-red h2"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $flightCount; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-muted">Flight Booked</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class='bx bxs-plane-alt icon-red h2'></i>
                            </div>
                        </div>
                    </div>
                    <!-- TOTAL SPENT-->
                    <div class="col-4">
                        <div class="shadow p-3 gradient-primary rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2 text-white">RM<?= $totalSpendDecimal ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-white">Total Spent</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bi bi-cash-coin h2 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 ms-3">
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <span class="h3">Recent User Bookings</span>
                                </div>
                                <div class="col-auto">
                                    <span class="h5"><span id="booking-count">0</span> Bookings</span>
                                </div>
                            </div>
                            <div class="row">
                                <table class="table table-responsive table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Booking Reference</th>
                                        <th scope="col">Trip Type</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Cost</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $bookingStatus = retrieveBookingStatus();
                                    $optionContent = "";
                                    foreach ($bookingStatus as $status) {
                                        $statusUC = ucfirst(strtolower($status["booking_status"]));
                                        $optionContent .= "<option value='{$status["booking_status"]}'>{$statusUC}</option>";
                                    }

                                    if ($bookings != null) {
                                        $count = 1;
                                        foreach ($bookings as $booking) {
                                            $tripType = $booking["trip_type"];
                                            $tripTypeStr = $tripType == "ONE-WAY" ? "One-way Trip" :
                                                ($tripType == "RETURN" ? "Round-trip" : "null");

                                            $status = ["status"=>ucfirst(strtolower($booking["booking_status"])), "class"=>strtolower($booking["booking_status"])];
                                            $bookingCost = number_format((float)$booking["booking_cost"], 2, '.', '');


                                            echo "
<tr class='align-middle'>
    <th scope='row'>$count</th>
    <td><a class='text-decoration-none fw-bold' href='/admin/view-booking.php?booking_id={$booking["booking_id"]}'>
{$booking["booking_reference"]}</a></td>
    <td>{$tripTypeStr}</td>
    <td><span class='{$status["class"]}'>{$status["status"]}</span></td>
    <td>RM{$bookingCost}</td>
    <td class='text-center'>
        <a type='button' class='btn btn-outline-primary' href='/account/manage-my-bookings.php/#{$booking["booking_id"]}'>
            <i class='bi bi-three-dots'></i> See More
        </a>
    </td>
</tr>";
                                            $count++;
                                        }
                                        $count--;
                                        echo "<script>$('#booking-count').html(\"{$count}\");</script>";
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No bookings found</td></tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
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