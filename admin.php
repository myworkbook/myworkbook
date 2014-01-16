<?php

session_start();

include ('include/functions.php');
/*
print_r($_GET);
echo"<p>";
print_r($_POST);
echo"<p>";
print_r($_SESSION);
echo "<p>";
*/
$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');


// PHP Funktionen
function newElement($Element, $handle){
	switch($Element){
		case('Kategorie'):
			$BtnLabel='Neue Kategorie';
			$ElementList="<p>
				Kategoriename: <input type ='text' name='Kategorie' id ='newKatName'>
			</p></p>
					<input type='submit' class='KatButton' onclick='saveKat()'  name='saveNewKat' value='Kategorie speichern' width =200px>";
		break;

		case('User'):
			$BtnLabel='Neuer Benutzer';
			$ElementList="<p class='adminList'>
			Username:<input type ='text' name='Username' id ='Username'><br>
			Vorname:<input type ='text' name='Vorname' id ='Vorname'><br>
			Nachname:<input type ='text' name='Nachname' id ='Nachname'><br>
			Email:<input type ='text' name='Email' id ='Email'><br>
			Passwort:<input type ='password' name='Passwort' id ='Passwort'><br>
			Passwort wiederholen:<input type ='password' name='Passwort2' id ='Passwort2'></p>
			<input type='submit' class='KatButton' onclick='saveUser()'  name='saveNewElement' value='User speichern' width =200px
			</p>";
		break;

		case('Artikel'):
			$BtnLabel='Neuer Artikel';
			$ElementList="<p>Überschrift: <input type='text' name='topic' id='topic' size='30'></p>
						<p>Kategorie: ";
			$ElementList.=ListAll('Kategorie','Kategorie','','KatList');
			$ElementList.="</p>
					<p>
						<textarea class='ckeditor' id='editor1' name='editor1'></textarea>
						<script type='text/javascript'>
							CKEDITOR.replace( 'editor1' );
						</script>
					</p>
					<p><input type='submit' class='KatButton' onclick='saveArt()'  name='saveNewArt' value='Artikel speichern' width =200px></p>";

			break;

	}

	$html="<script>
		function saveKat(){
			 var newKatName = document.getElementById('newKatName').value;
            $.ajax(
                {
                    type: 'POST',
                    url: 'createKat.php',
                    data: 'NewKatName='+newKatName //'NewKatName':+'newKatName'
				}
			);
			alert('Daten gespeichert');
		}

		function saveUser(){
			//alert('HALLO hier saveUser');
			if(document.getElementById('Passwort').value=document.getElementById('Passwort2').value){
			 var Username = document.getElementById('Username').value;
			 var Vorname = document.getElementById('Vorname').value;
			 var Nachname = document.getElementById('Nachname').value;
			 var Email = document.getElementById('Email').value;
			 var Passwort = document.getElementById('Passwort').value;
            $.ajax(
                {
                    type: 'POST',
                    url: 'createUser.php',
                    data: 	'Username='+Username+
                    		'&Vorname='+Vorname+
                    		'&Nachname='+Nachname+
                    		'&Email='+Email+
                    		'&Passwort='+Passwort
				}
			);
			alert('Daten gespeichert');
			}
			else{
				alert('Eingebene Passwörter stimme nicht überein.');
			}

		}


		function saveArt(){
			 var newTopic = document.getElementById('topic').value;
			 var KatID = document.getElementById('KatList').value;
			 var ArtText = CKEDITOR.instances.editor1.getData();
            $.ajax(
                {
                    type: 'POST',
                    url: 'createArt.php',
                    data: 	'newTopic='+newTopic+
                     		'&KatID='+KatID+
                     		'&ArtText='+ArtText
				}
			);
			alert('Daten gespeichert');
		}
		</script>";

	if($handle=='detail' && $_POST['Element']==$Element){
		// Feldauflistung
		$html.="
				<form action ='admin.php' Method='POST'>
				<p>".$Element.":</p>";
		$html.=$ElementList;
		$html.="
				</form>"
		;
	}
	else{
		//Button
		$html.="<form action='admin.php' METHOD='POST'>
					<input type='hidden' name='Element' value='".$Element."'>
					<input type='hidden' name='handle' value='detail'>
					<input class='KatButton' type='submit' name='newElement' value='".$BtnLabel."'>
				</form>";
	}
	return $html;



}



function show($Element){

	$html="<div class='clearfix'>";
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	switch($Element){
		case ('Kategorie'):
			$sql="select * from Kategorie  where deleted=0 order by Kategorie";
		break;
		case ('User'):
			$sql="select * from Benutzer  where deleted=0 order by nachname";
		break;
		case('Artikel'):
			$sql="select Artikel.id, titel, Kategorie, content from Artikel, Kategorie  where Kategorie.id=kat_idfk and Artikel.deleted=0 order by Titel";
		break;
	}
	foreach ($db->query($sql) as $row){
		switch($Element){
			case ('Kategorie'):
				$content=$row['Kategorie'];
				break;
			case ('User'):
				$content=$row['username'];
			break;
			case ('Artikel'):
				$content="<p class='artListHeadLine'>".$row['titel']." (".$row['Kategorie'].")</p>";
				$text=substr($row['content'],3,200);
				$text=trim($text);
				$text=substr($text,-strlen($text),strlen($text)-4);
				$content.="<p class='artListContent'>".$text."...</p>";
				break;
		}
		$html.="<div class='adminElement'>";
		$linx="<a href ='admin.php?action=edit&element=".$Element."&id=".md5($row['id'])."'>bearbeiten</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href='admin.php?element=".$Element."&action=delete&id=".md5($row['id'])."'>löschen</a><br>";

		switch($Element)
		{
			case ('Kategorie'):
				if(
					isset($_GET['action']) &&
					isset($_GET['id'])){

					if(
						$_GET['action']=='edit' &&
						$_GET['element']==$Element &&
						$_GET['id']==md5($row['id'])){

						$html.="<form action ='admin.php' Method='POST'>
						<script>
							function saveEditKat(){
								var newKatName = document.getElementById('newKatName').value;
								var originalID = document.getElementById('orginalID').value;
								$.ajax({
                    				type: 'POST',
                    				url: 'editKat.php',
                   					data: 	'id=' + originalID +
                   							'&newKatName=' + newKatName
									}
								);
								alert('Daten gespeichert');
							}
						</script>";
						$html.="<p>Kategoriename:
								<input type ='text' name='Kategorie' id ='newKatName' value='".$row['Kategorie']."'>
								<input type='hidden' id='orginalID' name='originalID' value='".$row['id']."'></p>
								<p><input type='submit' class='KatButton' onclick='saveEditKat()'  name='saveEditKatBtn' value='Kategorie speichern' width =200px></p>
						</form>";
						}

					else{
						$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
						$html.=$linx;
					}
				}
				else{
					$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
					$html.=$linx;
				}

			break;

			case('User'):
				if(
					isset($_GET['action']) &&
					isset($_GET['id'])
				){
					if(
						$_GET['action']=='edit' &&
						$_GET['element']==$Element &&
						$_GET['id']==md5($row['id'])
					)
					{
						$html.="<form action ='admin.php' Method='POST'>
								<script>
									function saveEditUser(){
										if(document.getElementById('Passwort').value==document.getElementById('Passwort2').value){
											var id = document.getElementById('orginalID').value;
											var Username = document.getElementById('Username').value;
				 							var Vorname = document.getElementById('Vorname').value;
				 							var Nachname = document.getElementById('Nachname').value;
				 							var Email = document.getElementById('Email').value;
				 							var Passwort = document.getElementById('Passwort').value;
	            							$.ajax(
	                							{
	                    							type: 'POST',
	                    							url: 'editUser.php',
	                    							data: 	'id='+id+
	                    									'&Username='+Username+
    	                									'&Vorname='+Vorname+
                    										'&Nachname='+Nachname+
                    										'&Email='+Email+
                    										'&Passwort='+Passwort
												}
											);
											alert('Daten gespeichert');
										}
										else{
											alert('Eingebene Passwörter stimme nicht überein.');
										}
									}
								</script>";
								$html.="<p class='adminList'>
										Username:<input type ='text' name='Username' id ='Username' value='".$row['username']."'><br>
										Vorname:<input type ='text' name='Vorname' id ='Vorname' value='".$row['vorname']."'><br>
										Nachname:<input type ='text' name='Nachname' id ='Nachname' value='".$row['nachname']."'><br>
										Email:<input type ='text' name='Email' id ='Email' value='".$row['email']."'><br>
										Passwort:<input type ='password' name='Passwort' id ='Passwort'><br>
										Passwort wdh:<input type ='password' name='Passwort2' id ='Passwort2'></p>
										<input type='hidden' id='orginalID' name='originalID' value='".$row['id']."'></p>
										<p><input type='submit' class='KatButton' onclick='saveEditUser()'  name='saveEditKatBtn' value='Benutzer speichern' width =200px></p>
								</form>";
					}
					else{
						$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
						$html.=$linx;
					}
				}
				else{
					$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
					$html.=$linx;
				}
			break;

			case ('Artikel'):
				if(
					isset($_GET['action']) &&
					isset($_GET['id'])){

					if(
						$_GET['action']=='edit' &&
						$_GET['element']==$Element &&
						$_GET['id']==md5($row['id'])){

						$html.="<form action ='admin.php' Method='POST'>
						<script>
							function saveEditArt(){
								var ArtTitle = document.getElementById('ArtTitle').value;
								var KatID = document.getElementById('KatID').value;
								var ArtText = CKEDITOR.instances.editor1.getData();
								var originalID = document.getElementById('orginalID').value;
								$.ajax({
                    				type: 'POST',
                    				url: 'editArt.php',
                   					data: 	'id=' + originalID +
                   							'&ArtTitle=' + ArtTitle +
                   							'&KatID=' + KatID +
                   							'&ArtText='+ ArtText
									}
								);
								alert('Daten gespeichert');
							}
						</script>";
						$html.="<p>Titel:<input type ='text' name='Titel' id ='ArtTitle' value='".$row['titel']."' size =50></p>
								<p>Kategorie:";
						$html.=ListAll('Kategorie','Kategorie',$row['Kategorie'],'KatID')."</p>";
						$html.="<p><input type='hidden' id='orginalID' name='originalID' value='".$row['id']."'></p>";
						$html.="<p>
								<textarea class='ckeditor' id='editor1' name='editor1'>".$row['content']."</textarea>
								<script type='text/javascript'>
									CKEDITOR.replace( 'editor1' );
								</script>
								</p>";
						$html.="<p>
								<input type='submit' class='KatButton'   name='AbortBtn' value='Abbrechen' width =200px>
								<input type='submit' class='KatButton' onclick='saveEditArt()'  name='saveEditArtBtn' value='Artikel speichern' width =200px></p>
						</form>";
					}

					else{
						$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
						$html.=$linx;
					}
				}
				else{
					$html.=$content."&nbsp;&nbsp;&nbsp;&nbsp;";
					$html.=$linx;
				}

				break;

		}

		$html.="</div>";
	}
	$html.="</div>";
	return $html;


}



function ListAll($Tab,$Feld,$selektiert,$Name){
	// Liefert alle nicht gelöschten Elemente einer Tabelle sowie die ID als Drop Down
	// Übergabewerte :
	// $Tab = Name der MySQL-Tabelle
	// $Feld = Schlüsselfeld der Tabelle
	// $selektiert = Wert, der vorselektiert werden soll
	// $Name = Name des Drop Down Elements

	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');

	$html="<select name='".$Name."' id='".$Name."' size=1>";
	$sql="select id,".$Feld." from ".$Tab." where deleted=0 order by ".$Feld;
	foreach ($db->query($sql) as $row){
		if($row[$Feld]==$selektiert){
			$html.="<option value='".$row['id']."' selected>".$row[$Feld]."</option>";
		}
		else{
			$html.="<option value='".$row['id']."'>".$row[$Feld]."</option>";
		}
	}
	$html.="</select>";

	return $html;
}


// Elemente löschen
if(isset($_GET['action'])){
	if($_GET['action']=='delete')
	{
		$sql="select id from ".$_GET['element']." where deleted=0";
		foreach ($db->query($sql) as $row){
			if(md5($row['id'])==$_GET['id']){
				$sql="delete from ".$_GET['element']." where id=".$row['id'];
				$db->query($sql);
			}
		}
	}
}

// LOGOUT
if(isset($_GET['Logout'])){
	session_unset();
	header('Location: index.php');
}





// Seitenaufbau
if(checkSession()){

	$html="<!DOCTYPE html>
	<html lang='de'>
	<head>
		<meta charset='UTF-8' /><title>Assessment Exercise</title>
		<link rel='stylesheet' type='text/css' href='css/all.css' />
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js'></script>
		<script type='text/javascript' src='ckeditor/ckeditor.js'></script>

	</head>
	<body>

		Die Admin Seite<p>

		<div class='adminArtikelContainer'>Artikelverwaltung";
	$html.=show('Artikel');
	if (!isset($_POST['handle'])){
		$_POST['handle']='';}
	$html.=newElement('Artikel',$_POST['handle']);
	$html.="</div><p>

		<div class='adminContainer'>Kategorieverwaltung";
	$html.=show('Kategorie');
	if (!isset($_POST['handle'])){
		$_POST['handle']='';}
	$html.=newElement('Kategorie',$_POST['handle']);
	$html.="</div><br>

	<div class='adminContainer'>Userverwaltung";
	$html.=show('User');

	if (!isset($_POST['handle'])){
		$_POST['Userhandle']='';}
	$html.=newElement('User',$_POST['handle']);
	$html.="</div>";

	$html.="<p>
			<form>
				<input type='hidden' name='Logout' value='Logout'>
				<input type='submit' class='KatButton' value='Logout'>
			</form>
			</p>";


$html.="	</body>
	</html>";
}
else{
	session_unset();
	header('Location: index.php');

}


echo $html;


