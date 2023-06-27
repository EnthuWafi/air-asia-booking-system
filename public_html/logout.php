<?php
session_start();
require("../includes/functions.inc.php");

session_destroy();
//keep session for traffic
$_SESSION["traffic"] = 1;
makeToast("success", "User successfully logged out!", "Success");
header("Location: /index.php");

die();