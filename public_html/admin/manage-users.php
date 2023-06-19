<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //delete user todo
    if (isset($_POST["delete"])) {

    }
    //create admin todo
    if (isset($_POST["admin"])) {

    }
}


$usersCount = retrieveCountUsers()["count"] ?? 0;
$adminsCount = retrieveCountAdminUsers()["count"] ?? 0;
$customersCount = retrieveCountCustomerUsers()["count"] ?? 0;


$adminUsers = retrieveAllAdminUsers();
$customerUsers = retrieveAllCustomerUsers();


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
                                    <form id="admin" action="/admin/manage-users.php">
                                        <div class="row">
                                            <div class="col" id="name">
                                                <label for="first-name" class="form-label">First Name</label>
                                                <input type="text" class="form-control" id="first-name" placeholder="John">
                                            </div>
                                            <div class="col">
                                                <label for="last-name" class="form-label">Last Name</label>
                                                <input type="text" class="form-control" id="last-name" placeholder="Johnny">
                                            </div>
                                        </div>
                                        <div class="row px-2">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" placeholder="john123" required>
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email" placeholder="john@gmail.com" required>
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" placeholder="password" required>
                                        </div>

                                    </form>

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn-admin" form="admin" name="admin" value="1" class='btn btn-danger'>Create Account</button>
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

                                </div>
                                <div class='modal-footer bg-light-subtle'>
                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                    <button type='submit' id="modal-btn" form="" name="delete" value="1" class='btn btn-danger'>I understand</button>
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