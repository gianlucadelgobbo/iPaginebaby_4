<ol>
<?	
	$site->dataObj->db->query("select id,name,city,permalink from places where (permalink is null or permalink='' or permalink like '\_%') and not city='' limit 500");
	//$site->dataObj->db->query("select id,name,city,permalink from places where id=2465");
	//$site->dataObj->db->query("select * from places limit 1");
	$res=$site->dataObj->db->fetch();
	foreach($res as $row){
		$permalink=$site->dataObj->sanitize_title_with_dashes($row->city)."/".$site->dataObj->getPermalink($row->id,'places',mysql_escape_string($row->name),0);		
		$site->dataObj->db->query("update places set permalink='".$permalink."' where id=".$row->id);
		echo("<li>".$row->id." ".$permalink."</li>\n");
	}
?>
</ol>
<?
	echo("<script type=\"text/javascript\"><!--\n");
	echo("var t=setTimeout(\"window.location.href='/_admin/tools/updatePermalink'\",1500);\n");
	echo("//--></script>\n");

?>