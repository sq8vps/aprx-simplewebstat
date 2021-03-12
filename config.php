<?php
/********************************************************
This is a configuration file for APRX SimpleWebstat.
You can set here a path to the log file or set static interface callsign and language.
********************************************************/
$logpath = "aprx-rf.log"; //full path to the aprx-rf.log file
$confpath = "aprx.conf"; //full path to the aprx.conf file
$cntalias = "IT"; //your national APRS untraced digipeater alias (helps checking, if frame was received directly or not), for ex. SP is in Poland, HU is in Hungary. If you don't know, what is it, you can leave it blank.

//interface callsigns. Insert callisgns between "" and separated by commas 
$interfaces=array("IZ7BOJ-11","IZ7BOJ-12");

//interface descriptions. Insert strings between "" and separated by commas
$intdesc=array("144.800  VHF Port","432.500 UHF Port");

//interface callsign also can be changed temporarily from the website, but this option provides automatic choose of the interface, instead of choosing it by hand via website every time
$static_if = 1; //1 to enable using static interface callsign
$static_call = "IZ7BOJ-11"; //interface callsign to be set as default, with SSID
$static_lang = "en"; //language to be set by default (it can be changed temporarily via website): en - English, pl - Polski

//station posistion data for calculating distance from received station in details.php 
$stationlat = 41.248027; //station latitude in decimal degrees
$stationlon = 16.421246;  //station longtitude in decimal degrees

//logo path,with file name
$logourl="aprslogo.png";

//AX.25 realtime traffic watch config
$refresh=1000; //refresh time in ms. Don't go below 1000
$timestampcolor="silver"; //color of timestamp
$APRSIScolor="orange"; //color of APRSIS interface
$RFcolor="blue"; //color of RF interface
$TXcolor="red"; //color of TX indicator
$RXcolor="green"; //color of RX indicator
$pathcolor="purple"; //color of path string
$startrows=5; //number of last rows displayed at session opening
?>
