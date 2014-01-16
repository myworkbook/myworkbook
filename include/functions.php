<?php

	function checkSession(){
	//echo "bin in der Checksession<br>";

		if (isset($_POST['user'])&&isset($_POST['pwd'])){
			// Neue Benutzeranmeldung

			$return=0;

			$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
			$sql="select * from Benutzer where username='".$_POST['user']."' and passwort='".md5($_POST['pwd'])."'";

			//echo $sql;
			foreach ($db->query($sql) as $row){
				$id=$row['id'];
				$return=1;
			}
			if($return){
				$_SESSION['userID']=$id;
				$_SESSION['username']=$_POST['user'];
			}
			//echo 'User ID ist: '.$_SESSION['userID'].' Username ist '.$_SESSION['username'];
			return $return;

		}
		else{
			// Session muss bestehen
			$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
			$sql="select * from Benutzer where username='".$_SESSION['username']."'and id='".$_SESSION['userID']."'";
			foreach ($db->query($sql) as $row){
			$return=1;
			}
		return $return;
		}
	}



	function showArt($selKatBtn,$id,$showDetail){

	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	$sql="select Artikel.id, titel, Kategorie, username, CREATEDATE, content from Artikel, Kategorie, Benutzer  where Kategorie.id=kat_idfk and autor_idfk=Benutzer.id and Artikel.deleted=0 ";

	if($selKatBtn !='Alle'){
		$sql.=" and Kategorie='".$selKatBtn."' ";
	}

	$sql.=" order by Artikel.Timestamp desc";


	$html="

	";
	foreach ($db->query($sql) as $row){
		$html.="<div class='artikel' onclick='window.location = \"detail.php?id=".md5($row['id'])."\";'>
							<p class='artListHeadLine'>".$row['titel']." (".$row['Kategorie'].") + + + ".$row['username'].", ".$row['CREATEDATE']."</p>";


			$text=substr($row['content'],3,200);
			$text=trim($text);
			$text=substr($text,-strlen($text),strlen($text)-4);
			$html.="<p class='artListContent'>".$text."...</p>";


		$html.="</div>";
	}
	return $html;
	}


function getAllKat($id)
{
	// Hier werden die Kategorien  geladen
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	$sql="select * from Kategorie  where deleted=0 order by Kategorie";
	$html="<form action ='' Method='GET'>Kategorien";
	$html.="<div class='katList'>";
	$html.="<div class='katElement'>
				<input type='hidden' name='id' value='".$id."'>
				<input type ='submit' name='noSelKatBtn' class='KatButton' value='Alle'>
				</div>";
	foreach ($db->query($sql) as $row){
		$html.="<div class='katElement'>
				<input type ='submit' name='selKatBtn' class='KatButton' value='".$row['Kategorie']."'></div>";
	}
	$html.="</div></form>";
	return $html;
}


function getArtMeta($id){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	$sql="select Artikel.id, titel, Kategorie, username, CREATEDATE from Artikel, Kategorie, Benutzer  where Kategorie.id=kat_idfk and autor_idfk=Benutzer.id and Artikel.deleted=0 ";

	foreach ($db->query($sql) as $row){
		if(md5($row['id'])==$id){
			$html="<p>Artikelinfos:<br>".$row['titel']."<br>".$row['Kategorie']."<br> ".$row['username']."<br>".$row['CREATEDATE']."</p>";

		}
	}
	return $html;
}


function getArtContent($id){
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	$sql="select Artikel.id, titel, content from Artikel  where  Artikel.deleted=0 ";

	foreach ($db->query($sql) as $row){
		if(md5($row['id'])==$id){
			$text=trimCKBlock($row['content']);
			/*
			$text=substr($row['content'],3,strlen($row['content']));
			$text=trim($text);
			$text=substr($text,-strlen($text),strlen($text)-4);
*/

			$html="<p class='artListContent'>".$text."</p>";
			$html="<p class='artListHeadLine'>".$row['titel']."</p><p class='artListContent'>".$text."</p>";

		}
	}
	return $html;
}


function trimCKBlock($content){
	$text=substr($content,3,strlen($content));
	$text=trim($text);
	$text=substr($text,-strlen($text),strlen($text)-4);
	return $text;
}


function NextPrefArtBtn($id,$kat,$move){
	// ID Ermitteln
	//echo "ID: ". $id;
	//echo "Kategorie:".$kat;
	$html="";
	$db = new PDO('mysql:host=178.63.38.102;dbname=myworkbook', 'mwbuser', 'aVNtYsW9Z6RfdHss');
	if($kat=='Alle'){
		$sql="select Artikel.id from Artikel  where  Artikel.deleted=0 order by CREATEDATE";
	}
	else{
		$sql="select Artikel.id from Artikel, Kategorie  where Kategorie.id=kat_idfk and  Artikel.deleted=0 and Kategorie='".$kat."' order by CREATEDATE";

	}
	//echo "<p>".$sql."<p>";
	$rs=$db->prepare($sql);
	$rs->execute();
	$result=$rs->fetchAll();
	//print_r($result);
	echo "<p>";
	$i=0;
	foreach ($db->query($sql) as $row){

		if(md5($row['id'])==$id){
			$ArtID=$row['id'];
			$rowNum=$i;
		}
		$i++;
	}
	if($move=='prev'){
		// Gibt es einen Artikel davor (Datum)
		if(isset($result[$rowNum-1])){

			$PrevID=$result[$rowNum-1]['id'];
			$html.="<form action='' METHOD='GET'>
						<input type='hidden' name='id' value='".md5($PrevID)."'>
						<input type='hidden' name='selKatBtn' value='".$kat."'>
						<input type='submit' class='NavButton' value='vorheriger'>
					</form>";
		}
		else{
			// Nein Button deaktivieren
			$html.="<form action='' METHOD='GET'><input type='button' class='NavButtonInactive' value='vorheriger'></form>";
		}
	}
	else{
		if(isset($result[$rowNum+1])){

			$PrevID=$result[$rowNum+1]['id'];
			$html.="<form action='' METHOD='GET'>
				<input type='hidden' name='id' value='".md5($PrevID)."'>
				<input type='hidden' name='selKatBtn' value='".$kat."'>

				<input type='submit' class='NavButton' value='nächster'>
			</form>";
		}
		else{
			// Nein Button deaktivieren
			$html.="<form action='' METHOD='GET'><input type='button' class='NavButtonInactive' value='nächster'></form>";
		}
	}

	return $html;

}

?>