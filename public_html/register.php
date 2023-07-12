<?php
session_start();
require("../includes/functions.inc.php");

admin_forbidden();
customer_forbidden();

// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                $fname = htmlspecialchars($_POST["fname"]);
                $lname = htmlspecialchars($_POST["lname"]);
                $email = filter_var($_POST["email"], FILTER_SANITIZE_SPECIAL_CHARS);
                $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
                $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

                //check if exists
                $user = checkUser($username, $email);
                //create account
                if (!$user) {
                    if (createUser($fname, $lname, $username, $password, $email, "customer")){
                        makeToast("info", "You can now log in using your account in Login Page!", "Account successfully created!");
                    }
                    header("Location: /login.php");
                    die();
                }
                else {
                    throw new exception("Another account with the same username or email exists!");
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

    header("Location: /register.php");
    die();
}

displayToast();

$token = getToken();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/login.css">
    <title><?= config("name") ?> | Register</title>
</head>
<body>
<div style="background: #ea0e0e">
    <?php nav_bar(); ?>
    <div class="container-fluid">
        <div class="row overflow-x-auto">
            <div class="container my-4">
                <div class="row my-5">
                    <div class="col-md-6 offset-md-3 bg-body p-5 rounded-3">
                        <h2 class="text-center mb-3">Registration</h2>
                        <hr>
                        <form action="<?php current_page(); ?>" method="post">
                            <div class="row mb-3 mt-4">
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="fname" name="fname" placeholder="First Name" required>
                                        <label for="fname" class="form-label">First Name</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" required>
                                        <label for="lname" class="form-label">Last name</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                                    <label for="username" class="form-label">Username</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                    <label for="email" class="form-label">Email</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                    <label for="password" class="form-label">Password</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="confirm-password" placeholder="Confirm your password" required>
                                    <label for="confirm-password" class="form-label">Confirm Password</label>
                                </div>
                            </div>
                            <div class="text-center">
                                <input type="submit" value="Sign-Up" />
                            </div>
                            <div class="signup_link">Already a member? <a href="/login.php">Login</a></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php footer(); ?>
<?php body_script_tag_content(); ?>
<script type="text/javascript" src="/assets/js/register.js"></script>
</body>
</html>

