<?php

require("../includes/functions.inc.php");

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    try{
        if(!empty($postedToken)){
            if(isTokenValid($postedToken)){
                $type = htmlspecialchars($_POST["type"]);
                $email = htmlspecialchars($_POST["email"]);
                $message = htmlspecialchars($_POST["message"]);

                createMessage($type, $email, $message) or throw new Exception("Feedback was not created!");
                makeToast("success", "Message successfully sent! <br>Thank you for your feedback :)", "Success");
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

    header("Location: /contact-us.php");
    die();
}

displayToast();
$token = getToken();

?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content() ?>
    <link rel="stylesheet">

    <title><?= config("name") ?> | Contact Us</title>
</head>

<body>
<!-- Navigation -->
<?php nav_bar() ;?>

<div class="container py-5">
    <form action="<?php current_page(); ?>" method="post">
        <div class="row p-5 shadow rounded-3">
            <h1>Contact Us</h1>
            <div class="row">

                <div class="mb-3">
                    <label for="type">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="problem">Problem/Issues</option>
                        <option value="feedback">Feedback/Suggestion</option>
                        <option value="others">Other</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email">
                </div>
                <div class="form-group mb-3">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" rows="5" name="message" placeholder="Enter your message"></textarea>
                </div>
            </div>
            <div class="row mt-5 justify-content-end">
                <div class="col-auto">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <button type="submit" class="btn btn-danger rounded-pill btn-red" style="width: 150px">Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php footer() ?>
<?php body_script_tag_content(); ?>
</body>

</html>