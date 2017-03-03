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
if(isset($_SESSION['if']))
{
	header('Refresh: 0; url=summary.php');
	die();
} else {
	header('Refresh: 0; url=chgif.php?chgif=1');
	die();
}

?>