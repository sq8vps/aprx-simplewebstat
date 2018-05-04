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
<?php
include 'config.php';
include 'common.php';

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

if(!isset($_GET['time']) or ($_GET['time'] == "")) //if time range not specified
{
	$_GET['time'] = 1;
} 

if($lang == "en")
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="APRX statistics" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>APRX statistics - stations' info</title>
</head>
<body>
<center><font size="20"><b>APRX statistics</b></font>
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - stations' info</h2> <a href="chgif.php?chgif=1">Change interface</a></center>
<br><br><br>

<br><b>Show:</b> <a href="summary.php">Summary and received stations</a> - <a href="stations.php">Stations' informations</a> - <a href="frames.php">Show frames from specified station</a> - <a href="details.php">Show details of a specified station</a>
<br><br><hr>
</center>
	<br> <form action="stations.php" method="get">
	Show stations since last: 
	<select name="time">
	<option value="1" <?php if($_GET['time'] == 1) echo 'selected=\"selected\"'?>>1 hour</option>
	<option value="2" <?php if($_GET['time'] == 2) echo 'selected=\"selected\"'?>>2 hours</option>
	<option value="4" <?php if($_GET['time'] == 4) echo 'selected=\"selected\"'?>>4 hours</option>
	<option value="6" <?php if($_GET['time'] == 6) echo 'selected=\"selected\"'?>>6 hours</option>
	<option value="12" <?php if($_GET['time'] == 12) echo 'selected=\"selected\"'?>>12 hours</option>
	<option value="24" <?php if($_GET['time'] == 24) echo 'selected=\"selected\"'?>>1 day</option>
	<option value="48" <?php if($_GET['time'] == 48) echo 'selected=\"selected\"'?>>2 days</option>
	<option value="168" <?php if($_GET['time'] == 168) echo 'selected=\"selected\"'?>>week</option>
	<option value="720" <?php if($_GET['time'] == 720) echo 'selected=\"selected\"'?>>30 days</option>
	<option value="e" <?php if($_GET['time'] == 'e') echo 'selected=\"selected\"'?>>all</option>
</select>
<input type="submit" value="Refresh">
<br>
<?php
} else {
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX - informacje o stacjach</title>
</head>
<body>
<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - informacje o stacjach</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br><br><br>

<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie i odebrane stacje</a> - <a href="stations.php">Informacje o stacjach</a> - <a href="frames.php">Pokaż ramki wybranej stacji</a> - <a href="details.php">Pokaż szczegóły wybranej stacji</a>
<br><br><hr>
</center>
<br>
	<br><br> <form action="stations.php" method="get">
	Pokaż stacje z czasu: 
	<select name="time">
	<option value="1" <?php if($_GET['time'] == 1) echo 'selected=\"selected\"'?>>1 godziny</option>
	<option value="2" <?php if($_GET['time'] == 2) echo 'selected=\"selected\"'?>>2 godzin</option>
	<option value="4" <?php if($_GET['time'] == 4) echo 'selected=\"selected\"'?>>4 godzin</option>
	<option value="6" <?php if($_GET['time'] == 6) echo 'selected=\"selected\"'?>>6 godzin</option>
	<option value="12" <?php if($_GET['time'] == 12) echo 'selected=\"selected\"'?>>12 godzin</option>
	<option value="24" <?php if($_GET['time'] == 24) echo 'selected=\"selected\"'?>>1 dnia</option>
	<option value="48" <?php if($_GET['time'] == 48) echo 'selected=\"selected\"'?>>2 dni</option>
	<option value="168" <?php if($_GET['time'] == 168) echo 'selected=\"selected\"'?>>tygodnia</option>
	<option value="720" <?php if($_GET['time'] == 720) echo 'selected=\"selected\"'?>>30 dni</option>
	<option value="e" <?php if($_GET['time'] == 'e') echo 'selected=\"selected\"'?>>wszystko</option>
	</select>
	<input type="submit" value="Odśwież">
<br>
<?php
}

$time = 0; //start of the time from which to read data from log in Unix timestamp type
$staticstations = array();
$movingstations = array();
$otherstations = array();
$directstations = array(); //stations received directly
$viastations = array(); //stations received via digi

if(!isset($_GET['time']) or ($_GET['time'] == "")) //if time range not specified
{
	$time = time() - 3600; //so take frames from last 1 hour
} 
elseif($_GET['time'] == "e") //if whole log
{
	$time = 0;
}
else //else if the time range is choosen
{
	$time = time() - ($_GET['time'] * 3600); //convert hours to seconds
}

function stationparse($frame) //function for parsing station information
{
	global $staticstations;
	global $movingstations;
	global $otherstations;
	global $viastations; //stations received via digi
	global $directstations; //stations received directly
	global $callraw;
	global $time;
	global $cntalias;
	$fg = 0;
	if(strpos($frame, $callraw." R")) //if frame received by RF
	{
		$uu = substr($frame, 0, 19);  //take only the part of the line, where date and time is
		$uu = strtotime($uu) + date('Z'); //convert string with date and time to the Unix timestamp and add a timezone shift
		if($uu > $time) //if frame was received in out time range
		{
			$aa = explode(">", $frame); //divide frame from > separator to get station's callsign
			$stationcall = substr($aa[0], strpos($aa[0], $callraw." R ") + strlen($callraw." R ")); //remove date and time, interface call up to the received station's call, so that we get only station's call
			$bb = substr($frame, 46); //let's cut temporarily some part of a frame to make sure, that there is no : character, because we want it only as a separator between frame path and info field
			//------DEBUG-----^^^^^^ this can make some problems, beacuse it's very primitive
			$bb = substr($bb, strpos($bb, ":") + 1); //get whole date from the frame after a : character, to get info field
			if(($bb[0] === "@") or ($bb[0] === "!") or ($bb[0] === "=") or ($bb[0] === "/") or (ord($bb[0]) === 96) or ($bb[0] === "\'")) //if it's a frame with position or Mic-E position			
			{
				if($bb[7] === 'z') //if the positions contains timestamp shift reading symbol data by 7 characters
				{
					$fg = 26;
				} else 
				{
					$fg = 19;
				}
				if((ord($bb[0]) === 96) or ($bb[0] === "'")) //special case - if Mic-E postion
				{
					$fg = 7; //set symbol place to 7
				}
					if(in_array($bb[$fg], array('!', '#', '%', '&', '+', ',', '-', '.', '/', ':', ';', '?', '@', 'A', 'B', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'T', 'V', 'W', 'Z', '\\', ']', '_', '`', 'c', 'd', 'h', 'i', 'l', 'm', 'n', 'o', 'q', 'r', 't', 'w', 'x', 'y', 'z', '}')))
					{
						if(!in_array($stationcall, $staticstations))
						{
							$staticstations[] = $stationcall;
						}
					}	
					elseif(in_array($bb[$fg], array('$', '\'', '(', ')', '*', '<', '=', '>', 'C', 'F', 'P', 'R', 'U', 'X', 'Y', '[', '^', 'a', 'b', 'f', 'g', 'j', 'k', 'p', 's', 'u', 'v')))
					{
						if(!in_array($stationcall, $movingstations))
						{
							$movingstations[] = $stationcall;
						}
					}
					else
					{
						if(!in_array($stationcall, $otherstations))
						{
							$otherstations[] = $stationcall;
						}
					}	
			}
			
			$cc = substr($frame, strpos($frame, ">")); //temporarily get everything after > symbol (after received station callsign)
			$cc = substr($cc, 0, strpos($cc, ":")); //and then everything before info field separator, so that we have only frame path right now
			if(strpos($cc, '*') !== false) //if there is a * the frame was definitely not heard directly
			{
				if(!in_array($stationcall, $viastations))
				{
					$viastations[] = $stationcall;
				}
			} else //if there is no *
			{
				if($cntalias == "") //if no national alias selected, take frame as not direct
				{
						if(!in_array($stationcall, $viastations))
						{
							$viastations[] = $stationcall;
						}	
						return;	
				}
				$cntpos = strpos($cc, $cntalias);
				if((strpos($cc, $cntalias) !== false) and ($cc[$cntpos + 3] == "-")) //if there is national untraced alias without *, the frame still can be heard indirectly
				{
					if($cc[$cntpos + 2] == $cc[$cntpos + 4]) //if this path element has n=N, for example SP2-2, it was heard directly
					{
						if(!in_array($stationcall, $directstations))
						{
							$directstations[] = $stationcall;
						}
					} else //else if n!=N, for example SP2-1, the frame was PROBABLY heard via digi
					{
						if(!in_array($stationcall, $viastations))
						{
							$viastations[] = $stationcall;
						}
					}
				} else //if there is no national alias, it was heard directly
				{
					if(!in_array($stationcall, $directstations))
					{
						$directstations[] = $stationcall;
					}
				}
			}
		}
	}
	
}

$logfile = file($logpath); //read log file
$linesinlog = count($logfile);
$lines = 0;
while ($lines < $linesinlog) { //read line by line
    $line = $logfile[$lines];
	stationparse($line);
	$lines++;
} 

asort($movingstations); //sort
	asort($staticstations);
	asort($otherstations);
	asort($viastations);
	asort($directstations);
if($lang == "en")
{	
	echo "<br><br><hr /><br><font color=\"blue\"><b>Moving stations (<u>".count($movingstations)."</u>):</b></font> ";
	while(list($u, $o) = each($movingstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"red\"><b>Static stations (<u>".count($staticstations)."</u>):</b></font> ";
	while(list($u, $o) = each($staticstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"green\"><b>Other stations (<u>".count($otherstations)."</u>):</b></font> ";
	while(list($u, $o) = each($otherstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stations received indirectly (via digi) (<u>".count($viastations)."</u>):</b></font> ";
	while(list($u, $o) = each($viastations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stations received directly (<u>".count($directstations)."</u>):</b></font> ";
	while(list($u, $o) = each($directstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
} else {
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje ruchome (<u>".count($movingstations)."</u>):</b></font> ";
	while(list($u, $o) = each($movingstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje stałe (<u>".count($staticstations)."</u>):</b></font> ";
	while(list($u, $o) = each($staticstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"green\"><b>Inne stacje (<u>".count($otherstations)."</u>):</b></font> ";
	while(list($u, $o) = each($otherstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje odebrane pośrednio (przez digi) (<u>".count($viastations)."</u>):</b></font> ";
	while(list($u, $o) = each($viastations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje odebrane bezpośrednio (<u>".count($directstations)."</u>):</b></font> ";
	while(list($u, $o) = each($directstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$o.'">'.$o.'</a>'.", ";
	}
}
?>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>
</body>
</html>