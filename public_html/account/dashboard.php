<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

displayToast();

$user = $_SESSION["user_data"];
$name = $user["user_fname"] ?? "";
$today = date_create("now");
$date = date_format($today, "D, d M Y");

$bookingsCount = retrieveBookingCountUser($user["user_id"])["count"] ?? 0;
$totalSpend = retrieveUserTotalSpend($user["user_id"])["sum"] ?? 0;
$flightCount = retrieveFlightCountUser($user["user_id"])["count"] ?? 0;

$totalSpendDecimal = number_format((float)$totalSpend, 2, ".", ",");


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
                        <span class="h3">Hello there, <?= $name ?? "-" ?></span>
                        <span class="lead">Today is <?= $date ?></span>
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
            </div>

            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
</body>

</html>