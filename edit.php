<?php

session_start();

include ('include/functions.php');

/*print_r($_GET);
echo"<p>";
print_r($_POST);
echo"<p>";
print_r($_SESSION);
echo "<p>";
*/
$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

$html="<!DOCTYPE html>
<html lang='de'>
<head>
<meta charset='UTF-8' /><title>Assessment Exercise</title>
<link rel='stylesheet' type='text/css' href='css/all.css' />";

$html.="<script>
		function saveKat(){
			alert('HALLO hier');
			$.ajax(
                {
                    type: 'POST',
                    url: 'editKat.php',
                     data: 'id=' + $('#orginalID').val() + '&newKatName=' + $('#newKatName').val()
				}
			);
			alert('Daten gespeichert');
		}
	</script>";

if(isset($_GET['element'])){

	switch ($_GET['element']){

		case('Kategorie'):
			$sql="select * from ".$_GET['element']." where deleted=0";
			foreach ($db->query($sql) as $row){
				if(md5($row['id'])==$_GET['id']){
					$html.="
					<form action ='edit.php' Method='POST'>

					<p>Bearbeiten der Kategorie: ".$row['Kategorie']."</p>
					<p>Neuer Kategoriename:
						<input type='hidden' id='orginalID' value='".$row['id']."'>
						<input type ='text'  id ='newKatName' name='Kategorie'></p>
						<input type='submit' class='KatButton' onclick='saveKat()'  name='saveNewKat' value='Kategorie speichern' width =200px>
					</form>";
				}
			}
		break;
	}
}
echo $html;