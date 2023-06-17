<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

$bookingsCount = retrieveCountBookings()["count"] ?? 0;
$bookings = retrieveAllBookings();
$bookingStatus = retrieveBookingStatus();

$optionContent = "";
foreach ($bookingStatus as $status) {
    $statusUC = ucfirst(strtolower($status["booking_status"]));
    $optionContent .= "<option value='{$status["booking_status"]}'>{$statusUC}</option>";
}
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
            <?php admin_header_bar("Admin Dashboard") ?>

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
                                    <th scope="col">Customers</th>
                                    <th scope="col">Trip Type</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Update Transaction</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($bookings != null) {
                                    $count = 1;
                                    foreach ($bookings as $booking) {
                                        $tripType = $booking["trip_type"];
                                        $tripTypeStr = $tripType == "ONE-WAY" ? "One-way Trip" :
                                            ($tripType == "RETURN" ? "Round-trip" : "null");

                                        $status = ["status"=>ucfirst(strtolower($tripType)), "class"=>strtolower($tripType)];
                                        $bookingCost = number_format((float)$booking["booking_cost"], 2, '.', '');

                                        echo
                                        "<tr>
                                            <th scope='row'>$count</th>
                                            <td><a class='text-decoration-none fw-bold' href='/admin/view-booking.php?booking_ref={$booking["booking_reference"]}'>
                                            {$booking["booking_reference"]}</a></td>
                                            <td>{$booking["username"]}</td>
                                            <td>{$tripTypeStr}</td>
                                            <td>RM{$bookingCost}</td>
                                            <td><span class='{$status["class"]}'>{$status["status"]}</span></td>
                                            <td>
                                                <form action='manage-bookings.php' method='post'>
                                                    <div class='row'>
                                                        <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                                                        <div class='col-auto'>
                                                            <select class='form-select' name='status' id='floatingSelectGrid'>
                                                                {$optionContent}
                                                            </select>
                                                            <label for='floatingSelectGrid'>Status</label>
                                                        </div>
                                                        <div class='col-auto'>
                                                            <button type='submit' class='btn btn-danger mb-3'>Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form action='manage-bookings.php' method='post'>
                                                    <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                                                    <button type='submit' class='h2'><i class='bi bi-trash'></i></button>
                                                </form>    
                                            </td>
                                            
                                        </tr>";
                                        $count++;
                                    }
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