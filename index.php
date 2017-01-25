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
if((!isset($_GET['if'])) or (isset($_GET['if']) and ($_GET['if'] == ""))) //jesli nie zostal wybrany interfejs
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
	$ileis = $ilerx = $iletx = $ileinnych = 0;
	$czas1 = 0;
	$czas2 = 0;
	$ramkinamin = 0.00;
?>

<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $znak; ?></b></font></h2> <a href="index.php">Zmień interfejs</a>
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

function obciazenie($ramka, $koniec)
{
	global $ileis, $ilerx, $iletx, $ileinnych, $czas1, $czas2, $ramkinamin;
	if($koniec === 0)
	{
		$czas1 = substr($ramka, 0, 19);
		$czas1 = strtotime($czas1);
	} elseif($koniec === 1)
	{
		$czas2 = substr($ramka, 0, 19);
		$czas2 = strtotime($czas2);
		$ramkinamin = 20 / (($czas2 - $czas1) / 60);
	}
	
}
$adrespliku = "/var/log/aprx/test.log";
$stacjeodebrane = array();
$ilelinii = 0;
$plik = array();

$plik = file($adrespliku); //odczytujemy plik
$liniiwpliku = count($plik);
while ($ilelinii < $liniiwpliku) { //czytaj linia po linii
    $linia = $plik[$ilelinii];
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
		$ilelinii++;
}
echo "<b>Wszystkich ramek w logu: </b>".($ileis + $ilerx + $iletx + $ileinnych);
echo "<br><b>Ramek odebranych przez sieć radiową: </b>".$ilerx;
echo "<br><b>Ramek nadanych przez sieć radiową: </b>".$iletx;
echo "<br><b>Ramek odebranych z APRS-IS: </b>".$ileis;
if($plik[$ilelinii - 20] > 0)
{
	obciazenie($plik[$ilelinii - 21], 0);
	obciazenie($plik[$ilelinii - 1], 1);
	echo "<br><b>Obciążenie (ostatnie 20 ramek): </b>".number_format($ramkinamin, 2, '.', ',')." ramek/min";
}
$unikalne = array_count_values($stacjeodebrane); //ilosc wystapien kazdej z wartosci, ale uzyjemy tego tylko dla stacji unikalnych
echo "<br><br><b>Stacje odebrane drogą radiową (w tym $unikalne[1] unikalnych):</b><br><br>";
echo "<pre><font color=\"blue\"><b>Znak\t&nbsp;&nbsp;&nbsp;&nbsp;Punkty</b></font><br>";
array_multisort($stacjeodebrane, SORT_DESC); //sortujemy malejaco
while(list($z, $il) = each($stacjeodebrane))
{
	echo $z; //znak 
	$spacje = 12 - strlen($z); //sprawdzamy ile musimy miec spacji, aby wszystko bylo rowno
	for($i = 0; $i < $spacje; $i++) //i dodajemy te spacje
	{
		echo '&nbsp;';
	}
	echo $il; //ile razy znak byl uslyszany
	echo '<br>';
}


?>

</body>
</html>
<?php
}
?>





