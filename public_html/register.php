<?php

include("../includes/functions.inc.php");

session_start();
// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  
  $conn = OpenConn();

  $email = filter_var($_POST["email"], FILTER_SANITIZE_SPECIAL_CHARS);
  $username = filter_var($_POST["username"], FILTER_SANITIZE_SPECIAL_CHARS);
  $password = filter_var($_POST["password"], FILTER_SANITIZE_SPECIAL_CHARS);

  //check if exists
  $user = checkUser($username);

  //create account
  if (empty($user)) {
    createUser($username, $password, $email);
    $message = "Successfully created!";
  }
  else {
    $message = "Account exists!";
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
  <title>airasia | Flights, Hotels, Activities, and More</title>
</head>

<body>

  <!-- Content -->
  <h1>Register</h1>  
  <form action="<?php current_page(); ?>" method="POST">
    <label>Email:</label>
    <input type="text" name="email"><br>
    <label>Username:</label>
    <input type="text" name="username"><br>
    <label>Password:</label>
    <input type="password" name="password"><br>
    <input type="submit" value="Login">
  </form>
  <a href="/login.php">Already have an account?</a>
  <? echo $message ?>
  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>