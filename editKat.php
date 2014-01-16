<?php
session_start();
include('include/functions.php');


if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

	$sql="update Kategorie set Kategorie='".$_POST['newKatName']."', user_idfk=".$_SESSION['userID']." where id=".$_POST['id'];
	var_dump($SQL);
	$db->query($sql);
}
?>