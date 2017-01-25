<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statyski APRX</title>
</head>
<body>
<?php
if((!isset($_GET['if'])) or (isset($_GET['if']) and ($_GET['if'] == ""))) //jesli nie zostal wybrany interfejsu
{
?>
<form action="index.php" method="get">
Podaj znak interfejsu: <input type="text" name="if">
<input type="submit" value="OK">
</form>
<?php
} else
{
	$znak = $_GET['if'];
?>

<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $znak; ?></b></font></h2>
<br><br><br>
</center>

<?php
function stacjaparse($ramka)
{
	global $stacjeodebrane;
	global $znak;
	if(strpos($ramka, $znak."  R")) //czy to jest ramka odebrana po radiu?
	{
		$aa = explode(">", $ramka); //odetnij wszystko za znakiem
		$znakstacji = substr($aa[0], 36); //obetnij linie do miejsca, gdzie jest znak TODO: chyba nie zawsze działa, bo nie zawsze 36
		if(array_key_exists($znakstacji, $stacjeodebrane)) //jesli ten znak juz jest na liscie stacji
		{
			$stacjeodebrane[$znakstacji]++; //zwiekszamy ilosc ramek od danej stacji
		} else //a jesli tego znaku jeszcze nie mamy
		{
			$stacjeodebrane[$znakstacji] = 1; //dodajemy go do listy
		}
	}
} 

$pliklogu = fopen("/var/log/aprx/test.log", "r"); //otworz plik
$stacjeodebrane = array();
if ($pliklogu) {
    $ileis = $ilerx = $iletx = $ileinnych = 0;
	while (($linia = fgets($pliklogu)) !== false) { //czytaj linia po linii
        stacjaparse($linia);
		if(strpos($linia, $znak."  R") !== false) { //jesli to ramka odebrana przez siec radiowa
			$ilerx++;
		} elseif(strpos($linia, "APRSIS") !== false) //a jesli z APRSIS
		{
			$ileis++;
		} elseif(strpos($linia, $znak."  T") !== false) //jesli to sa ramki nadane na radio
		{
			$iletx++;
		} else //inne ramki
		{
			$ileinnych++;
		}		
    }
    echo "<b>Wszystkich ramek w logu: </b>".($ileis + $ilerx + $iletx + $ileinnych);
	echo "<br><b>Ramek odebranych przez sieć radiową: </b>".$ilerx;
	echo "<br><b>Ramek nadanych przez sieć radiową: </b>".$iletx;
	echo "<br><b>Ramek odebranych z APRS-IS: </b>".$ileis;
	echo "<br><br><b>Stacje odebrane drogą radiową:</b><br><br>";
	echo "<pre><font color=\"blue\"><b>Znak\t&nbsp;&nbsp;&nbsp;&nbsp;Punkty</b></font><br>";
	array_multisort($stacjeodebrane, SORT_DESC); //sortujemy malejaco
	while(list($z, $il) = each($stacjeodebrane))
	{
		echo $z;
		$spacje = 12 - strlen($z);
		for($i = 0; $i < $spacje; $i++)
		{
			echo '&nbsp;';
		}
		echo $il;
		echo '<br>';
	}
	fclose($pliklogu);
} else 
{
	echo "Błąd otwierania pliku logu!";
}


?>

</body>
</html>
<?php
}

?>





