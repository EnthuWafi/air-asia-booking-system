<?php
session_start();
require("../includes/functions.inc.php");


try{
    //gets token
    $urlToken = $_GET["token"] or throw new Exception("Token not found!");

    $passwordReset = retrievePasswordReset($urlToken) or throw new Exception("Token doesn't exist or is expired!");
    if ($passwordReset["check_used"] == 1) {
        throw new Exception("This password reset token is already used! Request a new one.");
    }
}
catch (exception $e){
    makeToast("error", $e->getMessage(), "Error");
    header("Location: /index.php");
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                if (isset($_POST["reset"])) {
                    $password = htmlspecialchars($_POST["password"]);

                    if ($password == "") {
                        throw new Exception("Password cannot be empty!");
                    }

                    updatePassword($passwordReset["password_reset_email"], $password) or throw new Exception("Password was not updated!");
                    updateCheckPasswordReset($urlToken) or throw new Exception("Token already used");

                    makeToast("success", "User password updated!", "Success");
                    header("Location: /index.php");
                    die();
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

    header("Location: /create-new-password.php");
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
    <title><?= config("name") ?> | Password Reset</title>
</head>
<body>
<div style="background: #ea0e0e">
    <?php nav_bar(); ?>
    <div class="container-fluid">
        <div class="container" >
            <div class="position-relative mt-3" id="password-reset-form" style="height: 600px; scale: 0.95;" >
                <div class="center">
                    <h1>Password Reset</h1>
                    <form action="/create-new-password.php/?token=<?= $urlToken ?>"  method="post">
                        <div class="txt_field">
                            <input type="password" name="password" required />
                            <span></span>
                            <label>Password</label>
                        </div>
                        <div class="txt_field">
                            <input type="password" required />
                            <span></span>
                            <label>Confirm Password</label>
                        </div>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <input class="submit-red" type="submit" value="Reset" name="reset"/>
                        <div class="signup_link">&nbsp</div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>


<?php footer(); ?>

<?php body_script_tag_content(); ?>
<script>
    $(document).ready(function() {
        // Pop-out animation
        gsap.to("#password-reset-form", {
            scale: 1,
            opacity: 1,
            duration: 0.3,
            ease: "power2.out",
            yoyo: true
        });
    });
</script>
</body>
</html>

