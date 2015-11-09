<?
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$_GET['address']."&sensor=false";
echo file_get_contents($url);
?>