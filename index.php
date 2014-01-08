<?php


require ("include/db.class.php");

$db = new DB();

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
<div id='wrapper'>
<header id='header'>
		<p>HTML5 / CSS3 BLOG </p>
</header>

	<div class='clearfix'>
		<div class='artikelContainer'>";

$html.="	<p><div class='artikel'>test
			</div></p>
			<p><div class='artikel'>test
			</div></p>";
$html.="</div>
		<div class='KategorieContainer'>
			<form action='index.php' method ='POST'>
				<input type ='submit' class='KatButton' value='test'>
			</form>
		</div>
		<div style='clear:both'</div>
	</div>
</div>
<div id='footer'><p>Footer</p></div>

<p></p>
<div class='LoginDiv'>
<form action='admin.php' method ='POST'>
				<p>Username: <input type ='text' name='user' size='20'></p>
				<p>Passwort&nbsp;&nbsp;: <input type ='password' name='pwd' size='20' ></p>
				<input type ='submit' class='LoginButton' value='Anmelden'>
			</form>
</div>

</body>";

echo $html;


function getAllArt()
{
	// Hier werden die Artikel geladen
}
?>