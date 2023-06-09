<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

$userCount = retrieveCountUsersMonthly()["count"] ?? 0;
$flight = retrieveCountFlightsMonthly()["count"] ?? 0;
$booking = retrieveCountBookingsMonthly()["count"] ?? 0;
$income = retrieveIncomeMonthly()["income"] ?? 0;

$incomeDecimal =  number_format((float)$income, 2, '.', '');

$bookings = retrieveAllBookingsLIMIT5();
$users = retrieveAllCustomerUsersLIMIT5();

$today = date_create("now");
$date = date_format($today, "D, d M Y");

$user = $_SESSION["user_data"];
$name = "{$user["user_fname"]} {$user["user_lname"]}";

//chart
$traffics = retrieveTrafficMonthly(); //all

$visitedToday = false;

$highestPeak = 1;

$dataPoints = array();
foreach($traffics as $traffic) {
    $datetime = new DateTime($traffic["date"]);
    $timestamp = $datetime->getTimestamp();
    $timestamp *= 1000;
    $y = $traffic["count"];
    array_push($dataPoints, array("x" => $timestamp, "y" => $y));

    $interval = $today->diff($datetime);
    if ($interval->days === 0) {
        $visitedToday = true;
    }
    if ($traffic["count"] > $highestPeak) {
        $highestPeak = $traffic["count"];
    }
}
$max = $highestPeak * 1.2;
if (!$visitedToday) {
    $timestamp = $today->getTimestamp() * 1000;
    array_push($dataPoints, array("x" => $timestamp, "y" => 0));
}
displayToast();
?>
<!DOCTYPE html>
<html lang="en">

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
            <?php admin_header_bar("Dashboard") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="row">
                        <div class="col">
                            <span class="h3">Hello there, <?= $name ?? "-" ?></span><br>
                            <span class="lead">Today is <?= $date ?></span>
                        </div>
                        <div class="col-3 text-end">
                            <span class="h3 text-muted">#<?= $user["admin_code"] ?? "-" ?></span>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 ms-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link icon-red interact" aria-current="page" href="/admin/dashboard.php">All-Time</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link icon-red interact" href="/admin/dashboard-monthly.php">Monthly</a>
                        </li>
                    </ul>
                </div>

                <div class="row mt-4 gx-4 ms-3">
                    <!-- USER COUNT -->
                    <div class="col">
                        <div class="shadow p-3 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $userCount; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-muted">Users</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bi bi-people-fill icon-red h2"></i>
                            </div>
                        </div>
                    </div>

                    <!-- BOOKINGS COUNT -->
                    <div class="col">
                        <div class="shadow p-3 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $booking; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-muted">Bookings</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bi bi-calendar2-check-fill icon-red h2"></i>
                            </div>
                        </div>
                    </div>

                    <!-- FLIGHTS COUNT -->
                    <div class="col">
                        <div class="shadow p-3 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $flight; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-muted">Flights</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bi bi-airplane-fill icon-red h2"></i>
                            </div>
                        </div>
                    </div>

                    <!-- INCOME -->
                    <div class="col-4">
                        <div class="shadow p-3 mb-5 gradient-primary rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2 text-white">RM<?= $incomeDecimal; ?></span>
                                </div>
                                <div class="row">
                                    <span class="text-white">Income</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <i class="bi bi-cash-coin icon-white h2"></i>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-2 ms-2 justify-content-center">
                    <div class="col-8">
                        <div class="shadow p-3 mb-5 bg-body rounded">
                            <?php makeChart($dataPoints, "chart", $max); ?>
                        </div>
                    </div>
                    <div class="col px-3 d-xl-flex justify-content-end">
                        <div class="shadow p-3 mb-5 bg-body rounded align-self-stretch">
                            <h3 class="mx-2">Recent Customers</h3>
                            <div class="row my-2 px-3">
                                <?php admin_displayCustomerUserDashboard($users); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2 ms-3">
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <span class="h3">Recent Bookings</span>
                                </div>
                                <div class="col-auto">
                                    <span class="h5"><span id="booking-count">0</span> Bookings</span>
                                </div>
                            </div>
                            <div class="row">
                                <?php admin_displayBookingsLite($bookings); ?>
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