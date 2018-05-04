<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com & Alfredo IZ7BOJ
You can modify this program, but please give a credit to original author. Program is free for non-commercial use only.
(C) Peter SQ8VPS & Alfredo IZ7BOJ 2017-2018

Version 1.2beta
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
<?php
include 'config.php';
include 'common.php';

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
Interface callsign: <input type="text" name="call">
<br>"APRSIS" is NOT valid interface callsign, it will cause problems.<br>
Language: <select name="lang">
<option>English</option>
<option>Polski</option>
</select>
<br>
<input type="submit" value="OK">
</form>
<br><b>If you don't want to see this page, you can set static interface callsign in config.php</b><br>
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