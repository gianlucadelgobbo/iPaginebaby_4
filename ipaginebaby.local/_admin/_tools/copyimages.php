<?

	//if (is_dir("/sites.local/ipaginebaby/warehouse_new/")) exec("rm -dfr /sites.local/ipaginebaby/warehouse_new/");
	//mkdir("/sites.local/ipaginebaby/warehouse_new/");

		$_sql="SELECT count(id) as tot from places where txt like '%text-img%'";
		$site->dataObj->db->query($_sql);
		$res = $site->dataObj->db->fetch();
		$tot=$res[0]->tot;

		$_sql="SELECT txt from places where txt like '%text-img%' limit ".$skip.",".$limit;
		$site->dataObj->db->query($_sql);
		echo($_sql);
		$res = $site->dataObj->db->fetch();
		//print_r($res);
	
		foreach($res as $r){
			$a = explode("warehouse/",$r->txt);
			$b = explode("\" />",$a[1]);
			//print_r("/sites.local/ipaginebaby/warehouse/".$b[0]."\n");
			copy("/sites.local/ipaginebaby/warehouse/".$b[0],"/sites.local/ipaginebaby/warehouse_new/".$b[0]);
		}
		echo(($skip+$limit)."-".$tot."");
		if(($skip+$limit)<$tot){
			echo(" | <a href=\"".str_replace(array("skip=".$skip,"ord="),array("skip=".($skip+$limit),"ord_old="),$_SERVER['REQUEST_URI'])."\">next</a>");
		}
		if(($skip+$limit)<$tot){
			echo(($skip+$limit)."-".$tot." | <a href=\"".$_SERVER['PHP_SELF']."?skip=".($skip+$limit)."&rows=".$tot."\">next</a>");
			echo("<script type=\"text/javascript\"><!--\n");
			echo("//alert(document.getElementById('resultsTxt').innerHTML.indexOf('Warning'));\n");
			//echo("if (document.getElementById('resultsTxt').innerHTML.indexOf('Warning')==-1) var t=setTimeout(\"window.location.href='".$_SERVER['PHP_SELF']."?skip=".($skip+$limit).$getString."'\",700);\n");
			echo("//--></script>\n");
		}

	?>