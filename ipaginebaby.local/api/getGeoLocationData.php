<?
$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$_GET['latlng']."&sensor=false";
echo file_get_contents($url);
?>