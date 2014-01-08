<?php


require ("include/db.class.php");


if(userproof($_POST['user'],$_POST['pwd'])){

	$html="<!DOCTYPE html>
	<html lang='de'>
	<head>
	<meta charset='UTF-8' /><title>Assessment Exercise</title>
	<link rel='stylesheet' type='text/css' href='css/all.css' />
	<!--<link rel='stylesheet' type='text/css' media='all and (max-device-width: 480px)' href='css/handy.css' />-->
	<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'>
	</script>
	</head>
	<body>

		Die Admin Seite<p>

		Artikel schreiben<br>
		Artikel auflisten<br>
		Artikel bearbeiten<br>
		Artikel löschen<p>

		Kategorieverwaltung<br>
			Kat 1	<a href ='editKat.php'>bearbeiten</a> <a href='deleteKat.php'>löschen</a><br>


		Kategorien anlegen<br>


		Benutzerverwaltung<br>
		Benutzer anlegen<br>
		Benutzer ändern<br>
		Benutzer löschen<p>

	</body>
	</html>";
}
echo $html;

function userproof($user, $pwd){

	$db = new DB();
	$sql="select * from User where username='".$user."'and passwort='".md5($pwd)."'";
	//echo $sql;
	$result=$db->query($sql);
	foreach ($result as $key => $row){
		$id=$row['id'];
	}
	//echo 'User ID ist: '.$id;
	return 1;
}