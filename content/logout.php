<?php
session_start();
require("../includes/functions.inc.php");

session_destroy();
header("Location: /index.php");

die();