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

if((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) //if interface was not selected
{
	if(($static_if == 1) && ($static_call != ""))
	{
		session_start();
		
		$callsign = strtoupper($static_call); //uppercase the static callsign
		$_SESSION['call'] = $callsign;
	
		$callsignraw = $callsign;
		while(strlen($callsignraw) < 9)
		{
				$callsignraw .= " "; //add spaces to raw callsign
		}
		$_SESSION['if'] = $callsignraw;
		if($static_lang == "en")
		{
			$_SESSION['lang'] = "en";
		} else if($static_lang == "pl")
		{
			$_SESSION['lang'] == "pl";
		} else
		{
			$_SESSION['lang'] = "en";
		}
		
		header('Refresh: 0; url=summary.php');
	}
	else
	{
		header('Refresh: 0; url=chgif.php?chgif=1');
	}
	die();
} else { //else if inteface selected
	header('Refresh: 0; url=summary.php');
	die();
}
?>