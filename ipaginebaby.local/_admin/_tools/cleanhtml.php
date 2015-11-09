<?

//if (is_dir("/sites.local/ipaginebaby/warehouse_new/")) exec("rm -dfr /sites.local/ipaginebaby/warehouse_new/");
//mkdir("/sites.local/ipaginebaby/warehouse_new/");

	$_sql="SELECT count(id) as tot from places";
	$site->dataObj->db->query($_sql);
	$res = $site->dataObj->db->fetch();
	$tot=$res[0]->tot;

	$_sql="SELECT * from places limit ".$skip.",".$limit;
	$site->dataObj->db->query($_sql);
	echo($_sql."<br />\n");
	$res = $site->dataObj->db->fetch();
	//print_r($res);
	echo(($skip+$limit)."-".$tot."");
	if(($skip+$limit)<$tot){
		echo(" | <a href=\"".str_replace(array("skip=".$skip,"ord="),array("skip=".($skip+$limit),"ord_old="),$_SERVER['REQUEST_URI'])."\">next</a>");
	}
?>
<div id="resultsTxt">
<?
	foreach($res as $r){
		$t = $site->dataObj->makeRichTextToPlain($r->txt);
		
		$sql = "update places set txt='".mysql_real_escape_string($t)."' where id=".$r->id;
		$site->dataObj->db->query($sql);
		//echo($sql."<br />\n");
	}
?>
</div>
<?
	echo(($skip+$limit)."-".$tot."");
	if(($skip+$limit)<$tot){
		echo(" | <a href=\"".str_replace(array("skip=".$skip,"ord="),array("skip=".($skip+$limit),"ord_old="),$_SERVER['REQUEST_URI'])."\">next</a>");
		echo("<script type=\"text/javascript\"><!--\n");
		echo("//alert(document.getElementById('resultsTxt').innerHTML.indexOf('Warning'));\n");
		echo("if (document.getElementById('resultsTxt').innerHTML.indexOf('Warning')==-1) var t=setTimeout(\"window.location.href='".str_replace(array("skip=".$skip,"ord="),array("skip=".($skip+$limit),"ord_old="),$_SERVER['REQUEST_URI'])."'\",2000);\n");
		echo("//--></script>\n");
	}

?>
