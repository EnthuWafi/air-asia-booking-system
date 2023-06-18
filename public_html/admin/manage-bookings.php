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
                                                <form action='manage-my-bookings.php' method='post'>
                                                    <div class='row'>
                                                        <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                                                        <div class='col-auto'>
                                                            <select class='form-select' name='status' id='floatingSelectGrid'>
                                                                {$optionContent}
                                                            </select>
                                                            <label for='floatingSelectGrid'>Status</label>
                                                        </div>
                                                        <div class='col-auto'>
                                                            <button type='submit' class='btn btn-danger mb-3' data-bs-toggle='modal' data-bs-target='#updateStatic' 
                                                            onclick='updateModal({$booking["booking_id"]}, \"modal-btn-update\");'>Update</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                            <td>
                                                <form action='manage-my-bookings.php' method='post'>
                                                    <input type='hidden' name='booking_id' value='{$booking["booking_id"]}'>
                                                    <a type='button' data-bs-toggle='modal' data-bs-target='#deleteStatic' onclick='updateModal({$booking["booking_id"]}, \"modal-btn-delete\");' class='h4'>
                                                    <i class='bi bi-trash'></i></a>
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

                    <!-- modal delete -->
                    <div class='modal fade' id='deleteStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Delete user?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="" class='btn btn-danger'>I understand</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal update -->
                    <div class='modal fade' id='updateStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticUpdateLabel'>Delete user?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-warning-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-static" form="" class='btn btn-danger'>I understand</button>
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