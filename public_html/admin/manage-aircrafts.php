<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                //todo delete
                if (isset($_POST["delete"])) {
                    $aircraftID = htmlspecialchars($_POST["aircraft_id"]);

                    deleteAircraft($aircraftID) or throw new Exception("Couldn't delete aircraft");
                    makeToast("success", "Aircraft successfully deleted!", "Success");
                }
                // todo register
                else if (isset($_POST["aircraft"])) {
                    $name = htmlspecialchars($_POST["name"]);
                    $economy = htmlspecialchars($_POST["economy"]);
                    $premiumEconomy = htmlspecialchars($_POST["premium-economy"]);
                    $business = htmlspecialchars($_POST["business"]);
                    $firstClass = htmlspecialchars($_POST["first-class"]);

                    createAircraft($name, $economy, $premiumEconomy, $business, $firstClass) or throw new Exception("Couldn't create aircraft");

                    makeToast("success", "Aircraft successfully created!", "Success");
                }
                else if (isset($_POST["update"])){
                    $name = htmlspecialchars($_POST["name"]);
                    $economy = htmlspecialchars($_POST["economy"]);
                    $premiumEconomy = htmlspecialchars($_POST["premium-economy"]);
                    $business = htmlspecialchars($_POST["business"]);
                    $firstClass = htmlspecialchars($_POST["first-class"]);
                    $aircraftID = htmlspecialchars($_POST["aircraft_id"]);

                    if ((!is_numeric($economy)) || (!is_numeric($premiumEconomy)) || (!is_numeric($premiumEconomy))
                    || (!is_numeric($business)) || (!is_numeric($firstClass))) {
                        throw new Exception("Must be an integer!");
                    }

                    updateAircraft($aircraftID, $name, $economy, $premiumEconomy, $business, $firstClass) or throw new Exception("Couldn't update aircraft");

                    makeToast("success", "Aircraft successfully updated!", "Success");
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

    header("Location: /admin/manage-aircrafts.php");
    die();
}
displayToast();

$aircraftCount = retrieveCountAircrafts()["count"] ?? 0;
$aircrafts = retrieveAircrafts();

$token = getToken();
?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Manage Aircraft</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Manage Aircrafts") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div class="row mt-4 ms-3">
                    <div class="shadow-sm p-3 px-4 mb-5 bg-body rounded row gx-3">
                        <div class="row mb-4">
                            <span class="h2"><?= $aircraftCount ?> aircrafts found</span>
                        </div>
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                            <div class="col">
                                <span class="fs-1 mb-3">Aircrafts</span>
                            </div>
                            <div class="col text-end">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#aircraftStatic">
                                    <span class="h5"><i class="bi bi-plus-circle"> </i>Add</span>
                                </button>
                            </div>

                            <div class="row mt-3">
                                <?php admin_displayAircraft($aircrafts); ?>
                            </div>
                        </div>
                    </div>

                    <!-- modal create aircraft -->
                    <div class='modal fade' id='aircraftStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Register aircraft</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form id="aircraft" action="/admin/manage-aircrafts.php" method="post">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="economy" class="form-label">Economy</label>
                                            <input type="number" class="form-control" id="economy" name="economy" min="0" placeholder="Enter quantity for Economy class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="premium-economy" class="form-label">Premium Economy</label>
                                            <input type="number" class="form-control" id="premium-economy" name="premium-economy" min="0" placeholder="Enter quantity for Premium Economy class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="business" class="form-label">Business</label>
                                            <input type="number" class="form-control" id="business" name="business" min="0" placeholder="Enter quantity for Business class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="first-class" class="form-label">First Class</label>
                                            <input type="number" class="form-control" id="first-class" name="first-class" min="0"  placeholder="Enter quantity for First Class">
                                        </div>
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                    </form>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-admin" form="aircraft" name="aircraft" value="1" class='btn btn-danger'>Register Aircraft</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- modal update aircraft -->
                    <div class='modal fade' id='updateAircraftStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Update aircraft?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form id="update" action="/admin/manage-aircrafts.php" method="post">
                                        <div class="mb-3">
                                            <label for="name-update" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name-update" name="name" placeholder="Enter your name">
                                        </div>
                                        <div class="mb-3">
                                            <label for="economy-update" class="form-label">Economy</label>
                                            <input type="number" class="form-control" id="economy-update" name="economy" min="0" placeholder="Enter quantity for Economy class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="premium-economy-update" class="form-label">Premium Economy</label>
                                            <input type="number" class="form-control" id="premium-economy-update" name="premium-economy" min="0" placeholder="Enter quantity for Premium Economy class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="business-update" class="form-label">Business</label>
                                            <input type="number" class="form-control" id="business-update" name="business" min="0" placeholder="Enter quantity for Business class">
                                        </div>
                                        <div class="mb-3">
                                            <label for="first-class-update" class="form-label">First Class</label>
                                            <input type="number" class="form-control" id="first-class-update" name="first-class" min="0" placeholder="Enter quantity for First Class">
                                        </div>
                                        <input type="hidden" name="aircraft_id">
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                    </form>
                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>Warning:</strong> Updating aircraft details can have significant consequences.
                                        It is highly recommended to register an aircraft in the system, rather than editing an existing one!
                                        <br>Please proceed with caution.
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' form="update" name="update" value="1" class='btn btn-danger'>Update Aircraft</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- modal delete -->
                    <div class='modal fade' id='deleteStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Deregister aircraft?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                        <form id="delete" action="/admin/manage-aircrafts.php" method="post">
                                            <input type="hidden" name="aircraft_id">
                                            <input type="hidden" name="token" value="<?= $token ?>">
                                        </form>
                                    </div>
                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' form="delete" name="delete" value="1" class='btn btn-danger'>I understand</button>
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