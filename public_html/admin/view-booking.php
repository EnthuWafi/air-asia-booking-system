<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

displayToast();

if (!$_GET || empty($_GET["booking_id"])){
    header("Location: /admin/manage-my-bookings.php");
    die();
}

$booking = retrieveBooking(htmlspecialchars($_GET["booking_id"]));

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | View Booking</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("View Booking") ?>

            <!-- todo view booking here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3">Booking Reference Number <span class="text-info">#<?= $booking["booking_reference"] ?></span>
                            </span>
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