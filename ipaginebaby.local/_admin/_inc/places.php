<? echo($searchBlock); ?>
<?

	if ($_GET['submit_search']) {
		$enabled = array("id","hl","pbcard","cat","name","txt","website","email","address","street_number","zip","city","country","lat","lng","tel","mtel","fax","checkin","rate","author","date_new","date_edit","notes","visits","img");
		$w ="";
		foreach($_GET as $k=>$v){
			if ($v!='' && in_array($k,$enabled)) {
				if ($k=='cat') {
					foreach($v as $kk){
						$w.= $k." like '%|".mysql_real_escape_string($kk)."|%' AND ";
					}
				} else if ($k=='txt' || $k=='name' || $k=='notes') {
					$w.= $k." like '%".mysql_real_escape_string($v)."%' AND ";
				} else {
					$w.= $k."='".mysql_real_escape_string($v)."' AND ";
				}
			}
		}
		if ($w) $w = substr($w, 0, strlen($w)-4);
		$cont=$skip;
		//$_sql="SELECT count(id) as tot from places ".($w ? "where ".$w : "");
		$_sql="SELECT count(id) as tot from places where public=".($_GET['public'] ? $_GET['public'] : 0)." and new=".($_GET['new'] ? $_GET['new'] : 0).($w ? " and  ".$w : "");
		$site->dataObj->db->query($_sql);
		$res = $site->dataObj->db->fetch();
		$tot=$res[0]->tot;
		
		$myquery = "";
		foreach($_GET as $k=>$v){
			if($k!="param" && $k!="ord" && $k!="ver" && $v!=""){
				if(is_array($v)){
					foreach($v as $kk=>$vv){
						$myquery.= $k."[]=".$vv."&";
					}
				} else {
					$myquery.= $k."=".$v."&";
				}
			}
		}
		$myquery = substr($myquery,0,strlen($myquery)-1);

		$_sql="SELECT * from places where public=".($_GET['public'] ? $_GET['public'] : 0)." and new=".($_GET['new'] ? $_GET['new'] : 0).($w ? " and  ".$w : "")." ".($_GET['ord'] ? "order by ".$_GET['ord']." ".$_GET['ver'] : "")." limit ".$skip.",".$limit;
		$site->dataObj->db->query($_sql);
		echo($_sql."<br /><br />\n");
		$res = $site->dataObj->db->fetch();
		$table="
			<tr class=\"head\">
				<td>N</td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=id\">id</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=date_new\">date_new</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=date_edit\">date_edit</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=hl\">hl</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=pbcard\">pbcard</a></td>
				<td>cat txt</td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=cat\">cat</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=cat\">cat</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=name\">name</a></td>
				<td>txt HTML</td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=txt\">txt</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=website\">website</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=email\">email</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=address\">address</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=street_number\">street_number</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=zip\">zip</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=city\">city</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=country\">country</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=lat\">lat</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=lgt\">lgt</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=tel\">tel</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=mtel\">cell</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=fax\">fax</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=checkin\">checkin</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=visits\">visits</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=rate\">rate</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=owner\">owner</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=notes\">note_interne</a></td>
				<td>img</td>
				<td>gallery</td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=public\">public</a></td>
				<td><a href=\"/_admin/places/?".$myquery."&ver=".($_GET['ver']=="asc" ? "desc" : "asc")."&ord=new\">new</a></td>
			</tr>";
		
		//print_r($res);
	
		foreach($res as $r){
			//echo("ID: ".$cont." ".$r->id."<br />\n");
			$interestK=array();
			if($r->cat){
				$arrK=explode(",", $r->cat);
				for($i=0;$i<Count($arrK);$i++){
					$arrKval=explode("|", $arrK[$i]);
					if($arrKval[2]){
						$interestK[]=$site->dataObj->getNomeChiave($arrKval[1])." / ".$site->dataObj->getNomeChiave($arrKval[2]);
					}else{
						$interestK[]=$site->dataObj->getNomeChiave($arrKval[1]);
					}				
				}
			}
			$strkNome=implode("<br />", $interestK);

			if($r->hl){
				$site_url = "http://ipb.flyer.it";
				$site_path = "/sites.local/ipaginebaby/";
				if(is_dir($site_path."warehouse/".$r->id)){
					if ($handle = opendir($site_path."warehouse/".$r->id)) {
					    while (false !== ($entry = readdir($handle))) {
						    if ($entry !== "." && $entry != ".." && substr($entry, strlen($entry)-3, 3)=="jpg") {
							    if ($entry == "logo.jpg") {
									$r->img2 = $site_url."/warehouse/".$r->id."/".$entry;
									echo($r->img2);
							    } else {
							    	if (!$r->gallery) $res[$a]->gallery = array();
							        $r->gallery[] = array("src"=>$site_url."/warehouse/".$r->id."/".$entry);
							    }
						    }
					    }
					
					    closedir($handle);
					}
				}
			}
			
			$table.="<tr>\n";
			$table.="<td class=\"n\">".$cont."</td>\n";
			$table.="<td class=\"id\">ID: ".$r->id."<br /><a href=\"#\" onclick=\"onEdit(".$r->id.")\">EDIT</a><br /><br /><a href=\"#\" onclick=\"onDelete(".$r->id.",this)\">DELETE</a></td>\n";
			$table.="<td class=\"date_new\">".$r->date_new."</td>\n";
			$table.="<td class=\"date_edit\">".$r->date_edit."</td>\n";
			$table.="<td class=\"hl\">".$r->hl."</td>\n";
			$table.="<td class=\"pbcard\">".$r->pbcard."</td>\n";
			$table.="<td class=\"chiavi\">".$strkNome."</td>\n";
			$table.="<td class=\"chiavi\">".implode("<br />", explode(",", $r->cat))."</td>\n";
			$table.="<td class=\"cat\">".$r->cat."</td>\n";
			$table.="<td class=\"name\">".$r->name."</td>\n";
			$table.="<td class=\"txtHtml\">".$site->dataObj->makeTextPlainToRich($r->txt)."</td>\n";
			$table.="<td class=\"txt\">".($r->txt)."</td>\n";
			$table.="<td class=\"website\">".$r->website."</td>\n";
			$table.="<td class=\"emails\">".$r->email."</td>\n";
			$table.="<td class=\"address\">".$r->address."</td>\n";
			$table.="<td class=\"street_number\">".$r->street_number."</td>\n";
			$table.="<td class=\"zip\">".$r->zip."</td>\n";
			$table.="<td class=\"city\">".$r->city."</td>\n";
			$table.="<td class=\"country\">".$r->country."</td>\n";
			$table.="<td class=\"lat\">".$r->lat."</td>\n";
			$table.="<td class=\"lng\">".$r->lng."</td>\n";
			$table.="<td class=\"tel\">".$r->tel."</td>\n";
			$table.="<td class=\"mtel\">".$r->mtel."</td>\n";
			$table.="<td class=\"fax\">".$r->fax."</td>\n";
			$table.="<td class=\"checkin\">".$r->checkin."</td>\n";
			$table.="<td class=\"visits\">".$r->visits."</td>\n";
			$table.="<td class=\"rate\">".$r->rate."</td>\n";
			$table.="<td class=\"author\">".$r->author."</td>\n";
			$table.="<td class=\"notes\">".$r->notes."</td>\n";
			if($r->img2){
				$table.="<td class=\"img\"><img src=\"".$r->img2."\" />".$r->img2."</td>\n";
			} else {
				$table.="<td class=\"img\">&nbsp;</td>\n";
			}
			if($r->gallery){
				$table.="<td class=\"gallery\"><a href=\"/_admin/gallery/?dir=warehouse/".$r->id."\" />Gallery:".$r->id."</a></td>\n";
			} else {
				$table.="<td class=\"gallery\">&nbsp;</td>\n";
			}
			$table.="<td class=\"public\">".$r->public."</td>\n";
			$table.="<td class=\"new\">".$r->new."</td>\n";
			$table.="</tr>\n";
			
			$cont++;
		}
 ?>
 
 
<? 
		echo("From ".$skip." to ".($skip+count($res))." of ".$tot."");
		if(($skip+$limit)<$tot){
			echo(" | <a href=\"/_admin/places/?".str_replace(array("skip=".$skip),array("skip=".($skip+$limit)),$myquery)."".($_GET['ver'] ? "&ver=".$_GET['ver'] : "").($_GET['ord'] ? "&ord=".$_GET['ord'] : "")."\">next</a>");
		}
?>
 
<div id="resultsTxt">
<? 
		print_r("<table border=\"1\">");
		print_r($table);
		print_r("</table>");
?>
</div>
<? include_once("formEdit.php"); ?>
<? 
		echo("From ".$skip." to ".($skip+$limit)." of ".$tot."");
		if(($skip+$limit)<$tot){
			echo(" | <a href=\"/_admin/places/?".str_replace(array("skip=".$skip),array("skip=".($skip+$limit)),$myquery)."".($_GET['ver'] ? "&ver=".$_GET['ver'] : "").($_GET['ord'] ? "&ord=".$_GET['ord'] : "")."\">next</a>");
		}
	}

	?>