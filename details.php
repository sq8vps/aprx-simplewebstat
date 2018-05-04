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

function nmeatodec($data, $shift)
{
	$dec = 0;
	$dec += ($data[$shift] * 10);
	$dec += $data[1 + $shift];
						
	$temp = 0;
						
	$temp += ($data[2 + $shift] * 10);
	$temp += $data[3 + $shift];
						
	$temp += ($data[5 + $shift] / 10);
	$temp += ($data[6 + $shift] / 100);
						
	$temp /= 60;
						
	$dec += $temp;
	
	return $dec;
}

function mice_decode($dest, $info)
{
   //conversion of Mic-E posistion to DDMMmm format
	global $declat;
	global $declon;
	$declat = 0;
	$declon = 0;
	$ghf = ord($dest[0]);
	
    if($ghf <= 57) $declat += ($ghf - 48) * 100000;
    else if(($ghf >= 65) && ($ghf <= 74)) $declat += ($ghf - 65) * 100000;
    else if(($ghf >= 80) && ($ghf <= 89)) $declat += ($ghf - 80) * 100000;

    $ghf = ord($dest[1]);
    if($ghf <= 57) $declat += ($ghf - 48) * 10000;
    else if(($ghf >= 65) && ($ghf <= 74)) $declat += ($ghf - 65) * 10000;
    else if(($ghf >= 80) && ($ghf <= 89)) $declat += ($ghf - 80) * 10000;

    $ghf = ord($dest[2]);
    if($ghf <= 57) $declat += ($ghf - 48) * 1000;
    else if(($ghf >= 65) && ($ghf <= 74)) $declat += ($ghf - 65) * 1000;
    else if(($ghf >= 80) && ($ghf <= 89)) $declat += ($ghf - 80) * 1000;

    $ghf = ord($dest[3]);
    if($ghf <= 57)
    {
        $declat += ($ghf - 48) * 100;
        $declat = $declat * (-1);
    }
    else if(($ghf >= 80) && ($ghf <= 89))
    {
        $declat += ($ghf - 80) * 100;
    }

    $looff = 0;

    $ghf = ord($dest[4]);
    if($ghf <= 57)
    {
        $declat += ($ghf - 48) * 10;
        $looff = 0;
    }
    else if(($ghf >= 80) && ($ghf <= 89))
    {
        $declat += ($ghf - 80) * 10;
        $looff = 100;
    }

    $lonneg = 0;

    $ghf = ord($dest[5]);
    if($ghf <= 57)
    {
        $declat += $ghf - 48;
        $lonneg = 1;
    }
    else if(($ghf >= 80) && ($ghf <= 89))
    {
        $declat += $ghf - 80;
        $lonneg = -1;
    }

    $ghf = ord($info[1]);
    $ghf -= 28;
    $ghf += $looff;
    if(($ghf <= 189) && ($ghf >= 180)) $ghf -= 80;
    else if(($ghf <= 199) && ($ghf >= 190)) $ghf -= 190;

    $declon += ($ghf * 10000);

    $ghf = ord($info[2]);
    $ghf -= 28;
    if($ghf >= 60) $ghf -= 60;

    $declon += ($ghf * 100);

    $ghf = ord($info[3]);
    $ghf -= 28;
    $declon += $ghf;

	
    $declon = $declon * $lonneg;
	

	//converting DDMMmm to DDdddddd
	//latitude
	$tt = 0;
	$tt += (int)($declat / 10000);
	
	$temp = ($declat % 10000) / 100;
	$temp /= 60;
	
	$declat = $tt + $temp;
	
	
	//longtitude
	$tt = 0;
	$tt += (int)($declon / 10000);
	
	
	$temp = ($declon % 10000) / 100;
	$temp /= 60;
	
	$declon = $tt + $temp;

}


function frameparse($frame)
{
	global $callraw;
	global $scall;
	global $posframefound;
	global $otherframefound;
	global $posframe;
	global $otherframe;
	global $lastpath;
	global $noofframes;
	global $symbol;
	global $symboltab;
	global $stationlat;
	global $stationlon;
	global $distance;
	global $declat;
	global $declon;
	global $posdate;
	global $postime;
	global $comment;
	global $status;
	global $otherdate;
	global $othertime;
	global $mice;
	global $bearing;

		$packet = substr($frame, 36); //get only frame, without interface call, date etc.
		$aa = explode(">", $packet); //get the callsign
		
		if($aa[0] == $scall)
		{
			
			$noofframes++;
			if($posframefound and $otherframefound) return;
			$bb = explode(":", $packet); //get only info field, so everything after : separator
			$dd = substr($bb[1], 0); //i have no idea, but i must do this, because without this there are some problems
						
				
			if(($dd[0] === "@") or ($dd[0] === "!") or ($dd[0] === "=") or ($dd[0] === "/") or (ord($dd[0]) === 96) or ($dd[0] === "'")) //if it's a frame with position or Mic-E position	
			{
				
				if($posframefound) return; //if we have already position frame parsed, just skip it
			
				
			
				$posframe = $packet; //save whole posistion frame
				
				$posdate = substr($frame, 0, 10); //extract date
				$postime = substr($frame, 11, 8); //extract time
				
				$path = explode(">", $bb[0]); //take everything after station callsign and before info field (see bb[0])
				$lastpath = $path[1]; //take only path part
				
				$posframefound = 1; //newest position frame found
				
				if((ord($dd[0]) === 96) or ($dd[0] === "'")) //if it's a Mic-E frame
				{
					$mice = 1;
					
					$symboltab = $dd[8];
					$symbol = $dd[7];
					
					$destaddr = explode(",", $aa[1]); //get destination address, which encodes latitude
					
					mice_decode($destaddr[0], $bb[1]);
					
					$comment = substr($bb[1], 9);
					
				}
				else //if it's a standard frame
				{
					$mice = 0;
					if($dd[7] === 'z') //if the positions contains timestamp
					{
						$symboltab = $dd[16];
						$symbol = $dd[26];
						$comment = substr($dd, 27);
						
						$shft = 7;

					} else 
					{
						$symboltab = $dd[9];
						$symbol = $dd[19];
						$comment = substr($dd, 20);
						$shft = 0;
					}	

					//convert NMEA to decimal degrees
					$declat = nmeatodec($dd, 1 + $shft);
					if($dd[8 + $shft] == 'S') $declat *= -1;
					$declon = nmeatodec($dd, 11 + $shft);
					if($dd[18 + $shft] == 'W') $declon *= -1;
						

				}
				
				//haversine formula for distance calculation	
				$latFrom = deg2rad($stationlat);
				$lonFrom = deg2rad($stationlon);
				$latTo = deg2rad($declat);
				$lonTo = deg2rad($declon);

				$latDelta = $latTo - $latFrom;
				$lonDelta = $lonTo - $lonFrom;
				
				$bearing = rad2deg(atan2(sin($lonDelta)*cos($latTo), cos($latFrom)*sin($latTo)-sin($latFrom)*cos($latTo)*cos($latDelta)));
				if($bearing < 0) $bearing += 360;

				$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
				cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
				$distance = round($angle * 6371, 2); //gives result in km rounded to 2 digits after comma
				
				$declat = round($declat, 5);
				$declon = round($declon, 5);
				$bearing = round($bearing, 1);
									
			}
			else if(($dd[0] === ">") or ($dd[0] === "<") or ($dd[0] === "{")) //if it's a status or beacon frame
			{
				if($otherframefound) return; //if we have already status frame parsed, just skip it
			
				$otherframe = $packet; //save whole status frame
				
				$status = substr($dd, 1);
				
				$otherdate = substr($frame, 0, 10); //extract date
				$othertime = substr($frame, 11, 8); //extract time
				
				$otherframefound = 1; //newest beacon frame found
			}
		}

}

if(isset($_GET['call']) && ($_GET['call'] != ""))
{
	$scall = strtoupper($_GET['call']);
	$logfile = file($logpath); //read log file
	$linesinlog = count($logfile);
	$lines = $linesinlog - 1;
	while ($lines > 0) { //read line by line but starting from the newest frame!
		$line = $logfile[$lines];
		frameparse($line);
		$lines--;
	}
	
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
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - station's details</h2> <a href="chgif.php?chgif=1">Change interface</a></center>
<br><br><br>

<br><b>Show:</b> <a href="summary.php">Summary and received stations</a> - <a href="stations.php">Stations' informations</a> - <a href="frames.php">Show frames from specified station</a> - <a href="details.php">Show details of a specified station</a>
<br><br><hr>
</center>
<br>

<form action="details.php" method="get">
Show details of station: <input type="text" name="call" <?php if(isset($_GET['call'])) echo 'value="'.$_GET['call'].'"'; ?>>
<input type="submit" value="Show">
</form>
<br>
<?php

if(isset($_GET['call']) && ($_GET['call'] != ""))
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
			echo '<br><br><font color="blue"><b>Last position frame heard:</b> '.$posdate.' '.$postime.' (';
			$dc = time() - strtotime($posdate.' '.$postime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s ago)</font>';
			echo '<br><font color="red"><b>Station position: </b>'.$declat.', '.$declon.' - <b>'.$distance.' km '.$bearing.'° from your location</b></font>';
			echo '<br><font color="green"><b>Frame comment: </b>'.$comment.'</font>';
			echo '<br><br><b>Frame type:</b> ';
			if($mice) echo 'Mic-E compressed frame'; else echo 'Uncompressed frame';
			echo '<br><b>Station symbol:</b> '.$symboltab.$symbol;
			echo '<br><b>Frame path:</b> '.$scall.'>'.$lastpath;
		}
		
		if($otherframefound) 
		{			
			echo '<br><br><b>Last status frame heard:</b> '.$otherdate.' '.$othertime.' (';
			$dc = time() - strtotime($otherdate.' '.$othertime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s ago)';
			echo '<br><b>Status: </b>'.$status;
		}
		
		
	
	
	
	
	}

} 

}else {
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
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - szczegóły stacji</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br><br><br>

<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie i odebrane stacje</a> - <a href="stations.php">Informacje o stacjach</a> - <a href="frames.php">Pokaż ramki wybranej stacji</a> - <a href="details.php">Pokaż szczegóły wybranej stacji</a>
<br><br><hr>
</center>
<br>
<form action="details.php" method="get">
Pokaż szczegóły stacji: <input type="text" name="call" <?php if(isset($_GET['call'])) echo 'value="'.$_GET['call'].'"'; ?>>
<input type="submit" value="Pokaż">
</form>
<br>





<?php

if(isset($_GET['call']) && ($_GET['call'] != ""))
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
			echo '<br><br><font color="blue"><b>Ostatnia ramka pozycyjna odebrana:</b> '.$posdate.' '.$postime.' (';
			$dc = time() - strtotime($posdate.' '.$postime);
			echo (int)($dc / 86400).'d '.(int)(($dc % 86400) / 3600).'h '.(int)(($dc % 3600) / 60).'m '.(int)($dc % 60).'s temu)</font>';
			echo '<br><font color="red"><b>Pozycja stacji: </b>'.$declat.', '.$declon.' - <b>'.$distance.' km '.$bearing.'° od twojej lokalizacji</b></font>';
			echo '<br><font color="green"><b>Komentarz: </b>'.$comment.'</font>';
			echo '<br><br><b>Typ ramki:</b> ';
			if($mice) echo 'Ramka skompresowana Mic-E'; else echo 'Ramka nieskompresowana';
			echo '<br><b>Symbol stacji:</b> '.$symboltab.$symbol;
			echo '<br><b>Ścieżka ramki:</b> '.$scall.'>'.$lastpath;
		}
		
		if($otherframefound) 
		{			
			echo '<br><br><b>Ostatnia ramka statusowa odebrana:</b> '.$otherdate.' '.$othertime.' (';
			$dc = time() - strtotime($otherdate.' '.$othertime);
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