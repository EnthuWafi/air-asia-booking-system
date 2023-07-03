<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if (isset($_GET["q"])){
    $query = htmlspecialchars($_GET["q"]);

    $bookings = retrieveAllBookingLike($query);
    $customerUsers = retrieveAllCustomerLike($query);
    $adminUsers = retrieveAllAdminLike($query);
    $flights = retrieveAllFlightLike($query);
    $aircrafts = retrieveAllAircraftLike($query);
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
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Search Result") ?>

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
                            <?php admin_displayBookingsLite($bookings); ?>
                        </div>
                        <hr/>

                        <div class="shadow p-3 mb-3 mt-3 bg-body rounded row gx-3 mx-1">
                            <div class="row mt-5">
                                <span class="h3"><span id="admin-count">0</span> admins found</span>
                            </div>
                            <div class="row">
                                <span class="fs-1 mb-3">Admins</span>
                            </div>
                            <div class="row">
                                <?php admin_displayAdminUserLite($adminUsers); ?>
                            </div>
                            <div class="row mt-5">
                                <span class="h3"><span id="customer-count">0</span> customer found</span>
                            </div>
                            <div class="row">
                                <span class="fs-1 mb-3">Customers</span>
                            </div>
                            <div class="row">
                                <?php admin_displayCustomerUserLite($customerUsers); ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mt-5">
                            <span class="h3"><span id="flight-count">0</span> flights found</span>
                        </div>
                        <div class="shadow p-3 mb-3 mt-3 bg-body rounded row gx-3 mx-1">
                            <div class="row">
                                <span class="fs-1 mb-3">Flights</span>
                            </div>
                            <div class="row">
                                <?php admin_displayFlightLite($flights); ?>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mt-5">
                            <span class="h3"><span id="aircraft-count">0</span> aircrafts found</span>
                        </div>
                        <div class="shadow p-3 mb-3 mt-3 bg-body rounded row gx-3 mx-1">
                            <div class="row">
                                <span class="fs-1 mb-3">Aircrafts</span>
                            </div>
                            <div class="row">
                                <?php admin_displayAircraftLite($aircrafts); ?>
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