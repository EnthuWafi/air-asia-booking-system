<?php
session_start();
require("../../includes/functions.inc.php");


customer_login_required();

if (!$_SESSION["booking_id"]){
    die("Something went wrong..");
}
$bookingID = $_SESSION["booking_id"];


?>
<html>
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/invoice.css">
    <title>Flight Confirm</title>
</head>
<body>
<div class="container">
    <h1>Thank you for flying with AirAsia</h1>
    <!-- INVOICE HERE -->
    <div>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td class="title">
                                    <img src="/assets/img/airasiacom_logo.svg" style="width: 100%; max-width: 300px" />
                                </td>

                                <td>
                                    Invoice #: <br />
                                    Booking Reference #:
                                    Created: <br />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    AIRASIA BERHAD<br />
                                    RedQ Jalan Pekeliling 5<br />
                                    Lapangan Terbang Antarabangsa Kuala Lumpur<br/>
                                    Selangor, 64000 Malaysia<br />
                                    +60-3-86604333
                                </td>

                                <td>
                                    name<br />
                                    email<br />
                                    phone
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>Payment Method</td>
                </tr>

                <tr class="details">
                    <td>Direct Bank Transfer</td>
                </tr>

                <tr class="heading">
                    <td>Item</td>

                    <td>Price</td>
                </tr>

                <tr class="item">
                    <td>Website design</td>

                    <td>$300.00</td>
                </tr>

                <tr class="item">
                    <td>Hosting (3 months)</td>

                    <td>$75.00</td>
                </tr>

                <tr class="item last">
                    <td>Domain name (1 year)</td>

                    <td>$10.00</td>
                </tr>

                <tr class="total">
                    <td></td>

                    <td>Total: $385.00</td>
                </tr>
            </table>
        </div>
    </div>
</div>


<?php body_script_tag_content(); ?>
</body>
</html>
