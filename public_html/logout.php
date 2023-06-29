<?php
session_start();
require("../includes/functions.inc.php");

session_destroy();

session_start();
makeToast("success", "User successfully logged out!", "Success");
$_SESSION["traffic"] = 1;//keep session for traffic

header("Location: /index.php");

die();