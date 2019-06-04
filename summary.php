<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com & Alfredo IZ7BOJ
You can modify this program, but please give a credit to original author. Program is free for non-commercial use only.
(C) Peter SQ8VPS & Alfredo IZ7BOJ 2017-2018

Version 1.2.2beta
*******************************************************************************************/
?>
<?php
include 'config.php';
include 'common.php';
include 'functions.php';

logexists();

session_start(); //start session
if(!isset($_SESSION['if'])) //if interface not defined
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
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="APRX statistics" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>APRX statistics - summary</title>
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
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - summary</h2> <a href="chgif.php?chgif=1">Change interface</a>
<br>
<br><b>Show:</b> <a href="summary.php">Summary (main)</a> - <a href="frames.php">RAW frames from specified station</a> - <a href="details.php">Details of a specified station</a><br><br>
<hr>
</center>
<br>
<?php
} else {
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX - podsumowanie</title>
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
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - podsumowanie</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br>
<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie (główna)</a> - <a href="frames.php">Surowe ramki wybranej stacji</a> - <a href="details.php">Szczegóły wybranej stacji</a><br><br>
<hr>
</center>
<br>
<?php
}

$lines = 0;
$rx = 0;
$tx = 0;
$is = 0;
$other = 0;
$receivedstations = array();
$time = 0;
$framespermin = 0;
$time1 = 0;
$time2 = 0;

$isservers = array();
$interfaces = array();

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



$logfile = file($logpath); //read log file
$linesinlog = count($logfile);
while ($lines < $linesinlog) { //read line by line
    $line = $logfile[$lines];
	stationparse($line);
	if((strpos($line, $callraw." R") !== false)OR(strpos($line, $callraw." d") !== false)) { //if it's the frame received by radio
		$rx++;
	} elseif(strpos($line, "APRSIS    R") !== false) //if it's from aprs-is
	{
		$is++;
	} elseif(strpos($line, $callraw." T") !== false) //if it's the frame transmitted on radio
	{
		$tx++;
	} else //other frames
	{
		$other++;
	}
		$lines++;
	
}

$unique = array_count_values($receivedstations);
if(empty($unique[1]))
{
	$unique[1] = 0;
}
array_multisort($receivedstations, SORT_DESC);

// System parameters reading
$sysver = NULL;
$kernelver = NULL;
$aprxver = NULL;
$cputemp = NULL;
$cpufreq = NULL;
$uptime = NULL;

$sysver = shell_exec ("cat /etc/os-release | grep PRETTY_NAME |cut -d '=' -f 2");
$kernelver = shell_exec ("uname -r");
$aprxver = shell_exec ("sudo aprx --v | grep version: | cut -d ':' -f 2");

// following command works only if aprx is installed using apt-get
//$aprxver = shell_exec ("apt-cache policy aprx | grep Installed | cut -d ':' -f 2");

if (file_exists ("/sys/class/thermal/thermal_zone0/temp")) {
    exec("cat /sys/class/thermal/thermal_zone0/temp", $cputemp);
    $cputemp = $cputemp[0] / 1000;
}

if (file_exists ("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq")) {
	exec("cat /sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq", $cpufreq);
	$cpufreq = $cpufreq[0] / 1000;
}

$uptime = shell_exec('uptime -p');

//part for reading from aprx.conf file	
//will be developed  
/*	
$lines2 = 0;	
$conffile = file($confpath); //read config file
$linesinconf = count($conffile); //get number of lines in aprx.conf
while ($lines2 < $linesinconf) { //read line by line
    $line = $conffile[$lines2];
	
	$xz = strpos($line, "server ");
	if($xz !== false)
	{
		for($cc = 0; $cc < strlen($line); $cc++)
		{
			if(($line[$cc] != " ") and ($line[$cc] != "#"))
			{
				$xx = explode(substr($line, $xz + 7), " ");
				array_push($servers, $xx[0]);
				break;
			} else if($line[$cc] == "#") break;
		}
		
	}
	
	$lines2++;
	
}
*/

$cyclesize = NULL;
$myloc = NULL;
$server = NULL;
$txactive = NULL;
$numradioint = NULL;
$digiactive = NULL;

//parse parameters in aprx.conf using shell commands and grep. Don't consider commented rows and blanks
$cyclesize = shell_exec ("cat $confpath |  grep '^[[:blank:]]*[^[:blank:]#]' | grep cycle-size | awk  '{print $2}'");
$myloc = shell_exec ("cat $confpath | grep '^[[:blank:]]*[^[:blank:]#]' | grep 'myloc' | grep -v 'beacon' | cut -d ' ' -f2-");
$server = shell_exec ("cat $confpath | grep '^[[:blank:]]*[^[:blank:]#]' | grep server |  awk  '{print $2}'");
$txactive = shell_exec ("cat $confpath | grep '^[[:blank:]]*[^[:blank:]#]' | grep tx-ok |  awk  '{print $2}'");
$numradioint = shell_exec ("cat $confpath | grep '^[[:blank:]]*[^[:blank:]#]' | grep ax25-device -c");
$digiactive = shell_exec ("cat $confpath | grep '^[[:blank:]]*[^[:blank:]#]' | grep '<digipeater>' -c");

//include custom info
if(file_exists('custom.php')) include 'custom.php';

if($lang == "en")
{
	echo "<br><b>Number of frames in log: </b>".($rx + $tx + $is + $other);
	echo "<br><b>Number of frames received on radio: </b>".$rx;
	echo "<br><b>Number of frames transmitted on radio: </b>".$tx;
	echo "<br><b>Number of frames received from APRS-IS: </b>".$is;
/*	if($logfile[$lines - 20] > 0)
	{
		load($logfile[$lines - 21], 0);
		load($logfile[$lines - 1], 1);
		echo "<br><b>Load (last 20 frames): </b>".number_format($framespermin, 2, '.', ',')." frames/min";
	}
*/
rxload();
echo "<br><b>RX Load (last 20 frames): </b>".number_format($rxframespermin, 2, '.', ',')." frames/min";
txload();
echo "<br><b>TX Load (last 20 frames): </b>".number_format($txframespermin, 2, '.', ',')." frames/min";
	?>


<br><br><hr><br>

<!-- <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
  <tbody> 
    <tr> 
      <td align="center"> -->
<table style="text-align: left; height: 116px; width: 600px;" border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td bgcolor="#ffd700" style="width: 600px;" colspan="2" rowspan="1"><span
      style="color: red; font-weight: bold;">SYSTEM STATUS</span></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>System Version: </b></td>
      <td style="width: 400px;"><?php echo $sysver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Kernel Version: </b></td>
      <td style="width: 400px;"><?php echo $kernelver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>APRX Version: </b></td>
      <td style="width: 400px;"><?php echo $aprxver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>System uptime: </b></td>
      <td style="width: 400px;"><?php echo $uptime ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>CPU temperature:</b></td>
      <td style="width: 400px;"><?php echo $cputemp ?> °C </td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>CPU frequency: </b></td>
      <td style="width: 400px;"><?php echo $cpufreq ?> MHz </td>
    </tr>
 </tbody>
</table>
<br>
<br>
<!-- </td> -->



<table style="text-align: left; height: 116px; width: 600px;" border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td bgcolor="#ffd700" style="width: 600px;" colspan="2" rowspan="1"><span style="color: red; font-weight: bold;">APRX CONFIG PARAMETERS</span></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Beacon Interval: </b></td>
      <td style="width: 400px;"><?php echo $cyclesize ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>APRS-IS server: </b></td>
      <td style="width: 400px;"><?php echo $server ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Location: </b></td>
      <td style="width: 400px;"><?php echo $myloc ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>TX active? </b></td>
      <td style="width: 400px;"><?php echo $txactive ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Number of radio ports:</b></td>
      <td style="width: 400px;"><?php echo $numradioint ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Digipeater active? </b></td>
      <td style="width: 400px;"><?php if ($digiactive == 1) echo "true" ?></td>
    </tr>
 </tbody>
</table>
<br>
<!-- </td>
</tr>
  </tbody>
</table> -->

<br><hr>

	<br><br> <form action="summary.php" method="GET">
	Show stations since last: 
	<select name="time">
	<option value="1" <?php if(isset($_GET['time'])&&($_GET['time'] == 1)) echo 'selected="selected"'?>>1 hour</option>
	<option value="2" <?php if(isset($_GET['time'])&&($_GET['time'] == 2)) echo 'selected="selected"'?>>2 hours</option>
	<option value="4" <?php if(isset($_GET['time'])&&($_GET['time'] == 4)) echo 'selected="selected"'?>>4 hours</option>
	<option value="6" <?php if(isset($_GET['time'])&&($_GET['time'] == 6)) echo 'selected="selected"'?>>6 hours</option>
	<option value="12" <?php if(isset($_GET['time'])&&($_GET['time'] == 12)) echo 'selected="selected"'?>>12 hours</option>
	<option value="24" <?php if(isset($_GET['time'])&&($_GET['time'] == 24)) echo 'selected="selected"'?>>1 day</option>
	<option value="48" <?php if(isset($_GET['time'])&&($_GET['time'] == 48)) echo 'selected="selected"'?>>2 days</option>
	<option value="168" <?php if(isset($_GET['time'])&&($_GET['time'] == 168)) echo 'selected="selected"'?>>week</option>
	<option value="720" <?php if(isset($_GET['time'])&&($_GET['time'] == 720)) echo 'selected="selected"'?>>30 days</option>
	<option value="e" <?php if(isset($_GET['time'])&&($_GET['time'] == 'e')) echo 'selected="selected"'?>>all</option>
</select>
<input type="submit" value="Refresh">
	<?php
	echo "<br><br><b>Stations received on radio (including $unique[1] unique stations):</b><br><br>";
	?>
	<table style="text-align: left; height: 116px; width: 600px;" border="1">
	<tbody>
	<tr>
	<td bgcolor="#ffd700"><b><font color="blue">Callsign</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Points</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Map show</font></b></td>
        <td bgcolor="#ffd700"><b><font color="blue">Raw packet</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Details</font></b></td>
	</tr>
	<?php
	while(list($c, $nm) = each($receivedstations))
	{
	?>
	<tr>
		<td bgcolor="silver"><b><?php echo $c ?></b></td>
		<td align="center"><?php echo $nm ?></td>
		<td><?php echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'">Show on aprs.fi</a>'?></td>
		<td><?php echo '<a target="_blank" href="frames.php?getcall='.$c.'">Show RAW Packets</a>'?></td>
		<td><?php echo '<a target="_blank" href="details.php?getcall='.$c.'">Show station details</a>' ?></td>
	</tr>
	<?php
	}
	?>
	</tbody>
        </table>
	<?php
} else { //polish
	echo "<br><b>Liczba ramek w logu: </b>".($rx + $tx + $is + $other);
	echo "<br><b>Liczba ramek odebranych przez radio: </b>".$rx;
	echo "<br><b>Liczba ramek nadanych przez radio: </b>".$tx;
	echo "<br><b>Liczba ramek odebranych z APRS-IS: </b>".$is;
	if($logfile[$lines - 20] > 0)
	{
		load($logfile[$lines - 21], 0);
		load($logfile[$lines - 1], 1);
		echo "<br><b>Obciążenie (ostatenie 20 ramek): </b>".number_format($framespermin, 2, '.', ',')." ramek/min";
	}
	?>

<br><br><hr><br>

<!-- <table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
  <tbody> 
    <tr> 
      <td align="center"> -->
<table style="text-align: left; height: 116px; width: 600px;" border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td bgcolor="#ffd700" style="width: 600px;" colspan="2" rowspan="1"><span
      style="color: red; font-weight: bold;">STATUS SYSTEMU</span></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Wersja systemu: </b></td>
      <td style="width: 400px;"><?php echo $sysver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Wersja jądra: </b></td>
      <td style="width: 400px;"><?php echo $kernelver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Wersja APRX: </b></td>
      <td style="width: 400px;"><?php echo $aprxver ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Czas działania: </b></td>
      <td style="width: 400px;"><?php echo $uptime ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Temperatura CPU:</b></td>
      <td style="width: 400px;"><?php echo $cputemp ?> °C </td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Częstotliwość CPU: </b></td>
      <td style="width: 400px;"><?php echo $cpufreq ?> MHz </td>
    </tr>
 </tbody>
</table>
<br>
<br>
<!-- </td> -->

<table style="text-align: left; height: 116px; width: 600px;" border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td bgcolor="#ffd700" style="width: 600px;" colspan="2" rowspan="1"><span style="color: red; font-weight: bold;">KONFIGURACJA APRX</span></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Interwał beaconów: </b></td>
      <td style="width: 400px;"><?php echo $cyclesize ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Serwer APRS-IS: </b></td>
      <td style="width: 400px;"><?php echo $server ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Lokalizacja: </b></td>
      <td style="width: 400px;"><?php echo $myloc ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Nadawanie aktywne? </b></td>
      <td style="width: 400px;"><?php echo $txactive ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Liczba portów radiowych:</b></td>
      <td style="width: 400px;"><?php echo $numradioint ?></td>
    </tr>
    <tr>
      <td bgcolor="silver" style="width: 200px;"><b>Digipeater włączony? </b></td>
      <td style="width: 400px;"><?php if ($digiactive == 1) echo "true" ?></td>
    </tr>
 </tbody>
</table>
<br>
<!-- </td>
</tr>
  </tbody>
</table> -->

<br><hr>

	<br><br> <form action="summary.php" method="GET">
	Pokaż stacje z ostatnich: 
	<select name="time">
	<option value="1" <?php if(isset($_GET['time'])&&($_GET['time'] == 1)) echo 'selected=\"selected\"'?>>1 godziny</option>
	<option value="2" <?php if(isset($_GET['time'])&&($_GET['time'] == 2)) echo 'selected=\"selected\"'?>>2 godzin</option>
	<option value="4" <?php if(isset($_GET['time'])&&($_GET['time'] == 4)) echo 'selected=\"selected\"'?>>4 godzin</option>
	<option value="6" <?php if(isset($_GET['time'])&&($_GET['time'] == 6)) echo 'selected=\"selected\"'?>>6 godzin</option>
	<option value="12" <?php if(isset($_GET['time'])&&($_GET['time'] == 12)) echo 'selected=\"selected\"'?>>12 godzin</option>
	<option value="24" <?php if(isset($_GET['time'])&&($_GET['time'] == 24)) echo 'selected=\"selected\"'?>>1 dnia</option>
	<option value="48" <?php if(isset($_GET['time'])&&($_GET['time'] == 48)) echo 'selected=\"selected\"'?>>2 dni</option>
	<option value="168" <?php if(isset($_GET['time'])&&($_GET['time'] == 168)) echo 'selected=\"selected\"'?>>tygodnia</option>
	<option value="720" <?php if(isset($_GET['time'])&&($_GET['time'] == 720)) echo 'selected=\"selected\"'?>>30 dni</option>
	<option value="e" <?php if(isset($_GET['time'])&&($_GET['time'] == 'e')) echo 'selected=\"selected\"'?>>wszystkie</option>
</select>
<input type="submit" value="Refresh">
	<?php
	echo "<br><br><b>Stacje odebrane przez radio (zawiera $unique[1] unikatowych stacji):</b><br><br>";
	?>
	<table style="text-align: left; height: 116px; width: 600px;" border="1">
	<tbody>
	<tr>
	<td bgcolor="#ffd700"><b><font color="blue">Znak</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Punkty</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Pokaż na mapie</font></b></td>
        <td bgcolor="#ffd700"><b><font color="blue">Surowe pakiety</font></b></td>
	<td bgcolor="#ffd700"><b><font color="blue">Szczegóły</font></b></td>
	</tr>
	<?php
	while(list($c, $nm) = each($receivedstations))
	{
	?>
	<tr>
		<td bgcolor="silver"><b><?php echo $c ?></b></td>
		<td align="center"><?php echo $nm ?></td>
		<td><?php echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'">Pokaż na aprs.fi</a>'?></td>
		<td><?php echo '<a target="_blank" href="frames.php?getcall='.$c.'">Pokaż surowe pakiety</a>'?></td>
		<td><?php echo '<a target="_blank" href="details.php?getcall='.$c.'">Pokaż szczegółby stacji</a>' ?></td>
	</tr>
	<?php
	}
	?>
	</tbody>
        </table>
	<?php
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
<br><hr><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>
<br>
</body>
</html>
