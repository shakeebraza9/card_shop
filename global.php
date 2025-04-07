<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include_once('config.php');
include_once('class/SiteSettings.php');
include_once('class/electurmcls.php');



$settings = new SiteSettings($pdo, $encryptionKey);
$urlval = "http://localhost/shop3/";


date_default_timezone_set('Europe/London');

$currentDate = date('Y-m-d'); 
$currentTime = date('H:i:s');
$currentDateTime = date('Y-m-d H:i:s'); 