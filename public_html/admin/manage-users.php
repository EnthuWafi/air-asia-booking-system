<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){

                require_once("../../mail.inc.php");
                //delete user todo
                if (isset($_POST["delete"])) {
                    $userID = htmlspecialchars($_POST["user_id"]);

                    if ($userID == $_SESSION["user_data"]["user_id"]){
                        throw new Exception("Cannot delete the admin user you are currently using!");
                    }

                    $user = retrieveUser($userID) or throw new Exception("User wasn't found!");

                    //main
                    if ($user["username"] === "EnthuWafi"){
                        throw new Exception("Cannot delete the MAIN ADMIN account!");
                    }

                    deleteUser($userID) or throw new Exception("Couldn't delete booking");

                    $fullName = "{$user["user_fname"]} {$user["user_lname"]}";

                    $subject = "Apology for Account Deletion";
                    $content = "<p>We would like to sincerely apologize for the recent removal of your account from our system. We understand that this may have caused inconvenience to you, and we deeply regret any inconvenience caused.</p>
            <p>If you believe this account deletion was made in error or if you have any concerns or questions, please feel free to contact our customer support team. We will be more than happy to assist you.</p>
            <p>Once again, we apologize for any inconvenience caused, and we appreciate your understanding in this matter.</p>";

                    $body = "<h1>Dear {$fullName},</h1>
                             {$content}
                             <p>Sincerely,</p>
                             <p>AirAsia Team</p>";

                    sendMail($user["email"], $subject, $body) or throw new Exception("Message wasn't sent!");

                    makeToast("success", "User successfully deleted!", "Success");
                }
                //create admin todo
                else if (isset($_POST["admin"])) {
                    $fname = htmlspecialchars($_POST["fname"]);
                    $lname = htmlspecialchars($_POST["lname"]);
                    $username = htmlspecialchars($_POST["username"]);
                    $email = htmlspecialchars($_POST["email"]);
                    $password = htmlspecialchars($_POST["password"]);

                    createUser($fname, $lname, $username, $password, $email, "admin") or throw new Exception("Admin user wasn't able to be created!");
                    makeToast("success", "Admin account successfully created!", "Success");

                }
                else if (isset($_POST["update"])) {
                    $userID = htmlspecialchars($_POST["user_id"]);
                    $fname = htmlspecialchars($_POST["fname"]);
                    $lname = htmlspecialchars($_POST["lname"]);
                    $username = htmlspecialchars($_POST["username"]);
                    $email = htmlspecialchars($_POST["email"]);

                    $user = retrieveUser($userID) or throw new Exception("User wasn't found!");

                    updateUser($userID, $fname, $lname, $username, $email) or throw new Exception("Wasn't able to update user!");

                    $fullName = "{$user["user_fname"]} {$user["user_lname"]}";

                    $subject = "Account Details Updated";
                    $content = "
            <p>We would like to inform you that your account details have been successfully updated. Here are your updated details:</p>
            <ul>
                <li>First Name: {$fname}</li>
                <li>Last Name: {$lname}</li>
                <li>Username: {$username}</li>
                <li>Email: {$email}</li>
            </ul>
            <p>If you did not request this update or if you have any concerns, please contact our customer support team immediately.</p>
            <p>Thank you for choosing AirAsia.</p>";

                    $body = "<h1>Dear {$fullName},</h1>
                             {$content}
                             <p>Sincerely,</p>
                             <p>AirAsia Team</p>";

                    sendMail($user["email"], $subject, $body) or throw new Exception("Message wasn't sent!");

                    makeToast("success", "User successfully updated!", "Success");
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

    header("Location: /admin/manage-users.php");
    die();
}

displayToast();


$usersCount = retrieveCountUsers()["count"] ?? 0;
$adminsCount = retrieveCountAdminUsers()["count"] ?? 0;
$customersCount = retrieveCountCustomerUsers()["count"] ?? 0;


$adminUsers = retrieveAllAdminUsers();
$customerUsers = retrieveAllCustomerUsers();

$token = getToken();

?>
<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Manage Users</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php admin_side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php admin_header_bar("Manage User") ?>

            <!-- todo users here  -->
            <div class="container">
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-5 bg-body rounded row gx-3">
                        <div class="row">
                            <span class="h3"><?= $usersCount ?> users found</span>
                        </div>
                        <div class="shadow p-3 mb-5 mt-3 bg-body rounded row gx-3 mx-1">
                            <!-- ADMIN-->
                            <div class="row">
                                <div class="col">
                                    <span class="h3"><?= $adminsCount ?> admins found</span>
                                </div>
                                <div class="col text-end ">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adminStatic">
                                         <span class="h5"><i class="bi bi-plus-circle"> </i>Add</span>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Code</th>
                                        <th scope="col">Registration</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    admin_displayAdminUsers($adminUsers);
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="shadow p-3 mb-5 bg-body rounded row gx-3 mx-1">
                            <!-- CUSTOMER -->
                            <div class="row">
                                <span class="h3"><?= $customersCount ?> customers found</span>
                            </div>
                            <div class="row mt-3">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Date of Birth</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Registration</th>
                                        <th scope="col" class="text-center">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    admin_displayCustomerUsers($customerUsers);
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- modal create admin -->
                    <div class='modal fade' id='adminStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Create Admin Account</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form id="admin" action="/admin/manage-users.php" method="post">
                                        <div class="row mb-1">
                                            <div class="col" id="name">
                                                <label for="first-name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first-name" name="fname" placeholder="First name">
                                            </div>
                                            <div class="col">
                                                <label for="last-name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last-name" name="lname" placeholder="Last name">
                                            </div>
                                        </div>
                                        <div class="row px-2 mb-1">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username here" required>
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email here" required>
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password here" required>
                                        </div>
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                    </form>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>

                                    <button type='submit' id="modal-btn-admin" form="admin" name="admin" value="1" class='btn btn-danger'>Create Account</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- modal update admin -->
                    <div class='modal fade' id='updateAdminStatic' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Update Admin Account</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                    <form id="update" action="/admin/manage-users.php" method="post">
                                        <div class="row mb-1">
                                            <div class="col" id="name">
                                                <label for="first-name-update" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first-name-update" name="fname" placeholder="First name">
                                            </div>
                                            <div class="col">
                                                <label for="last-name-update" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last-name-update" name="lname" placeholder="Last name">
                                            </div>
                                        </div>
                                        <div class="row px-2 mb-1">
                                            <label for="username-update" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username-update" name="username" placeholder="Enter username here" required>
                                            <label for="email-update" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email-update" name="email" placeholder="Enter email here" required>
                                        </div>
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                        <input type="hidden" name="user_id" value="">
                                    </form>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-update" form="update" name="update" value="1" class='btn btn-danger'>Update Account</button>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- modal delete -->
                    <div class='modal fade' id='static' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header bg-light-subtle'>
                                    <h5 class='modal-title' id='staticBackdropLabel'>Delete user?</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body bg-danger-subtle'>
                                    <div class="px-3">
                                        <div class="mb-1">
                                            <span class="fw-bolder">Warning</span>
                                        </div>
                                        <span class="text-black mt-3">This action cannot be reversed!<br>Proceed with caution.</span>
                                    </div>
                                    <form id="delete" action="/admin/manage-users.php" method="post">
                                        <input type="hidden" name="token" value="<?= $token ?>">
                                        <input type="hidden" name="user_id" value="">
                                    </form>
                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-delete" form="delete" name="delete" value="1" class='btn btn-danger'>I understand</button>
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