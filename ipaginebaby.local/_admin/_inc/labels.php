
<?

	$errori = 0;
	$folderContainerSitePath = "/sites.local/ipaginebaby/";
	include_once($folderContainerSitePath."_admin/spreadsheetimport/functions.php");
	
	// COSTRUISCO IL DIZIONARIO DAL FILE GOOGLE DOC
	$obj = json_decode(str_replace(array("gdata.io.handleScriptLoaded(",");"),"",file_get_contents("https://spreadsheets.google.com/feeds/list/0Ar6GNQklR9jQdHd3TmZTN1FEal8xTWJpSTVpT0FZWHc/od4/public/values?alt=json-in-script")),true);
	
	$langArr = array("it","en");
	
	$dictionary = array();
	foreach($obj['feed']['entry'] as $item){
		foreach($langArr as $lang){
			//print_r($item["gsx\$key"]);
			$dictionary[$lang][$item["gsx\$key"]["\$t"]] = $item["gsx\$".$lang]["\$t"];
		}
	}
	// FINE COSTRUISCO IL DIZIONARIO DAL FILE GOOGLE DOC

	// COSTRUISCO IL DIZIONARIO DAI FILES DEL SITO
	if ($handle = opendir($folderContainerSitePath)) {
		$arr_confronto = surfFolders($handle, $folderContainerSitePath,array());
		closedir($handle);
	}
	// COSTRUISCO IL DIZIONARIO DAI FILES DEL SITO

	if (isset($_GET['save'])) {
		$now= date("Y-m-d_G-i-s");
		$strOut = "";
		for($b=0;$b<count($langArr);$b++) {
			$phpStr = var_export($dictionary[$langArr[$b]], true);
			$phpStr="<?\n\$dictionary = ".$phpStr."; \n ?>";
			$strOut.= "Muovi: <strong>".$folderContainerSitePath."_php/conf/".$langArr[$b].".php"."</strong> in <strong>".$folderContainerSitePath."__tools/spreadsheetimport/conf/backup/".$langArr[$b]."_".$now.".php</strong><br /><br />";				
			copy($folderContainerSitePath."_php/conf/".$langArr[$b].".php",$folderContainerSitePath."_admin/spreadsheetimport/conf/backup/".$langArr[$b]."_".$now.".php");				
			file_put_contents($folderContainerSitePath."_php/conf/".$langArr[$b].".php",$phpStr);	
			file_put_contents($folderContainerSitePath."js/".$langArr[$b].".js", "var dictionary = ".json_encode($dictionary[$langArr[$b]]) );	
			$strOut.= "Crea Oggetto php: <strong>".$folderContainerSitePath."_php/conf/".$langArr[$b].".php</strong><br /><br />";
		}
		file_put_contents($folderContainerSitePath."js/dictionary.js", "var dictionary = ".json_encode($dictionary) );	
	} else {
		//// BLOCCO COL DISTINCT CORRETTO
		$strOut= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$strOut.= "<tr>\n";
		$strOut.= "<td><strong>ID</strong></td>\n";
		$strOut.= "<td><strong>KEY</strong></td>\n";
		if ($_GET['write']) $strCsv = '"ID","KEY"';
		for($b=0;$b<count($langArr);$b++) {
			$strOut.= "<td><strong>".$langArr[$b]."</strong></td>\n";
			if ($_GET['write']) $strCsv.=',"'.$langArr[$b].'"';
		}
		if ($_GET['write']) $strCsv.='
	';
		$strOut.= "<td><strong>PAGES</strong></td>\n";
		$strOut.= "</tr>\n";
		$conta=1;
		//print_r($arr_confronto);
		
		$arr_confronto2 = array();
		foreach($arr_confronto as $aaaa) {
			$arr_confronto2[]=$aaaa['lab'];
			$strOut.= "<tr>\n";
			$strOut.= "<td valign=\"top\">".$conta."</td>\n";
			$strOut.= "<td valign=\"top\">".$aaaa['lab']."</td>\n";
			if ($_GET['write']) $strCsv.='"'.$conta.'","'.$aaaa['lab'].'"';
			for($b=0;$b<count($langArr);$b++) {
				$tmp = getLabel($aaaa['lab'],$dictionary[$langArr[$b]],$errori);
				if(strpos($tmp, "\"red\"")!==false) {
					$errori++;
				}
				$strOut.= "<td valign=\"top\">".$tmp."</td>\n";
				if ($_GET['write']) $strCsv.=',"'.$tmp.'"';
			}
			$strOut.= "<td valign=\"top\" style=\"white-space:nowrap;\">".str_replace($folderContainerSitePath,"/",implode("<br />",$aaaa['url']))."</td>\n";
			$strOut.= "</tr>\n";
			if ($_GET['write']) $strCsv.='
	';
			$conta++;
		}
		$strOut.= "</table>\n";
		$strOut.= "<hr />\n";
	
		$strOut.= "<h1>LABEL PRESENTI MA NON TROVATE NEL CODICE PHP</h1>\n";
	
		$conta=1;
		$strOut.= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n";
		$strOut.= "<tr>\n";
		$strOut.= "<td><strong>ID</strong></td>\n";
		$strOut.= "<td><strong>KEY</strong></td>\n";
		for($b=0;$b<count($langArr);$b++) {
			$strOut.= "<td><strong>".$langArr[$b]."</strong></td>\n";
		}
		$strOut.= "</tr>\n";
	
		for($a=0;$a<count($langArr);$a++) {
			foreach($dictionary[$langArr[$a]] as $key => $val) {
				//echo($langArr[$a]." - ".$key ." - ". $val."<br>");
				if (!in_array($key,$arr_confronto2)) {
					$strOut.= "<tr>\n";
					$strOut.= "<td valign=\"top\">".$conta."</td>\n";
					$strOut.= "<td valign=\"top\">".$langArr[$a]." - ".$key."</td>\n";
					for($b=0;$b<count($langArr);$b++) {
						$tmp = getLabel($key,$dictionary[$langArr[$b]],$errori);
						if(strpos($tmp, "\"red\"")!==false) {
							$errori++;
						}
						$strOut.= "<td valign=\"top\">".$tmp."</td>\n";
					}
					$strOut.= "</tr>\n";
					$conta++;
				}
			}
		}
		$strOut.= "</table>\n";
		$strOut.= "<hr />\n";
		$strOut.= "<h3><a href=\"".$_SERVER['REQUEST_URI']."?save=true\">SAVE LABELS FILES</a></h3>\n";
	}
?>
<div id="cnt">
	<div class="right">
		<a href="https://docs.google.com/a/flyer.it/spreadsheet/ccc?key=0Ar6GNQklR9jQdHd3TmZTN1FEal8xTWJpSTVpT0FZWHc" target="_blank">https://docs.google.com/a/flyer.it/spreadsheet/ccc?key=0Aizd44G8PfkOdFZtT1h5cTktVDZBemQyMzk4ZTgwTXc</a>
	</div>
	<br class="myClear" />
	<div id="result">
	<? echo($strOut); ?>
	</div>
</div>
