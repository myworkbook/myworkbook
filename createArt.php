<?php
session_start();
include('include/functions.php');
if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

	$sql="insert into Artikel
			(titel,
			TIMESTAMP,
			CREATEDATE,
			kat_idfk,
			content,
			autor_idfk)
		values(
			'".$_POST['newTopic']."',
			CURRENT_TIMESTAMP(),
			CURRENT_TIMESTAMP(),
			'".$_POST['KatID']."',
			'".$_POST['ArtText']."',
			'".$_SESSION['userID']."')";

	$file = 'debug.txt';
	$debug = 1;
	if ($debug == 1){
		file_put_contents($file, $sql);
	}
	$db->query($sql);
}
?>