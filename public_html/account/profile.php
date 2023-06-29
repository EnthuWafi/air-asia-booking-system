<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                if (!array_keys_isset(["address", "postcode", "city", "state", "phone"], $_POST)){
                    throw new Exception("Values not found!");
                }

                $contact = ["address"=>htmlspecialchars($_POST["address"]), "postcode"=>htmlspecialchars($_POST["postcode"]),
                    "city"=>htmlspecialchars($_POST["city"]), "state_code"=>htmlspecialchars($_POST["state"]),
                    "phone"=>htmlspecialchars($_POST["phone"])];
                $userID = $_SESSION["user_data"]["user_id"];

                if (updateContact($userID, $contact)){
                    makeToast('success', "Contact info is successfully updated!", "Success");
                }
                else{
                    throw new Exception("Contact info wasn't able to be updated!");
                }
            }
            else{
                makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
            }
        }
        else {
            throw new exception("Token not found");
        }
    }
    catch (exception $e){
        makeToast("error", $e->getMessage(), "Error");
    }

    header("Location: /account/profile.php");
    die();
}

displayToast();
$user = retrieveCustomer($_SESSION["user_data"]["user_id"]);

$date = $user["customer_dob"] ?? null;
if ($date) {
    $dob = date_create($date);
    $dob = date_format($dob, "d M Y");
}


$token = getToken();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title>Kerepek Funz | Profile</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Profile") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="col-5">
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <span class="fs-2">Details Update</span>
                            <div class="mt-2">
                                <form method="post" action="<?php current_page(); ?>">
                                    <div class="container">

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="shadow p-3 bg-body rounded row gx-3">
                            <span class="fs-2">Account Details</span>
                            <div class="col mt-2">
                                <div class="row">
                                    <span class="fw-bold">Username</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold">First Name</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold">Last Name</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold">Email</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold">Registration</span>
                                </div>
                            </div>
                            <div class="col mt-2">
                                <div class="row">
                                    <span class="fw-semibold"><?= $user["username"]  ?? "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= $user["user_fname"]  ?? "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= $user["user_lname"]  ?? "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= $user["email"]  ?? "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?php if (!empty($user["registration_date"])) {
                                            $d = date_create($user["registration_date"]);
                                            echo date_format($d, "d M Y");
                                        }?></span>
                                </div>
                            </div>
                        </div>
                        <div class="shadow p-3 mt-4 mb-4 bg-body rounded row gx-3">
                            <span class="fs-2">Customer Details</span>
                            <div class="col mt-2">
                                <div class="row">
                                    <span class="fw-bold">Phone</span>
                                </div>
                                <div class="row">
                                    <span class="fw-bold">Date of Birth</span>
                                </div>
                            </div>
                            <div class="col mt-2">
                                <div class="row">
                                    <span class="fw-semibold"><?= $user["customer_phone"] ?? "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= $dob ?? "-"  ?></span>
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
</body>

</html>