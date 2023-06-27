<?php
session_start();
require("../includes/functions.inc.php");

admin_forbidden();
customer_forbidden();

// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

    $userData = verifyUser($username, $password);

    if (isset($userData)) {
        $_SESSION["user_data"] = $userData;
        makeToast("success", "You are now logged in!", "Success");
        header("Location: /index.php");
    }
    else {
        makeToast("error", "Either username or password is incorrect.", "Error");
    }
}
displayToast();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php head_tag_content(); ?>
    <title>Login</title>
</head>
<body>
<?php nav_bar(); ?>
<div class="container-fluid">
    <div class="container my-5 pb-5">
        <form action="<?php current_page(); ?>" method="post">
            <div class="row pt-5">
                <div class="col-md-6 offset-md-3">
                    <h2 class="text-center mb-4">Login</h2>
                    <form>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password">
                        </div>
                        <div class="text-center my-4">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <div class="text-center mt-2">
                            <span>Don't have an account? <a class="text-decoration-none" href="/register.php">Register now!</a></span>
                        </div>
                    </form>
                </div>
            </div>
        </form>
    </div>
    <?php footer(); ?>
</div>


<?php body_script_tag_content(); ?>
</body>
</html>

