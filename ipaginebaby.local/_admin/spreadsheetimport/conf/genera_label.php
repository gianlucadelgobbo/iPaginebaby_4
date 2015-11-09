<?
include_once("../../_php/ajax/includeManage.php");
include_once("../../_php/class/core/xml.class.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Labels Manager</title>
<style type="text/css">
<!--
body {
	font-family:Arial, Helvetica, sans-serif;
	margin: 10px;
}
.red{color:#CC0000; font-weight:bold;}
td{ padding:5px;}
table{ border:1px solid #666666;}
-->
</style></head>

<body>
<h1>Labels Manager</h1>
<?	
$errori = 0;
$folderContainerSitePath = "/sites/flxer/";

	function getLabel($str,$dict,$errori){
		if(array_key_exists($str, $dict)){
			if($dict[$str]){
				$tmp = $dict[$str];
			} else {
				$tmp = 	"<span id=\"err".$errori."\" class=\"red\">".$str."</span>";
				echo("<a href=\"#err".$errori."\" class=\"red\">".$errori." - ".$str."</a><br />");
			}
		} else {
			$tmp = 	"<span id=\"err".$errori."\" class=\"red\">".$str."</span>";
			echo("<a href=\"#err".$errori."\" class=\"red\">".$errori." - ".$str."</a><br />");
		}
		return $tmp;
	}

	function uniord($ch) {
	
		$n = ord($ch{0});
	
		if ($n < 128) {
			return $n; // no conversion required
		}
	
		if ($n < 192 || $n > 253) {
			return false; // bad first byte || out of range
		}
	
		$arr = array(1 => 192, // byte position => range from
					 2 => 224,
					 3 => 240,
					 4 => 248,
					 5 => 252,
					 );
	
		foreach ($arr as $key => $val) {
			if ($n >= $val) { // add byte to the 'char' array
				$char[] = ord($ch{$key}) - 128;
				$range  = $val;
			} else {
				break; // save some e-trees
			}
		}
	
		$retval = ($n - $range) * pow(64, sizeof($char));
	
		foreach ($char as $key => $val) {
			$pow = sizeof($char) - ($key + 1); // invert key
			$retval += $val * pow(64, $pow);   // dark magic
		}
	
		return $retval;
	} 
	
	function surfFolders($handle, $folder,$myData) {
		while (($subFolderName = readdir($handle)) !== false) {
			if (is_file($folder.$subFolderName) != "1" && $subFolderName != "." && $subFolderName != ".." && $subFolderName != "warehouse" && $subFolderName != ".svn" && $subFolderName != "_notes" && $subFolderName != "thumb") {
				if ($handle2 = opendir($folder.$subFolderName)) {
					$myData = surfFiles($handle2, $folder.$subFolderName."/",$myData);
					//$myData.= $tmp[1];
				}
				if ($handle2 = opendir($folder.$subFolderName)) {
					$myData = surfFolders($handle2, $folder.$subFolderName."/",$myData);
				}
				closedir($handle2);
			}
		}
		if ($handle2 = opendir($folder)) {
			$myData = surfFiles($handle2, $folder,$myData);
			closedir($handle2);
		}
		return $myData;
	}
	function surfFiles($handle, $folder,$myData) {
		while (false !== ($file = readdir($handle))) {
			$myFile = $folder.$file;
			if (is_file($myFile) == "1" && $subFolderName != "." && $subFolderName != ".." && $subFolderName != "_notes" && $subFolderName != "warehouse" && $subFolderName != ".svn" && (strpos($file,".php")>0 || strpos($file,".js")>0)) {
				$cnt = file_get_contents($myFile);
				$arr = split('getLabel\("',$cnt);
				for ($a=1;$a<count($arr);$a++) {
					$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
					//$arr[$a] = $labelKeyStr;
					$myData[$labelKeyStr]['lab']=$labelKeyStr;
					$myData[$labelKeyStr]['url'][]=$myFile;
				}
				$arr = split('getPlainTextLabel\("',$cnt);
				for ($a=1;$a<count($arr);$a++) {
					$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
					//$arr[$a] = $labelKeyStr;
					$myData[$labelKeyStr]['lab']=$labelKeyStr;
					$myData[$labelKeyStr]['url'][]=$myFile;
				}
				$arr = split('myGetLabel\("',$cnt);
				for ($a=1;$a<count($arr);$a++) {
					$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
					//$arr[$a] = $labelKeyStr;
					$myData[$labelKeyStr]['lab']=$labelKeyStr;
					$myData[$labelKeyStr]['url'][]=$myFile;
				}
				$arr = split('getLangLabel\("',$cnt);
				for ($a=1;$a<count($arr);$a++) {
					$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
					//$arr[$a] = $labelKeyStr;
					$myData[$labelKeyStr]['lab']=$labelKeyStr;
					$myData[$labelKeyStr]['url'][]=$myFile;
				}
			}
		}
		return $myData;
	}



	function htmlnumericentities($str){
	  $enc_str = preg_replace('/[^!-#%<>\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
	  return $enc_str;
	}
	/*
	function addrizza($str){
		$str=htmlnumericentities($str);
		return str_replace(array("&#38;","&#13;&#10;"),array("&amp;",""),$str);
	}
	*/
	function addrizza($str){
		$new_str="";
		for($i=0;$i<strlen($str);$i++){
			//$new_str.="&#".ord(mb_substr($str,$i,1,'utf-8')).";";
			$c=mb_substr($str,$i,1,'utf-8');
			if(mb_detect_encoding($c)=="UTF-8"){
				$new_str.="&#".uniord($c).";";
			}else{
				$new_str.=$c;
			}
		}
		return str_replace("&#0;","",$new_str);
	}
	
	
	/* ============================================================= */
	
	
	
	
	if($_POST['submit']){
		if(@is_uploaded_file($_FILES["labelFile"]["tmp_name"])) {
			if(@move_uploaded_file($_FILES["labelFile"]["tmp_name"],$folderContainerSitePath."__generator/conf/private/label.csv")){
				chmod($folderContainerSitePath."__generator/conf/private/label.csv", 0644);
				$lines = file($folderContainerSitePath."__generator/conf/private/label.csv");
				$firstLine = explode("§",iconv("UTF-8", "ISO-8859-1",$lines[0]));
				$labels = array();
				for($a=1;$a<count($lines);$a++){
					$r=explode("§",$lines[$a]);
					for($b=2;$b<count($firstLine);$b++) {
						$labels[$firstLine[$b]].="
				<label>
					<key>".iconv("UTF-8", "ISO-8859-1",$r[1])."</key>
					<val><![CDATA[".rtrim(addrizza($r[$b]))."]]></val>
				</label>\n";
					}
				}
				echo("<ul>\n");
				echo("<li><h3>Risultati:</h3>\n");
				echo("<ul>\n");
				foreach($labels as $key => $val) {
					$xmlStr="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
					$xmlStr.="<root>\n";
					$xmlStr.=$val;
					$xmlStr.="</root>";
					file_put_contents($folderContainerSitePath."__generator/conf/private/".trim($key).".xml",$xmlStr);
					echo("<li><a href=\"private/".trim($key).".xml\">SCARICA FILE XML: ".trim($key).".xml</a></li>\n");	
				}
				echo("</ul>\n");
				echo("</li>\n");
				echo("<li><a href=\"?langA=".implode(",",$firstLine)."\">VERIFICA E SALVA</a></li>\n");
				echo("</ul>\n");
			}	
		}
	}elseif (isset($_GET['langA'])) {

		$firstLine = explode(",",$_GET['langA']);
		
		if (isset($_GET['save'])) {
			$now= date("Y-m-d_G-i-s");
			for($b=2;$b<count($firstLine);$b++) {
				echo("Muovi: <strong>".$folderContainerSitePath."__generator/conf/".$firstLine[$b].".xml</strong> in <strong>".$folderContainerSitePath."__generator/conf/backup/".$firstLine[$b]."_".$now.".xml</strong><br /><br />");
				rename($folderContainerSitePath."__generator/conf/".$firstLine[$b].".xml",$folderContainerSitePath."__generator/conf/backup/".$firstLine[$b]."_".$now.".xml");
				echo("Muovi: <strong>".$folderContainerSitePath."__generator/conf/private/".$firstLine[$b].".xml</strong> in <strong>".$folderContainerSitePath."__generator/conf/".$firstLine[$b].".xml</strong><br /><br />");
				rename($folderContainerSitePath."__generator/conf/private/".$firstLine[$b].".xml",$folderContainerSitePath."__generator/conf/".$firstLine[$b].".xml");
								
	
				/* AGGIUNTA */
				$dictionary=array();
				$xmlParser= new xmlParser();
				$xmlParser->parse($folderContainerSitePath."__generator/conf/".$firstLine[$b].".xml");
				foreach($xmlParser->struct[0][child] as $nodo){
					$dictionary[$nodo[child][0][data]]=$nodo[child][1][data];
				}
			
				$phpStr = var_export($dictionary, true);
				$phpStr="<?\n\$dictionary = ".$phpStr."; \n ?>";
				echo("Muovi: <strong>".$folderContainerSitePath."_php/conf/".$firstLine[$b].".php"."</strong> in <strong>".$folderContainerSitePath."__generator/conf/backup/".$firstLine[$b]."_".$now.".php</strong><br /><br />");				
				copy($folderContainerSitePath."_php/conf/".$firstLine[$b].".php",$folderContainerSitePath."__generator/conf/backup/".$firstLine[$b]."_".$now.".php");				
				file_put_contents($folderContainerSitePath."_php/conf/".$firstLine[$b].".php",$phpStr);	
				echo("Crea Oggetto php: <strong>".$folderContainerSitePath."_php/conf/".$firstLine[$b].".php</strong><br /><br />");
				
			}
		} else {
			$site=new site();
			$dictionary = array();
			for($b=2;$b<count($firstLine);$b++) {
				$dictionary[$firstLine[$b]] = array();
				$xmlParser= new xmlParser();
				$xmlParser->parseStr(file_get_contents($folderContainerSitePath."__generator/conf/private/".$firstLine[$b].".xml"));
				foreach($xmlParser->struct[0][child] as $nodo){
					$dictionary[$firstLine[$b]][$nodo[child][0][data]]=$nodo[child][1][data];
				}
			}
		
			$myData = array();
			
			if ($handle = opendir($folderContainerSitePath)) {
				$arr_confronto = surfFolders($handle, $folderContainerSitePath,$myData);
				closedir($handle);
			}
			//echo($str[0]);
			//echo("<hr />\n");
			/*$arr_confronto=array();
			$arr = split('getLabel\("',$str[1]);
			for ($a=1;$a<count($arr);$a++) {
				$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
				//$arr[$a] = $labelKeyStr;
				$arr_confronto[$labelKeyStr]=$labelKeyStr;
			}
			$arr = split('getPlainTextLabel\("',$str[1]);
			$str = "";
			for ($a=1;$a<count($arr);$a++) {
				$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
				//$arr[$a] = $labelKeyStr;
				$arr_confronto[$labelKeyStr]=$labelKeyStr;
			}
			$arr= array();*/
			//// BLOCCO COL DISTINCT CORRETTO
			$menuStr= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$menuStr.= "<tr>\n";
			$menuStr.= "<td><strong>ID</strong></td>\n";
			$menuStr.= "<td><strong>KEY</strong></td>\n";
			for($b=2;$b<count($firstLine);$b++) {
				$menuStr.= "<td><strong>".$firstLine[$b]."</strong></td>\n";
			}
			$menuStr.= "<td><strong>PAGES</strong></td>\n";
			$menuStr.= "</tr>\n";
			$conta=1;
			
			if ($_GET['sort']==1) {
				sort($arr_confronto);
			}
			$arr_confronto2 = array();
			foreach($arr_confronto as $aaaa) {
				$arr_confronto2[]=$aaaa['lab'];
				$menuStr.= "<tr>\n";
				$menuStr.= "<td valign=\"top\">".$conta."</td>\n";
				$menuStr.= "<td valign=\"top\">".$aaaa['lab']."</td>\n";
				for($b=2;$b<count($firstLine);$b++) {
					$tmp = getLabel($aaaa['lab'],$dictionary[$firstLine[$b]],$errori);
					if(strpos($tmp, "\"red\"")!==false) {
						$errori++;
					}
					$menuStr.= "<td valign=\"top\">".$tmp."</td>\n";
				}
				$menuStr.= "<td valign=\"top\" style=\"white-space:nowrap;\">".str_replace($folderContainerSitePath,"/",implode("<br />",$aaaa['url']))."</td>\n";
				$menuStr.= "</tr>\n";
				$conta++;
			}
			$menuStr.= "</table>\n";
			$menuStr.= "<hr />\n";
		
		
			$menuStr.= "<h1>LABEL PRESENTI MA NON TROVATE NEL CODICE PHP</h1>\n";
		
			$conta=1;
			$menuStr.= "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\">\n";
			$menuStr.= "<tr>\n";
			$menuStr.= "<td><strong>ID</strong></td>\n";
			$menuStr.= "<td><strong>KEY</strong></td>\n";
			for($b=2;$b<count($firstLine);$b++) {
				$menuStr.= "<td><strong>".$firstLine[$b]."</strong></td>\n";
			}
			$menuStr.= "</tr>\n";
		
			for($a=2;$a<count($firstLine);$a++) {
				foreach($dictionary[$firstLine[$a]] as $key => $val) {
					//echo($firstLine[$a]." - ".$key ." - ". $val."<br>");
					if (!in_array($key,$arr_confronto2)) {
						$menuStr.= "<tr>\n";
						$menuStr.= "<td valign=\"top\">".$conta."</td>\n";
						$menuStr.= "<td valign=\"top\">".$firstLine[$a]." - ".$key."</td>\n";
						for($b=2;$b<count($firstLine);$b++) {
							$tmp = getLabel($key,$dictionary[$firstLine[$b]],$errori);
							if(strpos($tmp, "\"red\"")!==false) {
								$errori++;
							}
							$menuStr.= "<td valign=\"top\">".$tmp."</td>\n";
						}
						$menuStr.= "</tr>\n";
						$conta++;
					}
				}
			}
			$menuStr.= "</table>\n";
			$menuStr.= "<hr />\n";
			$menuStr.= "<h3><a href=\"".$_SERVER['REQUEST_URI']."&save=true\">SAVE LABELS FILES</a></h3>\n";
			echo($menuStr);
		}
	} else {
	?>
	<h1>How to use it:</h1>
	<form method="post" action="<? echo($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data">
		<ol>
			<li>Modificare ed aggiungere le label nel file /_php/conf/siteLabels.xls</li>
			<li>Con openoffice calc esportare il file excel in csv con le sequenti opzioni
				<ul>
					<li>tipo di carattere: europa occidentale</li>
					<li>separatore di campo: &sect;</li>
					<li>separatore di testo: (lasciare in bianco)</li>
				</ul>
			</li>	
			<li>controlla che non che non ci siano accapi nel testo tranne quelli che definiscono la fine della riga del csv</li>
			<li>uploada il file generato qui:<br /><input type="file" name="labelFile" /> <input type="submit" name="submit" value="submit" /></li>
		</ol>
	</form>
		
	<?
	}		
	echo ("
		<script language=\"javascript\" type=\"text/javascript\">
			parent.attiva('".$_GET["campo"]."','".$_GET["site"]."');
		</script>
		");
?>
</body>
</html>