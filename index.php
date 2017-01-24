<?php
$znak = "SQ8VPS-3";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statyski APRX</title>
</head>
<body>
<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla stacji <font color="red"><b><?php echo $znak; ?></b></font></h2>
<br><br><br>
</center>

<?php
$pliklogu = fopen("/var/log/aprx/aprx-rf.log", "r"); //otworz plik
$ilerx = 0;
$ileis = 0;
$iletx = 0;
$ileinnych = 0;
if ($pliklogu) {
    while (($linia= fgets($pliklogu)) !== false) { //czytaj linia po linii
        if(strpos($linia, $znak."  R") !== false) { //jesli to ramka odebrana przez siec radiowa
			$ilerx++;
		} elseif(strpos($linia, "APRSIS") !== false) //a jesli z APRSIS
		{
			$ileis++;
		} elseif(strpos($linia, $znak."  T") !== false) //jesli to sa ramki nadane na radio
		{
			$iletx++;
		} else
		{
			$ileinnych++;
		}		
    }
    echo "<b>Wszystkich ramek w logu: </b>".($ileis + $ilerx + $iletx + $ileinnych);
	echo "<br><b>Ramek odebranych przez sieć radiową: </b>".$ilerx;
	echo "<br><b>Ramek nadanych przez sieć radiową: </b>".$iletx;
	echo "<br><b>Ramek odebranych z APRS-IS: </b>".$ileis;
	
	
	
	fclose($pliklogu);
} else 
{
	echo "Błąd otwierania pliku logu!";
} 
?>

</body>
</html>






