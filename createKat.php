<?php
session_start();
include('include/functions.php');
if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

	$sql="insert into Kategorie(Kategorie,TIMESTAMP, user_idfk)values('".$_POST['NewKatName']."',CURRENT_TIMESTAMP(),'".$_SESSION['userID']."')";

	//echo $sql;
	$db->query($sql);
}
?>

