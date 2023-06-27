<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

$userCount = retrieveCountUsers()["count"] ?? 0;
$flight = retrieveCountFlights()["count"] ?? 0;
$booking = retrieveCountBookings()["count"] ?? 0;
$income = retrieveIncome()["income"] ?? 0;

$incomeDecimal =  number_format((float)$income, 2, '.', '');

$today = date_create("now");
$date = date_format($today, "D, d M Y");

$user = $_SESSION["user_data"];
$name = "{$user["user_fname"]} {$user["user_lname"]}";

//chart
$traffics = retrieveTraffic(); //all
$trafficCount = retrieveTrafficCount()["count"] ?? 0;

$max = $trafficCount * 1.2;
$y = 0;
$dataPoints = array();
foreach($traffics as $traffic) {
    $datetime = new DateTime($traffic["timestamp"]);
    $timestamp = $datetime->getTimestamp();
    $timestamp *= 1000;
    $y += 1;
    array_push($dataPoints, array("x" => $timestamp, "y" => $y));
}


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
                        <span class="h3">Hello there, <?= $name ?? "-" ?></span>
                        <span class="lead">Today is <?= $date ?></span>
                    </div>
                </div>
                <div class="row mt-4 ms-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link icon-red" aria-current="page" href="/admin/dashboard.php">All-Time</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link icon-red" href="/admin/dashboard-monthly.php">Monthly</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link icon-red" href="/admin/dashboard-daily.php">Daily</a>
                        </li>
                    </ul>
                </div>

                <div class="row mt-4 gx-4 ms-3">
                    <!-- USER COUNT -->
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
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
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
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
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
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
                <div class="row mt-1 gx-4 ms-3">
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded">
                            <?php makeChart($dataPoints, "chart", $max); ?>
                        </div>
                    </div>
                    <!-- Maybe bookings here? todo -->
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="row">
                                <span class="h3">Recent Bookings</span>
                            </div>
                            <div class="row">

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