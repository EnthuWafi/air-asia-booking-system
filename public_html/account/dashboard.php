<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title>Dashboard</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Dashboard") ?>

            <!--  DASHBOARD here todo -->
            <div class="row m-5 ms-3">
                <span class="fs-1">Dashboard</span>
            </div>
            <?php


            ?>

            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
</body>

</html>