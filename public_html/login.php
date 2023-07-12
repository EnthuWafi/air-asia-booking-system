<?php
session_start();
require("../includes/functions.inc.php");

admin_forbidden();
customer_forbidden();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                require("../mail.inc.php");
                if (isset($_POST["login"])) {

                    $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
                    $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

                    $userData = verifyUser($username, $password);

                    if (isset($userData)) {
                        $_SESSION["user_data"] = $userData;
                        makeToast("success", "You are now logged in!", "Success");
                        header("Location: /index.php");
                        die();
                    }
                    else {
                        throw new Exception("Either username or password is incorrect.");
                    }
                }
                if (isset($_POST["pwdreset"])){
                    $email = htmlspecialchars($_POST["email"]);

                    $user = retrieveUserByEmail($email) or throw new Exception("We apologize, but user with that email does not exist!");

                    $urlToken = bin2hex(random_bytes(32));
                    $protocol = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on" ? "https://" : "http://";
                    $domain = $_SERVER["HTTP_HOST"];
                    $url = $protocol . $domain . "/create-new-password.php?token=$urlToken";

                    //expires date
                    $dateVal = date_create("now");
                    date_add($dateVal, date_interval_create_from_date_string("3 days"));
                    $dateStr = $dateVal->format('Y-m-d');

                    $subject = "Airasia Password Reset";
                    $body = "
                    <html>
                    <body>
                      <h2>Reset Your Airasia Password</h2>
                      <p>Hello, {$user["user_fname"]}</p>
                      <p>We received a request to reset your Airasia account password. To proceed with the password reset, please click the link below:</p>
                      <a href='$url'>Reset Password</a>
                      <p>If you did not initiate this request, you can safely ignore this email.</p>
                      <p>Thank you,</p>
                      <p>AirAsia Team</p>
                    </body>
                    </html>
                    ";

                    sendMail($email, $subject, $body) or throw new Exception("Message wasn't sent!");

                    createPasswordReset($email, $urlToken, $dateStr) or throw new Exception("Password reset failed!");

                    makeToast("success", "Password reset URL sent to your mail!", "Success");
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

    header("Location: /login.php");
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
    <title><?= config("name") ?> | Login</title>
</head>
<body>
<div style="background: #ea0e0e">
    <?php nav_bar(); ?>
    <div class="container-fluid">
        <div class="container" >
            <div class="position-relative mt-3" id="login-form" style="height: 600px; scale: 0.95;" >
                <div class="center">
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
                        <div class="pass" data-bs-toggle="modal" data-bs-target="#pwdResetModal">Forgot Password?</div>
                        <input class="submit-red" type="submit" value="Login" name="login"/>
                        <input type="hidden" name="token" value="<?= $token ?>">
                        <div class="signup_link">Not a member? <a href="/register.php">Signup</a></div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</div>

<div class="modal fade" id="pwdResetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header gradient-primary p-4">
                <h5 class="modal-title font-alt text-white">Forgot your password?</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body border-0 p-4">
                <p>Please enter your email address below. We will send you instructions on how to reset your password.</p>

                <form method="post" action="<?php current_page(); ?>">
                    <!-- Email address input-->
                    <div class="form-floating mb-3">
                        <input class="form-control" id="email" type="email" placeholder="name@example.com" name="email" />
                        <label for="email">Email address</label>
                        <div class="invalid-feedback">An email is required.</div>
                    </div>
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <!-- Submit Button-->
                    <div class="d-grid">
                        <input class="submit-red"  type="submit" name="pwdreset" value="Reset">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php footer(); ?>

<?php body_script_tag_content(); ?>
<script>
    $(document).ready(function() {
        // Pop-out animation
        gsap.to("#login-form", {
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

