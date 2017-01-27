<?php
/*********************************************************************************************************************
PHP statistics generator from APRX log data file (aprx-rf.log)
Version: 0.1.0 beta
It may be very unstable. It was written in very non-efficient way without a lot of good programming rules. And I'm still learning PHP.
Author: Piotr SQ8VPS
You can modify this code, publish it, but please give a credit to original author. Only for non-commercial use.

Generator statystyk PHP z pliku logu APRX (aprx-rf.log)
Wersja 0.1.0 beta
Program może być bardzo niestabilny. Jest póki co kompletnie niezoptymalizowany, napisany w wielu miejscach bez poszanowania zasad dobrego programowania. A ja wciąż uczę się PHP.
Autor: Piotr SQ8VPS
Możesz modyfikować ten kod i udostępniać go, ale musisz wskazać autora oryginalneg kodu. Tylko do uzytku niekomercyjnego.
**********************************************************************************************************************/

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Description" content="Statystyki APRX" />
<meta name="Keywords" content="" />
<meta name="Author" content="SQ8VPS" />
<title>Statystyki APRX</title>
</head>
<body>
<?php
session_start();
if((((!isset($_SESSION['if'])) or (isset($_SESSION['if']) and ($_SESSION['if'] == ""))) and ((!isset($_GET['if'])) or (isset($_GET['if']) and ($_GET['if'] == "")))) or $_GET['chgif'] == "1") //jesli nie zostal wybrany interfejs
{
	session_destroy();
?>
<form action="index.php" method="get">
Podaj znak interfejsu: <input type="text" name="if">
<input type="submit" value="OK">
<br>"APRSIS" nie jest poprawnym adresem interfejsu i wyswietlone dane nie będą zgodne z rzeczywistością. Wpisz cokolwiek innego.
</form>
<?php
} else
{
	if(!isset($_SESSION['if']))
	{
		$znak = strtoupper($_GET['if']);
		$_SESSION['if'] = $znak;
	}
	$znak = $_SESSION['if'];
	$znakraw = $znak;
	while(strlen($znakraw) < 9)
	{
			$znakraw .= " ";
	}
	$ileis = $ilerx = $iletx = $ileinnych = 0;
	$czas1 = 0;
	$czas2 = 0;
	$ramkinamin = 0;
	$zczasu = 0;
	if((!isset($_GET['dane']) or (($_GET['dane'] !== "ilosc") and ($_GET['dane'] !== "rs") and ($_GET['dane'] !== "szukaj"))) and (!isset($_SESSION['typ'])))
	{
		$dane =  "ilosc";
	} elseif(isset($_GET['dane']))
	{
		$dane = $_GET['dane'];
	} else
	{
		$dane = $_SESSION['typ'];
	}
	if(isset($_GET['dane']))
	{
		$_SESSION['typ'] = $_GET['dane'];
	}
?>

<center><font size="20"><b>Statystyki APRX</b></font>
<h2>dla interfejsu <font color="red"><b><?php echo $znak; ?></b></font></h2> <a href="index.php?chgif=1">Zmień interfejs</a>
<br><br><br>
</center>

<?php
function stacjaparse($ramka)
{
	global $stacjeodebrane;
	global $stacjestale;
	global $stacjeruchome;
	global $stacjeinne;
	global $znak;
	global $stacjeposr;
	global $stacjebezposr;
	global $znakraw;
	global $zczasu;
	if(strpos($ramka, $znakraw." R")) //czy to jest ramka odebrana po radiu?
	{
		$uu = substr($ramka, 0, 19);
		$uu = strtotime($uu) + 3600;
		if($uu > $zczasu)
		{
			//czesc dla liczenia ilosci ramek od stacji
			$aa = explode(">", $ramka); //odetnij wszystko za znakiem
			$znakstacji = substr($aa[0], strpos($aa[0], $znakraw." R ") + strlen($znakraw." R ")); //obetnij linie do miejsca, gdzie jest znak
			if(array_key_exists($znakstacji, $stacjeodebrane)) //jesli ten znak juz jest na liscie stacji
			{
				$stacjeodebrane[$znakstacji]++; //zwiekszamy ilosc ramek od danej stacji
			} else //a jesli tego znaku jeszcze nie mamy
			{
				$stacjeodebrane[$znakstacji] = 1; //dodajemy go do listy
			}
			//czesc dla podzialu na stacje ruchome i stale
			$bb = substr($ramka, 46); //na poczatek musimy uciac kawalek ramki z poczarku, zeby nie wystapil wczesniej dwukropek
			$bb = substr($bb, strpos($bb, ":") + 1); //bierzemy cala czesc ramki za znakiem ':' czyli separatorem sciezki od pola informacji
			if(($bb[0] === "@") or ($bb[0] === "!") or ($bb[0] === "=") or ($bb[0] === "/")) //jesli to jest ramka z pozycja
			{
				if($bb[7] === 'z')
				{
					if(in_array($bb[26], array('!', '#', '%', '&', '+', ',', '-', '.', '/', ':', ';', '?', '@', 'A', 'B', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'T', 'V', 'W', 'Z', '\\', ']', '_', '`', 'c', 'd', 'h', 'i', 'l', 'm', 'n', 'o', 'q', 'r', 't', 'w', 'x', 'y', 'z', '}')))
					{
						if(!in_array($znakstacji, $stacjestale))
						{
							$stacjestale[] = $znakstacji;
						}
					}	
					elseif(in_array($bb[26], array('$', '\'', '(', ')', '*', '<', '=', '>', 'C', 'F', 'P', 'R', 'U', 'X', 'Y', '[', '^', 'a', 'b', 'f', 'g', 'j', 'k', 'p', 's', 'u', 'v')))
					{
						if(!in_array($znakstacji, $stacjeruchome))
						{
							$stacjeruchome[] = $znakstacji;
						}
					}
					else
					{
						if(!in_array($znakstacji, $stacjeinne))
						{
							$stacjeinne[] = $znakstacji;
						}
					}	
				} else
				{	
					if(in_array($bb[19], array('!', '#', '%', '&', '+', ',', '-', '.', '/', ':', ';', '?', '@', 'A', 'B', 'G', 'H', 'I', 'K', 'L', 'M', 'N', 'T', 'V', 'W', 'Z', '\\', ']', '_', '`', 'c', 'd', 'h', 'i', 'l', 'm', 'n', 'o', 'q', 'r', 't', 'w', 'x', 'y', 'z', '}')))
					{
						if(!in_array($znakstacji, $stacjestale))
						{
							$stacjestale[] = $znakstacji;
						}
					}	
					elseif(in_array($bb[19], array('$', '\'', '(', ')', '*', '<', '=', '>', 'C', 'F', 'P', 'R', 'U', 'X', 'Y', '[', '^', 'a', 'b', 'f', 'g', 'j', 'k', 'p', 's', 'u', 'v')))
					{
						if(!in_array($znakstacji, $stacjeruchome))
						{
							$stacjeruchome[] = $znakstacji;
						}
				
					}
					else
					{
						if(!in_array($znakstacji, $stacjeinne))
						{
							$stacjeinne[] = $znakstacji;
						}
				
					}	
				}
			}
			//czesc dla podzialu na stacje uslyszane posrednio i bezposrednio
			$cc = substr($ramka, strpos($ramka, ">")); //ucinamy na wszelki wypadek
			$cc = substr($cc, 0, strpos($cc, ":")); //bierzemy cala czesc ramki przed znakiem ':' czyli separatorem sciezki od pola informacji
			if(strpos($cc, '*') !== false) //jesli mamy gwiazdke, to ta ramka na pewno nie jest bezposrednio
			{
				if(!in_array($znakstacji, $stacjeposr))
				{
					$stacjeposr[] = $znakstacji;
				}
			} else //a jesli nie ma gwiazdki
			{
				$sppos = strpos($cc, "SP");
				if((strpos($cc, "SP") !== false) and ($cc[$sppos + 3] == "-")) //jesli jest tam SP, to moze to mimo wszystko byc powtorzone przez digi
				{
					if($cc[$sppos + 2] == $cc[$sppos + 4]) //jesli jest tam np. SP4-4 (czyli 2 takie same cyfry)
					{
						if(!in_array($znakstacji, $stacjebezposr))
						{
							$stacjebezposr[] = $znakstacji;
						}
					} else //a jesli jest tam np. SP3-2 to ramka jest powtorzona
					{
						if(!in_array($znakstacji, $stacjeposr))
						{
							$stacjeposr[] = $znakstacji;
						}
					}
				} else //a jesli nie ma SP, to na pewno jest bezposrednio
				{
					if(!in_array($znakstacji, $stacjebezposr))
					{
						$stacjebezposr[] = $znakstacji;
					}
				}
			}
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
$adrespliku = "/var/log/aprx/aprx-rf.log";
$stacjeodebrane = array();
$stacjestale = array();
$stacjeruchome = array();
$stacjeinne = array();
$stacjeposr = array();
$stacjebezposr = array();
$ilelinii = 0;
$plik = array();
$zczasu = 0;
$szukanyznak;

if(isset($_GET['szukajznak']))
{
	$szukanyznak = strtoupper($_GET['szukajznak']);
}


if(!isset($_GET['time']) or ($_GET['time'] == ""))
{
	$zczasu = time() - 3600;
} 
elseif($_GET['time'] == "e")
{
	$zczasu = 0;
}
else
{
	$zczasu = time() - ($_GET['time'] * 3600);
}

$plik = file($adrespliku); //odczytujemy plik
$liniiwpliku = count($plik);
while ($ilelinii < $liniiwpliku) { //czytaj linia po linii
    $linia = $plik[$ilelinii];
	stacjaparse($linia);
	if(strpos($linia, $znakraw." R") !== false) { //jesli to ramka odebrana przez siec radiowa
		$ilerx++;
	} elseif(strpos($linia, "APRSIS    R") !== false) //a jesli z APRSIS
	{
		$ileis++;
	} elseif(strpos($linia, $znakraw." T") !== false) //jesli to sa ramki nadane na radio
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
?>
<br>
<br><b>Pokaż:</b> <a href="index.php?dane=ilosc">Odebrane stacje</a> - <a href="index.php?dane=rs">Informacje o stacjach</a> - <a href="index.php?dane=szukaj">Pokaż ramki wybranej stacji</a>
<br><br><hr>
<?php
if($dane !== "szukaj")
{
?>
<br><br> <form action="index.php" method="get">
Pokaż stacje z czasu: 
<select name="time">
	<option value="1" <?php if($_GET['time'] == 1) echo 'selected=\"selected\"'?>>1 godziny</option>
	<option value="2" <?php if($_GET['time'] == 2) echo 'selected=\"selected\"'?>>2 godzin</option>
	<option value="4" <?php if($_GET['time'] == 4) echo 'selected=\"selected\"'?>>4 godzin</option>
	<option value="6" <?php if($_GET['time'] == 6) echo 'selected=\"selected\"'?>>6 godzin</option>
	<option value="12" <?php if($_GET['time'] == 12) echo 'selected=\"selected\"'?>>12 godzin</option>
	<option value="24" <?php if($_GET['time'] == 24) echo 'selected=\"selected\"'?>>1 dnia</option>
	<option value="48" <?php if($_GET['time'] == 48) echo 'selected=\"selected\"'?>>2 dni</option>
	<option value="168" <?php if($_GET['time'] == 168) echo 'selected=\"selected\"'?>>tygodnia</option>
	<option value="720" <?php if($_GET['time'] == 720) echo 'selected=\"selected\"'?>>30 dni</option>
	<option value="e" <?php if($_GET['time'] == 'e') echo 'selected=\"selected\"'?>>wszystko</option>
</select>
<input type="submit" value="Odśwież">
<?php
if($dane === "ilosc") //jesli mamy pokazac ile jest stacji
{
	$unikalne = array_count_values($stacjeodebrane); //ilosc wystapien kazdej z wartosci, ale uzyjemy tego tylko dla stacji unikalnych
	if(empty($unikalne))
	{
		$unikalne[1] = 0;
	}
	echo "<br><br><b>Stacje odebrane drogą radiową (w tym $unikalne[1] unikalnych):</b><br>";
	echo "<br><pre><font color=\"blue\"><b>Znak\t&nbsp;&nbsp;&nbsp;&nbsp;Punkty</b></font><br>";
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
} elseif($dane === "rs") //a jesli mamy pokazac czy to stacje ruchome czy stale
{
	asort($stacjeruchome); //sortujemy alfabetycznie
	asort($stacjestale);
	asort($stacjeinne);
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje ruchome (<u>".count($stacjeruchome)."</u>):</b></font> ";
	while(list($u, $o) = each($stacjeruchome))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje stałe (<u>".count($stacjestale)."</u>):</b></font> ";
	while(list($u, $o) = each($stacjestale))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"green\"><b>Stacje inne (<u>".count($stacjeinne)."</u>):</b></font> ";
	while(list($u, $o) = each($stacjeinne))
	{
		echo $o.", ";
	}
	
	
	asort($stacjeposr); //sortujemy alfabetycznie
	asort($stacjebezposr);
	echo "<br><br><hr /><br><font color=\"blue\"><b>Stacje odebrane pośrednio (przez digi) (<u>".count($stacjeposr)."</u>):</b></font> ";
	while(list($u, $o) = each($stacjeposr))
	{
		echo $o.", ";
	}
	echo "<br><br><font color=\"red\"><b>Stacje odebrane bezpośrednio (<u>".count($stacjebezposr)."</u>):</b></font> ";
	while(list($u, $o) = each($stacjebezposr))
	{
		echo $o.", ";
	}	
}



}
else
{
?>
<br><br>
<form action="index.php" method="get">
	Pokaż wszystkie ramki od znaku: <input type="text" name="szukajznak" <?php if(isset($_GET['szukajznak'])) echo "value=\"".$_GET['szukajznak']."\""; ?>>
	<input type="submit" value="Pokaż">
</form>

<?php
if(isset($_GET['szukajznak']) and ($_GET['szukajznak'] !== ""))
{
	$szukanyznak = strtoupper($_GET['szukajznak']);
	global $liniiwpliku;
	global $plik;
	global $znakraw;
	echo '<br>';
	$ramkiodstacji = array();
	$b = 0;
	while($b < $liniiwpliku)
	{
		$linia2 = $plik[$b];
		if(strpos($linia2, $znakraw." R "))
		{
			$znaktejstacji = explode(">", $linia2); //odetnij wszystko za znakiem
			$znaktejstacji = substr($znaktejstacji[0], strpos($znaktejstacji[0], $znakraw." R ") + strlen($znakraw." R ")); //obetnij linie do miejsca, gdzie jest znak
			if($znaktejstacji == $szukanyznak)
			{
				
				$ramkiodstacji[] = str_replace($znakraw." R ", "&nbsp;&nbsp;&nbsp;", $linia2);
			}
		}
		$b++;
	}
	echo "<font color=\"red\"><b>Znaleziono ".count($ramkiodstacji)." ramek od stacji $szukanyznak</b></font><br><br>";
	array_multisort($ramkiodstacji, SORT_DESC);
	for($o = 0; $o < count($ramkiodstacji); $o++)
	{
		echo $ramkiodstacji[$o]."<br>";
	}
}
}
}
?>

<br><br><br><br><br>
<center>Napisane w 2017 roku przez Piotra SQ8VPS</center>
</body>
</html>



