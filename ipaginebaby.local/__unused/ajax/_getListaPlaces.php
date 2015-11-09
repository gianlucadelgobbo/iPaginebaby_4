<?
include_once("../site.class.php");	
$site = new site();
print_r($site->dataObj->writeListaPostHomeAjax());
?>