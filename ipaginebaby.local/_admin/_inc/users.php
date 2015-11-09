<? echo($searchBlock); ?>
<?

	if ($_GET['submit_search']) {
		$cont=$skip;
		$_sql="SELECT count(id) as tot from users ";
		$site->dataObj->db->query($_sql);
		$res = $site->dataObj->db->fetch();
		$tot=$res[0]->tot;
		
		if ($_GET['ord']) {
			if ($_SESSION['ord'] == $_GET['ord']) {
				if ($_SESSION['ver'] == "asc") {
					$_SESSION['ver'] = "desc";
				} else {
					$_SESSION['ver'] = "asc";
				}
			} else {
				$_SESSION['ord'] = $_GET['ord'];
				$_SESSION['ver'] = "asc";
			}
		}

		$_sql="SELECT * from users "." limit ".$skip.",".$limit;
		$site->dataObj->db->query($_sql);
		echo($_sql."<br />\n");
		$res = $site->dataObj->db->fetch();
	
		$table="
			<tr class=\"head\">
				<td>N</td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=id\">id</a></td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=date\">date</td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=date_last\">date_last</td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=name\">name</td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=email\">email</td>
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=fbid\">fbid</td>";
			if ($_GET['fbObj']==1) $table.="
				<td><a href=\"".str_replace("ord=","ord_old=",$_SERVER['REQUEST_URI'])."&ord=fbObj\">fbObj</td>";
			$table.="
			</tr>";
		
		//print_r($res);
	
		foreach($res as $r){
			$table.="<tr>\n";
			$table.="<td class=\"n\">".$cont."</td>\n";
			$table.="<td class=\"id\">".$r->id."</td>\n";
			$table.="<td class=\"date_new\">".$r->date."</td>\n";
			$table.="<td class=\"date_edit\">".$r->date_last."</td>\n";
			$table.="<td class=\"cognome\">".$r->name."</td>\n";
			$table.="<td class=\"emails\">".$r->email."</td>\n";
			$table.="<td class=\"fbid\">".$r->fbid."</td>\n";
			if ($_GET['fbObj']==1) $table.="<td class=\"fbObj\">".$r->fbObj."</td>\n";
			$table.="</tr>\n";
			
			$cont++;
		}
 ?>
 
 
<? 
		echo(($skip+$limit)."-".$tot."");
		if(($skip+$limit)<$tot){
			echo(" | <a href=\"".str_replace(array("skip=".$skip,"ord="),array("skip=".($skip+$limit),"ord_old="),$_SERVER['REQUEST_URI'])."\">next</a>");
		}
?>
 
<div id="resultsTxt">
<? 
		print_r("<table border=\"1\">");
		print_r($table);
		print_r("</table>");
?>
</div>
 
<? 
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
	}

	?>