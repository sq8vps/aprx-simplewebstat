<?php
$asw_version = "1.3.0beta";
include 'config.php';

function logexists()
{
	global $logpath;
	//global $confpath;
	global $asw_version;
	if(!file_exists($logpath))
	{
		echo '<font color="red" size="6"><b>Error. Cannot open APRX log file at '.$logpath.'.</b></font>';
		echo '<br><br>Please check, if log file path in config.php is set correctly.<br>Plase check, if file '.$logpath.' exists.';
		echo '<br><br><b>Pointless to continue.</b>';
		echo '<br><br><br><br><br><br><center>APRX Simple Webstat version '.$asw_version.' by Peter SQ8VPS and Alfredo IZ7BOJ 2017-2019</center>';
		die();
	}
	/*if(!file_exists($confpath))
	{
		echo '<font color="red" size="6"><b>Error. Cannot open APRX config file at '.$confpath.'.</b></font>';
		echo '<br><br>Please check, if config file path in config.php is set correctly.<br>Plase check, if file '.$logpath.' exists.';
		echo '<br><br><b>Pointless to continue.</b>';
		echo '<br><br><br><br><br><br><center>APRX Simple Webstat version '.$asw_version.' by Peter SQ8VPS and Alfredo IZ7BOJ 2017-2018</center>';
		die();
	}*/
}
?>