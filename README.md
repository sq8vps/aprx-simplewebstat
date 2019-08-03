# APRX Simple Statistics Website Generator

A simple statistics and information generator for APRX software, which allows to monitor load, frames, stations details, statistics from selected time window and more.


## Installation and usage

For installation just copy all files to the webistes folder in your WWW server directory. Make sure it supports PHP.

To configure, open config.php file with some text editor.

Enter the full path to your APRX-RF log file (aprx-rf.log, NOT aprx.log):

```
$logpath = "/some/path/aprx-rf.log";
```
Usually you can find it under /var/log/aprx/aprx-rf.log. Incorrect path will make the script unable to work.


This was the only required step and now the software should work.

It's recommended to set also another settings:

Your station latitude and longtitude for distance calculation (in decimal degrees):
```
$stationlat = 49.013855;
$stationlon = 28.762225;
```

And your regional country/state untraced (flood) digipeater alias, which helps the software to distinguish between direct and digipeated frames.
For example it's SP in Poland, CZ in Czech Republic or HU in Hungary.
```
$cntalias = "SP";
```

Normally every time you open the statistics website, you have to enter interface callsign. If you want to set static interface callsign (but it can be temporarily changed via website for one session), you can do this here:
```
$static_if = 1;
$static_call = "N0CALL-11;
$static_lang = "en";
```
Set $static_if to 1 to enable. Available languages are en for English and pl for Polish.


Custom logo and text info can be set optionally. The logo will be displayed on all pages and text info will be displayed only on main (summary.php) page.
To set the logo you have to provide the URL/address of the image:
```
$logourl="aprslogo.png";
```
That can be a link to an external or local file. The image will be automatically resized to fit the page.

To set your custom text info place that info in a file named **custom.php**. It can contain HTML and PHP code.

## AX.25 realtime monitor configuration

You can watch the AX.25 traffic in realtime using this software. The default configuration should be sufficient in most cases.

You can set monitor's refresh rate (in ms unit):
```
$refresh=2000;
```

You can change colors:
```
$timestampcolor="silver";
$APRSIScolor="orange";
$RFcolor="blue";
$TXcolor="red";
$RXcolor="green";
$pathcolor="purple";
```

Here you set the number of rows displayed at session opening:
```
$startrows=5;
```



## Software stability

This is a BETA software. It can contain some bugs and may be written in non-efficient way. Please contact authors if you find any bug.

## Authors

* **Peter SQ8VPS**
* **Alfredo IZ7BOJ**


## License

Project is free for non-commercial use. You can modify and publish this software, but you have to put an information about original authors.
