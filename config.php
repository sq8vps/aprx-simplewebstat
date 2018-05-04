<?php
/********************************************************
This is a configuration file for APRX SimpleWebstat.
You can set here a path to the log file or set static interface callsign and language.
********************************************************/
$logpath = "/var/log/aprx/aprx-rf.log"; //full path to the aprx-rf.log file
//$confpath = "aprx.conf"; //full path to the aprx.conf file - DO NOT SET
$cntalias = "SP"; //your national APRS untraced digipeater alias (helps checking, if frame was received directly or not), for ex. SP is in Poland, HU is in Hungary. If you don't know, what is it, you can leave it blank.


//interface callsign also can be changed temporarily from the website, but this option provides automatic choose of the interface, instead of choosing it by hand via website every time
$static_if = 0; //1 to enable using static interface callsign
$static_call = "N0CALL-11"; //interface callsign to be set as default, with SSID
$static_lang = "en"; //language to be set by default (it can be changed temporarily via website): en - English, pl - Polski

//station posistion data for calculating distance from received station in details.php 
$stationlat = 00.000000; //station latitude in decimal degrees
$stationlon = 00.000000;  //station longtitude in decimal degrees
?>