<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com
You can modify this program, but please give a credit to original author. Program is free for non-commercial use only.
(C) Peter SQ8VPS 2017
*******************************************************************************************/
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX</title>
</head>
<body>
<?php
$callsign;
$callsignraw;
session_start();
if((((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) and ((!isset($_GET['call'])) or (isset($_GET['call']) and ($_GET['call'] == "")))) or $_GET['chgif'] == "1") //if interface was not selected
{
	session_destroy(); //start session
?>
<form action="chgif.php" method="get">
Interface callsign: <input type="text" name="call">
<br>"APRSIS" is not valid interface callsign, enter anything else. <br>
Language: <select name="lang">
<option>Polski</option>
<option>English</option>
</select>
<br>
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
<center>(C) 2017 Peter SQ8VPS</center>


</body>
</html>