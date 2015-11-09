<?

//if (is_dir("/sites.local/ipaginebaby/warehouse_new/")) exec("rm -dfr /sites.local/ipaginebaby/warehouse_new/");
//mkdir("/sites.local/ipaginebaby/warehouse_new/");

	$_sql="SELECT count(id) as tot from places where not address='' AND lat=0";
	$site->dataObj->db->query($_sql);
	$res = $site->dataObj->db->fetch();
	$tot=$res[0]->tot;

	$_sql="SELECT * from places where not address='' AND lat=0 order by address  limit ".$skip.",".$limit;
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
		$json = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($r->address.", ".$r->cap." ".$r->city." ".$r->country)."&sensor=true"), true);
		if ($json['status']=="OVER_QUERY_LIMIT") {
			echo("OVER_QUERY_LIMIT");
			break;
		}
		if ($json['results'][0]['geometry']['location']['lat']) {
			$sql = "update places set lat='".$json['results'][0]['geometry']['location']['lat']."', lng='".$json['results'][0]['geometry']['location']['lng']."' where id=".$r->id;
			$site->dataObj->db->query($sql);
			echo($sql."<br />\n");
		}
		
		echo("http://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($r->address.", ".$r->cap." ".$r->city." ".$r->country)."&sensor=true"."<br />\n");
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
