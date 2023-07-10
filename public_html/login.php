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
    <link rel="stylesheet" href="/assets/css/login.css">
    <title><?= config("name") ?> | Login</title>
</head>
<body>
<div style="background: #ea0e0e">
    <?php nav_bar(); ?>
    <div class="container-fluid">
        <div class="container my-5 pb-5" style="height: 600px" >
            <div class="center" >
                <h1>Login</h1>
                <form action="<?php current_page(); ?>"  method="post">
                    <div class="txt_field">
                        <input type="text" name="username"  required />
                        <span></span>
                        <label>Username</label>
                    </div>
                    <div class="txt_field">
                        <input type="password" name="password"  required />
                        <span></span>
                        <label>Password</label>
                    </div>
                    <div class="pass">Forgot Password?</div>
                    <input type="submit" value="Login" />
                    <div class="signup_link">Not a member? <a href="/register.php">Signup</a></div>
                </form>
            </div>
        </div>

    </div>
</div>
<?php footer(); ?>

<?php body_script_tag_content(); ?>
</body>
</html>

