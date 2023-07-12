<?php

require("../../includes/functions.inc.php");

session_start();

customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                if (!array_keys_isset(["first_name", "last_name", "phone", "dob"], $_POST)){
                    throw new Exception("Values not found!");
                }

                $contact = ["phone"=>htmlspecialchars($_POST["phone"]), "first_name"=>htmlspecialchars($_POST["first_name"]),
                    "last_name"=>htmlspecialchars($_POST["last_name"]), "dob"=>htmlspecialchars($_POST["dob"])];
                $userID = $_SESSION["user_data"]["user_id"];

                //dob
                $dateDob = $contact["dob"];
                $currentDate = date_create("now");
                $minAge = date_modify(clone $currentDate, "-100 years");
                $maxAge = date_modify(clone $currentDate, "-18 years");

                $isValidAge = false;

                if ($dateDob >= $minAge && $dateDob <= $maxAge) {
                    $isValidAge = true;
                }

                if (!$isValidAge) {
                    throw new Exception("Date of birth can only be between 18 and 100!");
                }


                if (updateAccount($userID, $contact)){
                    makeToast('success', "Account info is successfully updated!", "Success");
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
    <title><?= config("name") ?> | Profile</title>
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
                    <div class="col-lg-5 col-md-auto">
                        <div class="shadow p-2 mb-5 bg-body rounded-3 row gx-3">
                            <span class="fs-2">Account Details</span>
                            <div class="mt-2">
                                <form method="post" action="<?php current_page(); ?>" class="needs-validation" novalidate>
                                    <div class="container">

                                        <div class="row gx-1 mb-2 align-items-center">
                                            <div class="col-auto me-1">
                                                <i class="bi bi-person icon-red h5 fw-bold me-3"></i>
                                            </div>
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter your first name" value="<?= $user["user_fname"] ?? "" ?>" required>
                                                    <label for="first_name">First Name</label>
                                                    <div class="invalid-feedback" id="first_name_error">Please enter your first name.</div>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter your last name" value="<?= $user["user_lname"] ?? "" ?>" required>
                                                    <label for="last_name">Last Name</label>
                                                    <div class="invalid-feedback" id="last_name_error">Please enter your last name.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-2 align-items-center">
                                            <div class="col-auto">
                                                <i class="bi bi-telephone icon-red h5 fw-bold"></i>
                                            </div>
                                            <div class="col">
                                                <div class="form-floating mb-2">
                                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" value="<?= $user["customer_phone"] ?? "" ?>" required>
                                                    <label for="phone">Phone</label>
                                                    <div class="invalid-feedback" id="phone_error">Please enter a valid phone number.</div>
                                                </div>
                                            </div>
                                        </div>

                                       <div class="row align-items-center">
                                           <div class="col-auto">
                                               <i class="bi bi-calendar icon-red h5 fw-bold"></i>
                                           </div>
                                           <div class="col">
                                               <div class="form-floating">
                                                   <input type="date" class="form-control" id="date_of_birth" name="dob" value="<?= $user["customer_dob"] ?? "" ?>" required>
                                                   <label for="date_of_birth">Date of Birth</label>
                                                   <div class="invalid-feedback" id="dob_error">Please enter a valid date of birth.</div>
                                               </div>
                                           </div>
                                       </div>

                                        <div class="row mt-4 text-end">
                                            <div class="col">
                                                <button type="submit" class="btn btn-danger">Update</button>
                                                <input type="hidden" name="token" value="<?= $token ?>">
                                            </div>
                                        </div>
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
                                    <span class="fw-semibold"><?= !empty($user["username"]) ? $user["username"]  : "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= !empty($user["user_fname"]) ? $user["user_fname"]  : "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= !empty($user["user_lname"]) ? $user["user_lname"] : "-" ?></span>
                                </div>
                                <div class="row">
                                    <span class="fw-semibold"><?= !empty($user["email"]) ? $user["email"] : "-"  ?></span>
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
                                    <span class="fw-semibold"><?= !empty($user["customer_phone"]) ? $user["customer_phone"] : "-" ?></span>
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
<script>
    $(document).ready(function() {

        document.querySelector('form.needs-validation').addEventListener('submit', function(event) {
            if (!validateForm()) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });

        function validateForm() {
            // Get the form inputs
            var firstNameInput = document.getElementById('first_name');
            var lastNameInput = document.getElementById('last_name');
            var phoneInput = document.getElementById('phone');
            var dobInput = document.getElementById('date_of_birth');

            // Validate first name
            if (firstNameInput.value.trim() === '') {
                displayErrorMessage(firstNameInput, 'first_name_error', 'Please enter your first name.');
                return false;
            }

            // Validate last name
            if (lastNameInput.value.trim() === '') {
                displayErrorMessage(lastNameInput, 'last_name_error', 'Please enter your last name.');
                return false;
            }

            // Validate phone number
            if (!/^(\+\d{1,3})?\d{10}$/.test(phoneInput.value.trim())) {
                displayErrorMessage(phoneInput, 'phone_error', 'Please enter a valid phone number.');
                return false;
            }

            // Validate date of birth
            var dobDate = new Date(dobInput.value);
            var currentDate = new Date();
            var minAgeDate = new Date(currentDate.getFullYear() - 18, currentDate.getMonth(), currentDate.getDate());
            var maxAgeDate = new Date(currentDate.getFullYear() - 100, currentDate.getMonth(), currentDate.getDate());
            if (dobDate > minAgeDate) {
                displayErrorMessage(dobInput, 'dob_error', 'You must be at least 18 years old.');
                return false;
            }
            if (dobDate < maxAgeDate) {
                displayErrorMessage(dobInput, 'dob_error', 'You must be at most 100 years old.');
                return false;
            }

            // Clear any error messages if validation passes
            clearErrorMessage(firstNameInput, 'first_name_error');
            clearErrorMessage(lastNameInput, 'last_name_error');
            clearErrorMessage(phoneInput, 'phone_error');
            clearErrorMessage(dobInput, 'dob_error');

            return true; // Form is valid
        }

        function displayErrorMessage(input, errorId, message) {
            input.classList.add('is-invalid');
            var errorElement = document.getElementById(errorId);
            errorElement.textContent = message;
        }

        function clearErrorMessage(input, errorId) {
            input.classList.remove('is-invalid');
            var errorElement = document.getElementById(errorId);
            errorElement.textContent = '';
        }


        <?php if (empty($user["customer_dob"])) { ?>
        // Set default value for Date of Birth 18 years prior to the current date
        var dateOfBirthInput = document.getElementById('date_of_birth');
        var currentDate = new Date();
        var eighteenYearsAgo = new Date(currentDate.getFullYear() - 18, currentDate.getMonth(), currentDate.getDate());
        dateOfBirthInput.valueAsDate = eighteenYearsAgo;
        <?php } ?>
    });
</script>

</body>

</html>