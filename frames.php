
<?php
/******************************************************************************************
This file is a part of SIMPLE WEB STATICSTICS GENERATOR FROM APRX LOG FILE
It's very simple and small APRX statictics generator in PHP. It's parted to smaller files and they will work independent from each other (but you always need chgif.php).
This script may have a lot of bugs, problems and it's written in very non-efficient way without a lot of good programming rules. But it works for me.
Author: Peter SQ8VPS, sq8vps[--at--]gmail.com & Alfredo IZ7BOJ
You can modify this program, but please give a credit to original author. Program is free for non-commercial use only.
(C) Peter SQ8VPS & Alfredo IZ7BOJ 2017-2018

Version 1.3.1beta
*******************************************************************************************/
?>
<?php
include 'config.php';
include 'common.php';

logexists();

session_start();
if(!isset($_SESSION['if']))
{
        header('Refresh: 0; url=chgif.php?chgif=1');
        die();
}
$call = $_SESSION['call'];
$callraw = $_SESSION['if'];
$lang = $_SESSION['lang'];
$frames = array();

if(isset($_GET['getcall']) and ($_GET['getcall'] !== ""))
{
        global $logpath;
        $scall = strtoupper($_GET['getcall']);
        $linesinlog = 0;
        global $callraw;
        echo '<br>';
        $logfile = file($logpath); //read log file
        $linesinlog = count($logfile);
        $b = 0;
//parse line by line
        while($b < $linesinlog)
        {
                $line = $logfile[$b];
                if(strpos($line, $callraw." R "))
                {
                        $statcall = explode(">", $line); //odetnij wszystko za znakiem
                        $statcall = substr($statcall[0], strpos($statcall[0], $callraw." R ") + strlen($callraw." R ")); //obetnij linie do miejsca, gdzie jest znak
                        if($statcall == $scall)
                        {
                                $frames[] = str_replace($callraw." R ", "&nbsp;&nbsp;&nbsp;", $line);
                        }
                }

                if(strpos($line, $callraw." d "))
                {
                        $statcall = explode(">", $line);
                        $statcall = substr($statcall[0], strpos($statcall[0], $callraw." d *") + strlen($callraw." d *"));
                        if($statcall == $scall)
                        {
                                $frames[] = str_replace($callraw." d *", "&nbsp;&nbsp;&nbsp;", $line);
                        }
                }
                $b++;
        }//close while

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
<title>APRX statistics - frames search</title>
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
<h2>for interface <font color="red"><b><?php echo $call; ?></b></font> - search frames</h2> <a href="chgif.php?chgif=1">Change interface</a>
<br>
<br><b>Show:</b> <a href="summary.php">Summary (main)</a> - <a href="frames.php">RAW frames from specified station</a> - <a href="details.php">Details of a specified station</a><br><br>
<hr>
</center>
<br>
<form action="frames.php" method="get">
        Show all frames from the callsign: <input type="text" name="getcall" <?php if(isset($_GET['getcall'])) echo "value=\"".$_GET['getcall']."\""; ?>>
        <input type="submit" value="Show">
</form>
<?php
        echo "<font color=\"red\"><b>Found ".count($frames)." frames from station $scall</b></font><br><br>";
        array_multisort($frames, SORT_DESC);
        for($o = 0; $o < count($frames); $o++)
        {
                echo $frames[$o]."<br>";
        }
}//close if english
else {
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX - szukaj ramek</title>
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
<h2>dla interfejsu <font color="red"><b><?php echo $call; ?></b></font> - szukaj ramek</h2> <a href="chgif.php?chgif=1">Zmień interfejs</a>
<br>
<br><b>Pokaż:</b> <a href="summary.php">Podsumowanie (główna)</a> - <a href="frames.php">Surowe ramki wybranej stacji</a> - <a href="details.php">Szczegóły wybranej stacji</a><br><br>
<hr>
</center>
<br>
<form action="frames.php" method="get">
        Pokaż wszystkie ramki od znaku: <input type="text" name="getcall" <?php if(isset($_GET['getcall'])) echo "value=\"".$_GET['getcall']."\""; ?>>
        <input type="submit" value="Pokaż">
</form>

<?php
        echo "<font color=\"red\"><b>Znaleziono ".count($frames)." ramek od stacji $scall</b></font><br><br>";
        array_multisort($frames, SORT_DESC);
        for($o = 0; $o < count($frames); $o++)
        {
                echo $frames[$o]."<br>";
        }
} //close else language
} //close if issset GET['call'] 
?>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<center><a href="https://github.com/sq8vps/aprx-simplewebstat" target="_blank">APRX Simple Webstat version <?php echo $asw_version; ?></a> by Peter SQ8VPS and Alfredo IZ7BOJ</center>
</body>
</html>
