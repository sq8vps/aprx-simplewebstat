<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com & Alfredo IZ7BOJ
You can modify this program, but please give a credit to original authors. Program is free for non-commercial use only.
(C) Peter SQ8VPS & Alfredo IZ7BOJ 2017-2018

Version 1.3.1beta
*******************************************************************************************/

include 'config.php';
include 'common.php';
include 'functions.php';

logexists();
session_start();
if(!isset($_SESSION['if'])) //if not in session
{
	header('Refresh: 0; url=chgif.php?chgif=1'); //go to the interface change page
	die();
}
$call = $_SESSION['call'];
$callraw = $_SESSION['if'];
$lang = $_SESSION['lang'];


$posframefound = 0; //true if position frame of the station already found
$otherframefound = 0; //true if any other frame of the station already found
$scall = "";
$noofframes = 0;

$posdate = "";
$postime = "";

$otherdate = "";
$othertime = "";

$posframe = "";
$otherframe = "";

$lastpath = "";

$symboltab = "";
$symbol = "";

$comment = "";
$status = "";

$distance = 0;
$bearing = 0;

$mice = 0;

$declat = 0;
$declon = 0;

$tocall = "";

if(isset($_GET['getcall']) && ($_GET['getcall'] != ""))
{
	$scall = strtoupper($_GET['getcall']);
	$logfile = file($logpath); //read log file
	$linesinlog = count($logfile);
	$lines = $linesinlog - 1;
	while ($lines > 0) { //read line by line but starting from the newest frame!
		$line = $logfile[$lines];
		frameparse($line);
		$lines--;
	}
device();	
}

if($lang == "en")
{
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="APRX statistics" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>APRX statistics - stations' info</title>
</head>
<body>
<?php
if(file_exists($logourl)){
?>
<center><img src="<?php echo $logourl ?>" width="100px" height="100px" align="middle"></center><br>
<?php
}
?>
<center><font size="20"><b>APRX statistics</b></font>
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - station's details</h2> <a href="chgif.php?chgif=1">Change interface</a>
<br>
<br><b>Show:</b> <a href="summary.php">Summary (main)</a> - <a href="frames.php">RAW frames from specified station</a> - <a href="details.php">Details of a specified station</a><br><br>
<hr>
</center>
<br>

<form action="details.php" method="get">
Show details of station: <input type="text" name="getcall" <?php if(isset($_GET['getcall'])) echo 'value="'.$_GET['getcall'].'"'; ?>>
<input type="submit" value="Show">
</form>
<br>

<?php
if(isset($_GET['getcall']) && ($_GET['getcall'] != ""))
{
	if(($posframefound == 0) && ($otherframefound == 0))
	{
		echo '<font size="6">No frames found for station <b>'.$scall.'</b>.</font>';
	}
	else
	{
		echo '<b><font color="blue" size="8">'.$scall.'</font></b>';
		echo '<br><a href="https://aprs.fi/?call='.$scall.'" target="_blank">Show on aprs.fi</a><br><br>';
		echo '<b>Frames heard: </b>'.$noofframes;
		if($posframefound) 
		{			
			echo '<br><br><font color="blue"><b>Last position frame heard:</b> '.$posdate.' '.$postime.' GMT (';
			$dc = time() -date('Z') - strtotime($posdate.' '.$postime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s ago)</font>';
			echo '<br><font color="red"><b>Station position: </b>'.$declat.', '.$declon.' - <b>'.$distance.' km '.$bearing.'° from your location</b></font>';
			echo '<br><font color="green"><b>Frame comment: </b>'.$comment.'</font>';
			echo '<br><br><b>Frame type:</b> ';
			if($mice) echo 'Mic-E compressed frame'; else echo 'Uncompressed frame';
			echo '<br><b>Station symbol:</b> '.$symboltab.$symbol;
			echo '<br><b>Frame path:</b> '.$scall.'>'.$lastpath;
			echo '<br><b>Device:</b> '.$device;
		}
		
		if($otherframefound) 
		{			
			echo '<br><br><b>Last status frame heard:</b> '.$otherdate.' '.$othertime.' (';
			$dc = time() - date('Z') - strtotime($otherdate.' '.$othertime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s ago)';
			echo '<br><b>Status: </b>'.$status;
		}
		
		
	
	
	
	
	}

} 

}else {
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX - informacje o stacjach</title>
</head>
<body>
<?php
if(file_exists($logourl)){
?>
<center><img src="<?php echo $logourl ?>" width="100px" height="100px" align="middle"></center><br>
<?php
}
?>
<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - szczegóły stacji</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br>
<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie (główna)</a> - <a href="frames.php">Surowe ramki wybranej stacji</a> - <a href="details.php">Szczegóły wybranej stacji</a><br><br>
<hr>
</center>
<br>
<form action="details.php" method="get">
Pokaż szczegóły stacji: <input type="text" name="call" <?php if(isset($_GET['call'])) echo 'value="'.$_GET['call'].'"'; ?>>
<input type="submit" value="Pokaż">
</form>
<br>





<?php

if(isset($_GET['getcall']) && ($_GET['getcall'] != ""))
{
	if(($posframefound == 0) && ($otherframefound == 0))
	{
		echo '<font size="6">Nie znaleziono ramek dla stacji <b>'.$scall.'</b>.</font>';
	}
	else
	{
		echo '<b><font color="blue" size="8">'.$scall.'</font></b>';
		echo '<br><a href="https://aprs.fi/?call='.$scall.'" target="_blank">Pokaż na aprs.fi</a><br><br>';
		echo '<b>Ramek odebranych: </b>'.$noofframes;
		if($posframefound) 
		{			
			echo '<br><br><font color="blue"><b>Ostatnia ramka pozycyjna odebrana:</b> '.$posdate.' '.$postime.' GMT (';
			$dc = time() - date('Z') - strtotime($posdate.' '.$postime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s temu)</font>';
			echo '<br><font color="red"><b>Pozycja stacji: </b>'.$declat.', '.$declon.' - <b>'.$distance.' km '.$bearing.'° od twojej lokalizacji</b></font>';
			echo '<br><font color="green"><b>Komentarz: </b>'.$comment.'</font>';
			echo '<br><br><b>Typ ramki:</b> ';
			if($mice) echo 'Ramka skompresowana Mic-E'; else echo 'Ramka nieskompresowana';
			echo '<br><b>Symbol stacji:</b> '.$symboltab.$symbol;
			echo '<br><b>Ścieżka ramki:</b> '.$scall.'>'.$lastpath;
			echo '<br><b>Device:</b> '.$device;
		}
		
		if($otherframefound) 
		{			
			echo '<br><br><b>Ostatnia ramka statusowa odebrana:</b> '.$otherdate.' '.$othertime.' (';
			$dc = time() - date('Z') - strtotime($otherdate.' '.$othertime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s temu)';
			echo '<br><b>Status: </b>'.$status;
		}
		

	
	}

} 



}
?>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>
</body>
</html>
