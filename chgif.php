<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com & Alfredo IZ7BOJ
You can modify this program, but please give a credit to original author. Program is free for non-commercial use only.
(C) Peter SQ8VPS & Alfredo IZ7BOJ 2017-2018

Version 1.2.1beta
*******************************************************************************************/
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="APRX stats" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>APRX statistics</title>
</head>
<body>
<center><font size="20"><b>WELCOME to APRX statistics!</b></font>
<h2>Please select interface and language before proceeding</h2> 
<br><br><br>
</center>
<?php
include 'config.php';
include 'common.php';
$i=0;
logexists();

$callsign;
$callsignraw;
session_start();
if((((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) and ((!isset($_GET['call'])) or (isset($_GET['call']) and ($_GET['call'] == "")))) or (isset($_GET['chgif']) and $_GET['chgif'] == "1")) //if interface was not selected
{
	$_SESSION = array();
	session_destroy(); //start session
	session_start();
?>
<form action="chgif.php" method="get">
Interface: <select name="call">
<?php
for ($i=0;$i<=sizeof($interfaces)-1;$i++) {
?>
	<option value=<?php echo $interfaces[$i] ?>><?php echo $interfaces[$i]." - ".$intdesc[$i] ?></option>
<?php
}
?>
</select>
<br><br><br><br><br>
Language: <select name="lang">
<option>English</option>
<option>Polski</option>
</select>
<br><br><br><br><br>
<input type="submit" value="OK">
</form>

<?php
} else {
	if(!isset($_SESSION['if'])) //if there is now "if" variable
	{
		$callsign = strtoupper($_GET['call']); //uppercase the callsign
		$_SESSION['call'] = $callsign;
	
		$callsignraw = $callsign;
		while(strlen($callsignraw) < 9)
		{
				$callsignraw .= " "; //add spaces to raw callsign
		}
		$_SESSION['if'] = $callsignraw;
		if($_GET['lang'] == "Polski")
		{
			$_SESSION['lang'] = "pl";
		} elseif($_GET['lang'] == "English")
		{
			$_SESSION['lang'] = "en";
		} else {
			$_SESSION['lang'] = "en";
		}
	}
	header('Refresh: 0; url=summary.php');
	die();
}
	
?>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>


</body>
</html>
