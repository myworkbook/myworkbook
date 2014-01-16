<?php
session_start();
include('include/functions.php');
if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

	$sql="insert into Benutzer
	(username,
	TIMESTAMP,
	vorname,
	nachname,
	passwort,
	email,
	creator_idfk)

	values(
	'".$_POST['Username']."',
	CURRENT_TIMESTAMP(),
	'".$_POST['Vorname']."',
	'".$_POST['Nachname']."',
	'".md5($_POST['Passwort'])."',
	'".$_POST['Email']."',
	'".$_SESSION['userID']."'
	)";


	$db->query($sql);
}
?>