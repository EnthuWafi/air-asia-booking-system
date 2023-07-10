<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

if (isset($_GET["q"])){
    $query = htmlspecialchars($_GET["q"]);

    $bookings = retrieveAllBookingUserLike($_SESSION["user_data"]["user_id"], $query);
}
else {
    makeToast("Warning", "Query was not found!", "Warning");
    header("Location: /admin/dashboard.php");
    die();
}

displayToast();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Search Result</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Search Result") ?>

            <!-- todo users here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="p-3 mb-5 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3"><span id="booking-count">0</span> bookings found</span>
                        </div>
                        <div class="shadow p-3 mb-3 mt-3 bg-body rounded row gx-3 mx-1">
                            <div class="col">
                                <span class="fs-1 mb-3">Bookings</span>
                            </div>
                            <!-- custoemr bookkigns -->
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
    <td><a class='text-decoration-none fw-bold' href='/account/view-booking.php?booking_id={$booking["booking_id"]}'>
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
                                    echo "<tr><td colspan='6' class='text-center'>No bookings found</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
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