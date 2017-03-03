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
<?php
session_start();
if(!isset($_SESSION['if']))
{
	header('Refresh: 0; url=chgif.php?chgif=1');
	die();
}
$call = $_SESSION['call'];
$callraw = $_SESSION['if'];
$lang = $_SESSION['lang'];
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
<br>
<br><b>Show:</b> <a href="summary.php">Summary and received stations</a> - <a href="stations.php">Stations' informations</a> - <a href="frames.php">Show frames from specified station</a>
<br><br><hr>
</center>
	<br><br> <form action="stations.php" method="get">
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

<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie i odebrane stacje</a> - <a href="stations.php">Informacje o stacjach</a> - <a href="frames.php">Pokaż ramki wybranej stacji</a>
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

$logpath = "/var/log/aprx/aprx-rf.log";
$time = 0;
$staticstations = array();
$movingstations = array();
$otherstations = array();
$directstations = array();
$viastations = array();

if(!isset($_GET['time']) or ($_GET['time'] == ""))
{
	$time = time() - 3600;
} 
elseif($_GET['time'] == "e")
{
	$time = 0;
}
else
{
	$time = time() - ($_GET['time'] * 3600);
}

function stationparse($frame)
{
	global $staticstations;
	global $movingstations;
	global $otherstations;
	global $viastations;
	global $directstations;
	global $callraw;
	global $time;
	if(strpos($frame, $callraw." R")) //czy to jest ramka odebrana po radiu?
	{
		$uu = substr($frame, 0, 19);
		$uu = strtotime($uu) + date('Z');
		if($uu > $time)
		{
			$aa = explode(">", $frame); //odetnij wszystko za znakiem
			$stationcall = substr($aa[0], strpos($aa[0], $callraw." R ") + strlen($callraw." R ")); //obetnij linie do miejsca, gdzie jest znak
			$bb = substr($frame, 46); //na poczatek musimy uciac kawalek ramki z poczarku, zeby nie wystapil wczesniej dwukropek
			$bb = substr($bb, strpos($bb, ":") + 1); //bierzemy cala czesc ramki za znakiem ':' czyli separatorem sciezki od pola informacji
			if(($bb[0] === "@") or ($bb[0] === "!") or ($bb[0] === "=") or ($bb[0] === "/")) //jesli to jest ramka z pozycja
			{
				if($bb[7] === 'z')
				{
					if(in_array($bb[26], array('!', '#', '%', '&', '+', ',', '-', '.', '/', ':', ';', '?', '@', 'A', 'B', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'T', 'V', 'W', 'Z', '\\', ']', '_', '`', 'c', 'd', 'h', 'i', 'l', 'm', 'n', 'o', 'q', 'r', 't', 'w', 'x', 'y', 'z', '}')))
					{
						if(!in_array($stationcall, $staticstations))
						{
							$staticstations[] = $stationcall;
						}
					}	
					elseif(in_array($bb[26], array('$', '\'', '(', ')', '*', '<', '=', '>', 'C', 'F', 'P', 'R', 'U', 'X', 'Y', '[', '^', 'a', 'b', 'f', 'g', 'j', 'k', 'p', 's', 'u', 'v')))
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
				} else
				{	
					if(in_array($bb[19], array('!', '#', '%', '&', '+', ',', '-', '.', '/', ':', ';', '?', '@', 'A', 'B', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'T', 'V', 'W', 'Z', '\\', ']', '_', '`', 'c', 'd', 'h', 'i', 'l', 'm', 'n', 'o', 'q', 'r', 't', 'w', 'x', 'y', 'z', '}')))
					{
						if(!in_array($stationcall, $staticstations))
						{
							$staticstations[] = $stationcall;
						}
					}	
					elseif(in_array($bb[19], array('$', '\'', '(', ')', '*', '<', '=', '>', 'C', 'F', 'P', 'R', 'U', 'X', 'Y', '[', '^', 'a', 'b', 'f', 'g', 'j', 'k', 'p', 's', 'u', 'v')))
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
			}
			//czesc dla podzialu na stacje uslyszane posrednio i bezposrednio
			$cc = substr($frame, strpos($frame, ">")); //ucinamy na wszelki wypadek
			$cc = substr($cc, 0, strpos($cc, ":")); //bierzemy cala czesc ramki przed znakiem ':' czyli separatorem sciezki od pola informacji
			if(strpos($cc, '*') !== false) //jesli mamy gwiazdke, to ta ramka na pewno nie jest bezposrednio
			{
				if(!in_array($stationcall, $viastations))
				{
					$viastations[] = $stationcall;
				}
			} else //a jesli nie ma gwiazdki
			{
				$sppos = strpos($cc, "SP");
				if((strpos($cc, "SP") !== false) and ($cc[$sppos + 3] == "-")) //jesli jest tam SP, to moze to mimo wszystko byc powtorzone przez digi
				{
					if($cc[$sppos + 2] == $cc[$sppos + 4]) //jesli jest tam np. SP4-4 (czyli 2 takie same cyfry)
					{
						if(!in_array($stationcall, $directstations))
						{
							$directstations[] = $stationcall;
						}
					} else //a jesli jest tam np. SP3-2 to ramka jest powtorzona
					{
						if(!in_array($stationcall, $viastations))
						{
							$viastations[] = $stationcall;
						}
					}
				} else //a jesli nie ma SP, to na pewno jest bezposrednio
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
while ($lines < $linesinlog) { //read line by line
    $line = $logfile[$lines];
	stationparse($line);
	$lines++;
} 

asort($movingstations); //sortujemy alfabetycznie
	asort($staticstations);
	asort($otherstations);
	asort($viastations); //sortujemy alfabetycznie
	asort($directstations);
if($lang == "en")
{	
	echo "<br><br><hr /><br><font color=\"blue\"><b>Moving stations (<u>".count($movingstations)."</u>):</b></font> ";
	while(list($u, $o) = each($movingstations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Static stations (<u>".count($staticstations)."</u>):</b></font> ";
	while(list($u, $o) = each($staticstations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"green\"><b>Other stations (<u>".count($otherstations)."</u>):</b></font> ";
	while(list($u, $o) = each($otherstations))
	{
		echo $o.", ";
	}
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stations received indirectly (via digi) (<u>".count($viastations)."</u>):</b></font> ";
	while(list($u, $o) = each($viastations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stations received directly (<u>".count($directstations)."</u>):</b></font> ";
	while(list($u, $o) = each($directstations))
	{
		echo $o.", ";
	}
} else {
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje ruchome (<u>".count($movingstations)."</u>):</b></font> ";
	while(list($u, $o) = each($movingstations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje stałe (<u>".count($staticstations)."</u>):</b></font> ";
	while(list($u, $o) = each($staticstations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"green\"><b>Inne stacje (<u>".count($otherstations)."</u>):</b></font> ";
	while(list($u, $o) = each($otherstations))
	{
		echo $o.", ";
	}
	
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje odebrane pośrednio (przez digi) (<u>".count($viastations)."</u>):</b></font> ";
	while(list($u, $o) = each($viastations))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje odebrane bezpośrednio (<u>".count($directstations)."</u>):</b></font> ";
	while(list($u, $o) = each($directstations))
	{
		echo $o.", ";
	}
}
?>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center>(C) 2017 Peter SQ8VPS</center>
</body>
</html>