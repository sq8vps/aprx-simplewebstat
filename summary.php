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
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="APRX statistics" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>APRX statistics - summary</title>
</head>
<body>
<center><font size="20"><b>APRX statistics</b></font>
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - summary</h2> <a href="chgif.php?chgif=1">Change interface</a>
<br><br><br>
</center>
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
<title>Statystyki APRX - podsumowanie</title>
</head>
<body>
<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - podsumowanie</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br><br><br>
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

if(!isset($_GET['time']) or ($_GET['time'] == ""))
{
	$_GET['time'] = 1;
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
	global $receivedstations;
	global $callraw;
	global $time;
	if(strpos($frame, $callraw." R")) //is this a frame received by radio?
	{
		$uu = substr($frame, 0, 19);
		$uu = strtotime($uu) + date('Z');
		if($uu > $time)
		{
			$aa = explode(">", $frame); //cut everything after station's callsign
			$stationcall = substr($aa[0], strpos($aa[0], $callraw." R ") + strlen($callraw." R ")); //cut everything until station's callsign
			if(array_key_exists($stationcall, $receivedstations)) //if this callsign is already on stations list
			{
				$receivedstations[$stationcall]++; //increment the number of frames from this station
			} else //if this callsign is not on the list
			{
				$receivedstations[$stationcall] = 1; //add callsign to the list
			}
		}
		return 0;
	}
	
} 

function load($frame, $end)
{
	global $framespermin;
	global $time1;
	global $time2;
	if($end === 0)
	{
		$time1 = substr($frame, 0, 19);
		$time1 = strtotime($time1);
	} elseif($end === 1)
	{
		$time2 = substr($frame, 0, 19);
		$time2 = strtotime($time2);
		$framespermin = 20 / (($time2 - $time1) / 60);
	}
	
}

$logfile = file($logpath); //read log file
$linesinlog = count($logfile);
while ($lines < $linesinlog) { //read line by line
    $line = $logfile[$lines];
	stationparse($line);
	if(strpos($line, $callraw." R") !== false) { //if it's the frame received by radio
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

$sysver = NULL;
$kernelver = NULL;
$aprxver = NULL;
$cputemp = NULL;
$cpufreq = NULL;
$uptime = NULL;

$sysver = shell_exec ("cat /etc/os-release | grep PRETTY_NAME |cut -d '=' -f 2");
$kernelver = shell_exec ("uname -r");

// following command works only if aprx is installed using apt-get
//$aprxver = shell_exec ("aprx --v | grep version: | cut -d ':' -f 2");

$aprxver = shell_exec ("apt-cache policy aprx | grep Installed | cut -d ':' -f 2");

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

if($lang == "en")
{
	echo "<b>System uptime: </b>".$uptime;
	echo "<br><b>CPU temperature: </b>".$cputemp." °C";
	echo "<br><b>CPU frequency: </b>".$cpufreq." MHz";
	echo "<br><b>Number of frames in log: </b>".($rx + $tx + $is + $other);
	echo "<br><b>Number of frames received on radio: </b>".$rx;
	echo "<br><b>Number of frames transmitted on radio: </b>".$tx;
	echo "<br><b>Number of frames received from APRS-IS: </b>".$is;
	if($logfile[$lines - 20] > 0)
	{
		load($logfile[$lines - 21], 0);
		load($logfile[$lines - 1], 1);
		echo "<br><b>Load (last 20 frames): </b>".number_format($framespermin, 2, '.', ',')." frames/min";
	}
	?>
	<br>
<br><b>Show:</b> <a href="summary.php">Summary and received stations</a> - <a href="stations.php">Stations' informations</a> - <a href="frames.php">Show frames from specified station</a> - <a href="details.php">Show details of a specified station</a>
	<br><br><hr>
	<br><br> <form action="summary.php" method="get">
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
	<?php
	echo "<br><br><b>Stations received on radio (including $unique[1] unique stations):</b><br>";
	echo "<br><pre><font color=\"blue\"><b>Callsign&nbsp;&nbsp;&nbsp;&nbsp;Points</b></font><br>";
	while(list($c, $nm) = each($receivedstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'">'.$c.'</a>'; //callsign
		$spaces = 12 - strlen($c);
		for($i = 0; $i < $spaces; $i++) 
		{
			echo '&nbsp;';
		}
		echo $nm;
		echo '<br>';
	}
} else {
	echo "<b>Czas działania systemu: </b>".$uptime;
	echo "<br><b>Temperatura CPU: </b>".$cputemp." °C";
	echo "<br><b>Częstotliwość CPU: </b>".$cpufreq." MHz";
	echo "<br><b>Wszystkich ramek w logu: </b>".($rx + $tx + $other + $is);
	echo "<br><b>Ramek odebranych przez sieć radiową: </b>".$rx;
	echo "<br><b>Ramek nadanych przez sieć radiową: </b>".$tx;
	echo "<br><b>Ramek odebranych z APRS-IS: </b>".$is;
	if($logfile[$lines - 20] > 0)
	{
		load($logfile[$lines - 21], 0);
		load($logfile[$lines - 1], 1);
		echo "<br><b>Obciążenie (ostatnie 20 ramek): </b>".number_format($framespermin, 2, '.', ',')." ramek/min";
	}
	?>
	<br>
<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie i odebrane stacje</a> - <a href="stations.php">Informacje o stacjach</a> - <a href="frames.php">Pokaż ramki wybranej stacji</a> - <a href="details.php">Pokaż szczegóły wybranej stacji</a>
	<br><br><hr>
	<br><br> <form action="summary.php" method="get">
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
	<?php
	echo "<br><br><b>Stacje odebrane drogą radiową (w tym $unique[1] unikalnych):</b><br>";
	echo "<br><pre><font color=\"blue\"><b>Znak\t&nbsp;&nbsp;&nbsp;&nbsp;Punkty</b></font><br>";
	while(list($c, $nm) = each($receivedstations))
	{
		echo '<a target="_blank" href="https://aprs.fi/?call='.$c.'">'.$c.'</a>'; //callsign
		$spaces = 12 - strlen($c);
		for($i = 0; $i < $spaces; $i++) 
		{
			echo '&nbsp;';
		}
		echo $nm;
		echo '<br>';
	}
}

?>

<html>
<head>
  <meta content="text/html; charset=ISO-8859-1"
 http-equiv="content-type">
  <title></title>
</head>
<body>
<table style="text-align: left; width: 100%;" border="1"
 cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td align="center">
<table style="text-align: left; height: 116px; width: 500px;"
 border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td style="width: 566px;" colspan="2" rowspan="1"><span
      style="color: red; font-weight: bold;">SYSTEM STATUS</span></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>System Version: </b></td>
      <td style="width: 566px;"><?php echo $sysver ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>Kernel Version: </b></td>
      <td style="width: 566px;"><?php echo $kernelver ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>APRX Version: </b></td>
      <td style="width: 566px;"><?php echo $aprxver ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>System uptime: </b></td>
      <td style="width: 566px;"><?php echo $uptime ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>CPU temperature:</b></td>
      <td style="width: 566px;"><?php echo $cputemp ?> °C </td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>CPU frequency: </b></td>
      <td style="width: 566px;"><?php echo $cpufreq ?> MHz </td>
    </tr>
 </tbody>
</table>
<br>
</body>
</td>

<body>
<td align="center">
<table style="text-align: left; height: 116px; width: 500px;"
 border="1" cellpadding="2" cellspacing="2">
  <tbody>
    <tr align="center">
      <td style="width: 566px;" colspan="2" rowspan="1"><span
      style="color: red; font-weight: bold;">APRX CONFIG PARAMETERS</span></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>Beacon Interval: </b></td>
      <td style="width: 566px;"><?php echo $cyclesize ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>APRS-IS server: </b></td>
      <td style="width: 566px;"><?php echo $server ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>Location: </b></td>
      <td style="width: 566px;"><?php echo $myloc ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>TX active? </b></td>
      <td style="width: 566px;"><?php echo $txactive ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>Number of radio ports:</b></td>
      <td style="width: 566px;"><?php echo $numradioint ?></td>
    </tr>
    <tr>
      <td style="width: 168px;"><b>Digipeater active? </b></td>
      <td style="width: 566px;"><?php if ($digiactive == 1) echo "true" ?></td>
    </tr>
 </tbody>
</table>
<br>
</body>
</td>
</tr>
  </tbody>
</table>

</html>	
	
</pre>
<br><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>
</body>
</html>
