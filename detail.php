<?php
/*
print_r($_GET);
echo "<p>";
print_r($_POST);
echo "<p>";
*/
if(isset($_SESSION)){
	print_r($_SESSION);
}
include ('include/functions.php');
$selKatBtn='';
if(isset($_GET['selKatBtn'])){
	$selKatBtn=$_GET['selKatBtn'];
}
else{
	$selKatBtn='Alle';
}

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
<header id='header'>
		<p>HTML5 / CSS3 BLOG </p>
</header>
<p></p>
<div id='wrapper'>
	<div id='detailLeft'>
	<div id='prevContainer'>";
$html.=NextPrefArtBtn($_GET['id'],$selKatBtn,'prev');
$html.="</div>";
$html.=getArtMeta($_GET['id']);
$html.="</div>";

$html.="<div id='detailRight'><div id='nextContainer'>";
$html.=NextPrefArtBtn($_GET['id'],$selKatBtn,'next');
$html.="</div>";
	$html.="<div id='katList'>";
		$html.=getAllKat($_GET['id']);
	$html.="</div>";
$html.="</div>";

$html.="<div id='detailCenter'>
";
$html.=getArtContent($_GET['id']);
$html.="</div>
		<div style='clear:both'></div>
</div>
<p>&nbsp;</p>
<p>
	<form action='index.php'>
		<input type='hidden' name='Logout' value='Logout'>
		<input type='submit' class='KatButton' value='zurück'>
	</form>
</p>

<div id='footer'><p>Footer</p></div>

</body>";

echo $html;
?>
