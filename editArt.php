<?php
session_start();
include('include/functions.php');


if(checkSession()){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');


	$sql="update Artikel set
			Titel='".$_POST['ArtTitle']."',
			kat_idfk='".$_POST['KatID']."',
			content='".$_POST['ArtText']."',
			autor_idfk=".$_SESSION['userID']."
		where
			id=".$_POST['id'];



	$file = 'debug.txt';
	$debug = 1;
	if ($debug == 1){
		file_put_contents($file, $sql);
	}
	$db->query($sql);
}
?>