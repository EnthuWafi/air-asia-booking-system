<?php
session_start();
include("../includes/functions.inc.php");
// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $conn = OpenConn();

  $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
  $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

  //check if user exists
  $user = verifyUser($username, $password);

  if (isset($user)) {   
    $_SESSION["user_id"] = $user;
    header("Location: ../index");
  }
  else {
    die("User doesn't exist");
  }
  CloseConn($conn);
}


?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=5,minimum-scale=1, viewport-fit=cover" name="viewport">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <title>airasia | Login</title>
</head>

<body>

  <!-- Content -->
  <h1>Login</h1>  
  <form action="<?php current_page() ?>" method="POST">
    <label>Username:</label>
    <input type="text" name="username"><br>
    <label>Password:</label>
    <input type="password" name="password"><br>
    <input type="submit" value="Login">
  </form>
  <a href="register">Create a new account?</a>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>