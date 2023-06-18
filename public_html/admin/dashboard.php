<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

$user = retrieveCountUsers()["count"] ?? 0;
$flight = retrieveCountFlights()["count"] ?? 0;
$booking = retrieveCountBookings()["count"] ?? 0;
$income = retrieveIncome()["income"] ?? 0;

$incomeDecimal =  number_format((float)$income, 2, '.', '');


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
            <?php admin_header_bar("Dashboard") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <!-- USER COUNT -->
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <div class="row">
                                    <span class="fs-2"><?= $user; ?></span>
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
                    <div class="col">
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
                    <!-- Maybe traffic and bookings here? todo -->
                    <div class="col">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="row">
                                <span class="h3">Recent Bookings</span>
                            </div>
                            <div class="row">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">First</th>
                                        <th scope="col">Last</th>
                                        <th scope="col">Handle</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                    </tr>
                                   <?php

                                   ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Maybe traffic here? todo -->
                </div>
            </div>




            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
</body>

</html>