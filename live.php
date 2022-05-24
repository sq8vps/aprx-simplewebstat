<?php
include 'config.php';
include 'common.php';

logexists();

if (isset($_GET['ajax'])) {
  session_start();
  $handle = fopen($logpath, 'r'); //open log
  if (isset($_SESSION['offset'])) { //this part is executed from 2nd cycle
    $rawdata = stream_get_contents($handle, -1, $_SESSION['offset']); //open stream
		if ($rawdata !== "")  { //only if last cycle got something, process new data, otherwise skip to next cycle
			$_SESSION['offset'] += strlen($rawdata); //update offset
			$rows=explode("\n", $rawdata, -1); //if more rows are received in the same cycle, divide it. -1 is necessary because last element would be empty
			foreach($rows as $row) {
				color($row);
				echo $rowc;
				} //close foreach
			} //close  if ($rawdata !== "")
	} else { //only at the beginning, print last rows
		$log=file($logpath);
    $rows=count($log)-1 -$startrows;
    $counter=1;
    while ($counter<=$startrows) {
			$row=$log[$rows+$counter];
			color($row);
			echo $rowc;
			$counter++;
      } //close while
    fseek($handle, 0, SEEK_END); //put the handle at the end of log
    $_SESSION['offset'] = ftell($handle);
  } //close else of if (isset($_SESSION['offset']))
  exit();
} //close if (isset($_GET['ajax']))

function color($row) {
  global $rowc;
	global $refresh;
	global $logpath;
	global $timestampcolor;
	global $APRSIScolor;
	global $RFcolor;
	global $TXcolor;
	global $RXcolor;
	global $pathcolor;

	$timestamp=substr($row, 0, 24);  //take only the part of the line, where date and time is
  	$int=substr($row,24,10); //cut interface
  	$txrx=substr($row,34,2);// cut rx-tx indicator
	$body=explode(":",substr($row,36),2);
	$path=$body[0];
	$comment=$body[1];

  	$timestampc="<span style='color:".$timestampcolor."'>".$timestamp."</span>";
  	if (strpos($int,"APRSIS")!==false) {
		$intc="<span style='color:".$APRSIScolor."'>".$int.str_repeat("&nbsp",3)."</span>";
		}
  	else {
		$intc="<span style='color:".$RFcolor."'>".$int.str_repeat("&nbsp",10 - strlen($int))."</span>";
		}
	if (strpos($txrx,"T")!==false) {
		$txrx="<span style='color:".$TXcolor."'>TX"."&nbsp</span>";
		}
	else  {
		$txrx="<span style='color:".$RXcolor."'>RX"."&nbsp </span>";
		}
	$path="<span style='color:".$pathcolor."'>".$path."</span>";
	
        if ((strpos($int,$_SESSION['if'])!==false) or (strpos($int,"APRSIS")!==false))  {
	        $rowc=$timestampc." ".$intc." ".$txrx." ".$path.":".$comment."<br>";
        	}
	else {
		$rowc=""; //don't print traffic from other radio interfaces than the selected
		}
} //close function

?>

<!doctype html> <html lang="en"> <head>
  <meta charset="UTF-8">
  <script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
  <script src="http://creativecouple.github.io/jquery-timing/jquery-timing.min.js"></script>
  <script>
  $(function() {
    $.repeat(<?php echo $refresh ?>, function() {
      $.get('live.php?ajax', function(rawdata) {
        $('#tail').append(rawdata);
      });
    });
  });
  </script> </head> <body>
  <div id="tail"><i>Real time traffic monitor - Starting up...</i><br><br>
<b>TIMESTAMP<?php echo str_repeat("&nbsp",17) ?>INTERF <?php echo str_repeat("&nbsp",3) ?>R/T<?php echo str_repeat("&nbsp",2)?>PATH:BODY</b>
<br></div> </body>
</html>
