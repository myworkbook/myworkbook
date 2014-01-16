<?php
session_start();
include('include/functions.php');
if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	if($_POST['Passwort']!=""){
		// Passwort wurde nicht veändert
		$sql="update Benutzer set
				username='".$_POST['Username']."',
				vorname='".$_POST['Vorname']."',
				nachname='".$_POST['Nachname']."',
				email='".$_POST['Email']."',
				creator_idfk='".$_SESSION['userID']."'
			where id='".$_POST['id']."'
				";
	}
	else{
		$sql="update Benutzer set
				username='".$_POST['Username']."',
				vorname='".$_POST['Vorname']."',
				nachname='".$_POST['Nachname']."',
				email='".$_POST['Email']."',
				passwort='".md5($_POST['Passwort'])."',
				creator_idfk='".$_SESSION['userID']."'
			where id='".$_POST['id']."'
				";
	}
	$file = 'debug.txt';
	$debug = 0;
	if ($debug == 1){
		file_put_contents($file, $sql);
	}
	$db->query($sql);

}
?>