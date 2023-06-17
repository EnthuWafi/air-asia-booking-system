<?php
session_start();
include("../../includes/functions.inc.php");

login_forbidden();
// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

    $userData = verifyUser($username, $password, "admins");

    if ($userData) {
        $_SESSION["userData"] = $userData;
        header("Location: /admin/dashboard.php");
    }
    else {
        die("Error: User (admin) doesn't exist");
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <?php head_tag_content(); ?>
    <title><?= config("name") ?> | Admin Login</title>
</head>

<body>

<!-- Content -->
<h1>Admin Login</h1>
<form action="<?php current_page(); ?>" method="POST">
    <label>Username:</label>
    <input type="text" name="username"><br>
    <label>Password:</label>
    <input type="password" name="password"><br>
    <input type="submit" value="Login">
</form>

<!-- JS -->
<?php body_script_tag_content(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>