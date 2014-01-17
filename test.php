<?php

session_start();

include ('include/functions.php');

print_r($_POST);
echo"<p>";
print_r($_SESSION);
echo "<p>";

//require ("include/db.class.php");


?>