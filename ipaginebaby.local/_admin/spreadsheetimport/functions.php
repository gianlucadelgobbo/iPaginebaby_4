<?	

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

	function surfFolders($handle, $folder,$myData) {
		while (($subFolderName = readdir($handle)) !== false) {
			if (is_file($folder.$subFolderName) != "1" && $subFolderName != "." && $subFolderName != ".." && $subFolderName != "warehouse" && $subFolderName != ".svn" && $subFolderName != "_notes" && $subFolderName != "_admin" && $subFolderName != "thumb") {
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
			if (is_file($myFile) == "1" && $subFolderName != "." && $subFolderName != ".." && $subFolderName != "_notes" && $subFolderName != "warehouse" && $subFolderName != ".svn" && (strpos($file,".php")>0 || strpos($file,".js")>0 || strpos($file,".htm")>0)) {
				$cnt = file_get_contents($myFile);
				$arr = explode('getLabel\("',$cnt);
				for ($a=1;$a<count($arr);$a++) {
					$labelKeyStr=substr($arr[$a] ,0,strpos($arr[$a],'"',1));
					//$arr[$a] = $labelKeyStr;
					$myData[$labelKeyStr]['lab']=$labelKeyStr;
					$myData[$labelKeyStr]['url'][]=$myFile;
				}
				$arr = explode('getLabel("',$cnt);
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
?>
