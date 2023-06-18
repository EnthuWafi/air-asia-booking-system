<?php

require("../../includes/functions.inc.php");

session_start();

admin_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo $_POST["user_id"] ?? "";
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
                                <span class="h3"><?= $adminsCount ?> admins found</span>
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
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($adminUsers != null) {
                                        $count = 1;
                                        // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
                                        foreach ($adminUsers as $user) {
                                            $fullName = $user["user_fname"] . " " . $user["user_lname"];
                                            $date = date_create($user["registration_date"]);
                                            $dateFormatted = date_format($date, "d M Y");
                                            echo
                                            "<tr>
                                            <th scope='row'>$count</th>
                                            <td>{$user["username"]}</td>
                                            <td>{$fullName}</td>
                                            <td>{$user["email"]}</td>
                                            <td class='text-center'>{$user["admin_code"]}</td>
                                            <td class='text-center'>{$dateFormatted}</td>
                                            <td class='text-center'>
                                                <form action='manage-users.php' id='{$user["user_id"]}' method='post'>
                                                    <input type='hidden' name='user_id' value='{$user["user_id"]}'>
                                                    <a type='button' data-bs-toggle='modal' data-bs-target='#static' onclick='updateModal({$user["user_id"]}, \"modal-btn\");' class='h4'>
                                                    <i class='bi bi-trash'></i></a>
                                                </form> 
                                            </td>
                                        </tr>";
                                            $count++;
                                        }
                                    }
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
                                        <th scope="col">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($customerUsers != null) {
                                        $count = 1;
                                        // OKAY, FOR DELETING, I need to use a modal so the user can be sure to remove it
                                        foreach ($customerUsers as $user) {
                                            $fullName = $user["user_fname"] . " " . $user["user_lname"];
                                            $date = date_create($user["registration_date"]);
                                            $dateFormatted = date_format($date, "d M Y");

                                            $dob = $user["customer_dob"];
                                            $dobFormatted = $dob ? date_format(date_create($dob), "d M Y") : "-";

                                            $phone = $user["customer_phone"] ?? "-";
                                            echo
                                            "<tr>
                                            <th scope='row'>$count</th>
                                            <td>{$user["username"]}</td>
                                            <td>{$fullName}</td>
                                            <td>{$user["email"]}</td>
                                            <td class='text-center'>{$dobFormatted}</td>
                                            <td class='text-center'>{$phone}</td>
                                            <td class='text-center'>{$dateFormatted}</td>
                                            <td class='text-center'>
                                                <form action='manage-users.php' id='{$user["user_id"]}' method='post'>
                                                    <input type='hidden' name='user_id' value='{$user["user_id"]}'>
                                                    <a type='button' data-bs-toggle='modal' data-bs-target='#static' onclick='updateModal({$user["user_id"]}, \"modal-btn\");' class='h4'>
                                                    <i class='bi bi-trash'></i></a>
                                                </form>    
                                            </td>
                                        </tr>";
                                            $count++;
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <!-- Modal -->
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
                                    <button type='submit' id="modal-btn" form="" class='btn btn-danger'>I understand</button>
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