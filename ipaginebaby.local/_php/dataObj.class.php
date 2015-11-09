<?
class dataObj {
	function dataObj(&$initObj){
		$this->db=new db();
		$this->db->database=$initObj['database'];
		$this->db->connect();
		$this->initObj=$initObj;	
	}
	function getPermalinkStr($id,$tab){
		if($tab=="soggetti"){
			$sql="select login as permalink from soggetti where id=".$id;
		}else{
			$sql="select permalink from ".$tab." where id=".$id;
		}
		$this->db->query($sql);
		$pres=$this->db->fetch();
		$res=false;
		if($pres){
			$res=$pres[0]->permalink;
		}
		return $res;
	}
	
	function getPermalink($id,$tab,$city,$name,$step=0){
		$name=$this->sanitize_title_with_dashes($name);
		$city=$this->sanitize_title_with_dashes($city);
		if ($name=="" || $city=="") {
			$esiste=true;
		} else {
			//echo($p."<br /><br />");
			$sql="select count(id) as tot from ".$tab." where permalink='".$city."/".$name."' and id!=".$id;
			$this->db->query($sql);
			$pres=$this->db->fetch();
			$esiste=false;
			if($pres){
				if($pres[0]->tot!=0){
					$esiste=true;
				}
			}
		}
		if(!$esiste){
			return $city."/".$name;
		}else{
			if($step<10){
				$nomefile_arr = explode("_", $name);
				$ultimo_numero = array_pop($nomefile_arr);
				if (is_numeric($ultimo_numero)) {
					$ultimo_numero++;
					$new_nome=implode("",$nomefile_arr);			
				} else {
					$new_nome = $name;
					$ultimo_numero = "0";  
					
				}
				return $this->getPermalink($id,$tab,$city,$new_nome."_".$ultimo_numero,1);
			}else{
				return "";
			}
		}
	}	
// PERMALINK

	function sanitize_title_with_dashes($title) {
		// RUSSO
		$cyr = array("&#1072;", "&#1073;", "&#1074;", "&#1075;", "&#1076;", "&#1106;", "&#1077;", "&#1078;", "&#1079;", "&#1080;", "&#1112;", "&#1082;", "&#1083;", "&#1113;", "&#1084;", "&#1085;","&#1114;", "&#1086;", "&#1087;", "&#1088;", "&#1089;", "&#1090;", "&#1115;", "&#1091;", "&#1092;", "&#1093;", "&#1094;", "&#1095;", "&#1119;", "&#1096;","&#1040;", "&#1041;", "&#1042;", "&#1043;", "&#1044;", "&#1026;", "&#1045;", "&#1046;", "&#1047;", "&#1048;", "&#1032;", "&#1050;", "&#1051;", "&#1033;", "&#1052;", "&#1053;","&#1034;", "&#1054;", "&#1055;", "&#1056;", "&#1057;", "&#1058;", "&#1035;", "&#1059;", "&#1060;", "&#1061;", "&#1062;", "&#1063;", "&#1039;", "&#1064;");
    	$lat= array ("a", "b", "v", "g", "d", "d", "e", "z", "z", "i", "j", "k", "l", "lj", "m", "n", "nj", "o", "p","r", "s", "t", "c", "u", "f", "h", "c", "c", "dz", "s","A", "B", "B", "G", "D", "D", "E", "Z", "Z", "I", "J", "K", "L", "LJ", "M", "N", "NJ", "O", "P","R", "S", "T", "C", "U", "F", "H", "C", "C", "DZ", "S");
		for($i=0;$i<count($cyr);$i++){
			$title=str_replace(array($cyr[$i]),array($lat[$i]),$title);
		}
		// END RUSSO
		
		//echo($str." - 1<br />");
		//$str = html_entity_decode($str);
		$title = strip_tags($title);
		// Preserve escaped octets.
		$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
		// Remove percent signs that are not part of an octet.
		$title = str_replace('%', '', $title);
		// Restore octets.
		$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	
		/*
		$title = $this->remove_accents($title);
		if ($this->seems_utf8($title)) {
			if (function_exists('mb_strtolower')) {
				$title = mb_strtolower($title, 'UTF-8');
			}
			$title = $this->utf8_uri_encode($title, 200);
		}
		*/
		$title = strtolower($title);
		$title = preg_replace('/&.+?;/', '', $title); // kill entities
		$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
		$title = preg_replace('/\s+/', '-', $title);
		$title = preg_replace('|-+|', '-', $title);
		$title = trim($title, '-');
	
		return $title;
	}

	function traslateCat($res){
		for ($a=0;$a<count($res);$a++) {
			$r = $res[$a];	
			if($r->cat){
				$arrK=explode(",", $r->cat);
				$interestK = array();
				for($i=0;$i<Count($arrK);$i++){
					$arrKval=explode("|", $arrK[$i]);
					if (!$interestK[$arrKval[1]]) $interestK[$arrKval[1]] = $this->getLabel($this->getNomeChiave($arrKval[1]));
					if($arrKval[2]){
						$interestK[$arrKval[1]].= ", ".$this->getLabel($this->getNomeChiave($arrKval[2]));
					}
					/*
					if($arrKval[2]){
						$interestK[]=$this->getNomeChiave($arrKval[1])." / ".$this->getNomeChiave($arrKval[2]);
					}else{
						$interestK[]=$this->getNomeChiave($arrKval[1]);
					}
					*/				
				}
				$res[$a]->cat_str = implode(" | ",$interestK);
			}
			if($r->txt){
				$res[$a]->txtHtml = $this->makeTextPlainToRich($r->txt);
			}
			/*
			if($r->img){
				$res[$a]->img = $this->initObj['site_url']."/".$r->img;
			}
			*/
			if($r->hl){
				if(is_dir($this->initObj['site_path']."warehouse/".$r->id)){
					if ($handle = opendir($this->initObj['site_path']."warehouse/".$r->id)) {
					    /* This is the correct way to loop over the directory. */
					    while (false !== ($entry = readdir($handle))) {
						    if ($entry !== "." && $entry != ".." && substr($entry, strlen($entry)-3, 3)=="jpg") {
							    if ($entry == "logo.jpg") {
									$res[$a]->img = $this->initObj['site_url']."/warehouse/".$r->id."/".$entry;
							    } else {
							    	if (!$res[$a]->gallery) $res[$a]->gallery = array();
							        $res[$a]->gallery[] = array("src"=>$this->initObj['site_url']."/warehouse/".$r->id."/".$entry);
							    }
						    }
							//if ($entry == ".DS_Store") exec("rm ".$this->initObj['site_path']."warehouse/".$r->id."/".$entry);
							//if ($entry == ".DS_Store") echo("rm ".$this->initObj['site_path']."warehouse/".$r->id."/".$entry);
					    }
					
					    closedir($handle);
					}
				}
			}
		}
		return $res;
	}
	function nearMe($ll,$radius,$skip=0){
		$coo = explode(",",$ll);
		$lat = $coo[0];
		$lon = $coo[1];
		// SOLO CON IMMAGINE $sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING distance < 5000 and not img='' ORDER BY hl desc, distance asc LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";		
		$sqlCount = "SELECT count(id) AS tot FROM places WHERE public=1";
		$sqlCount = $sqlCount.($radius ? " and ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) < ".$radius." " : "");
		$this->db->query($sqlCount);
		//echo($sqlCount);
		$resCount=$this->db->fetch();
		if ($resCount[0]->tot>$this->initObj['excludeRadiurLimit'] || $radius==0) {
			$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING public=1";
			$sql = $sql.($radius ? " and distance < ".$radius." " : "")." ORDER BY hl desc, distance asc LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";
			//$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places;";
			$this->db->query($sql);
			//echo($sql);
	
			$res=$this->db->fetch();
			if ($resCount[0]->tot>count($res)) {
				$msg = (count($res)+$skip)." ".$this->getLabel("risultati su")." ".$resCount[0]->tot." ".$this->getLabel("vicino alla posizione impostata. Utilizza il tasto Cerca o le Categorie per una ricerca più mirata").".";
			} else if ($resCount[0]->tot==1) {
				$msg = (count($res)+$skip)." ".$this->getLabel("risultato vicino alla posizione impostata").".";
			} else if ($resCount[0]->tot) {
				$msg = (count($res)+$skip)." ".$this->getLabel("risultati vicino alla posizione impostata").".";
			} else {
				$msg = (count($res)+$skip)." ".$this->getLabel("risultati vicino alla posizione impostata, prova ad aumentare il raggio della ricerca").".";
			}
			$res = json_encode(array("msg"=>$msg,"tot"=>$resCount[0]->tot,"skip"=>($skip+$this->initObj['searchLimit']<$resCount[0]->tot ? $skip+$this->initObj['searchLimit'] : ""),"data"=>$this->traslateCat($res)));
			return $res;
		} else {
			return $this->nearMe($ll,0,$cat,$skip);
		}
		//insert into `iPagineBaby_db`.`places` ( `checkin`, `id`, `name`, `city`, `address`, `long`, `lat`) values ( '1', '0', 'Flyer communication', 'Roma', 'Via del Verano 39', '12.520055770874023', '41.899224357510526')
	}
	function getPlainTextLabel($str){
		$str=$this->community->db->unhtmlentitiesISO(array_key_exists($str, $this->initObj['dictionary']) ? $this->initObj['dictionary'][$str] : "_".$str."_" );
		$str=utf8_encode($str);
		return addslashes($str);
	}	

	function getLabel($str){
		return (array_key_exists($str, $this->initObj['dictionary']) ? $this->initObj['dictionary'][$str] : "_".$str."_" );
	}
	function searchPlaces($str,$ll,$radius,$cat,$skip=0){
		if ($ll) {
			$coo = explode(",",$ll);
			$lat = $coo[0];
			$lon = $coo[1];
			if ($cat=="pbcard") {
				$sqlCount = "SELECT count(id) AS tot FROM places WHERE public=1 AND pbcard=1";
				$sql 	  = "SELECT *				, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING public=1 AND  pbcard=1";
			} else if ($cat=="hl") {
				$sqlCount = "SELECT count(id) AS tot FROM places WHERE public=1 AND hl=1";
				$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING public=1 AND hl=1";
			} else if ($cat) {
				$sqlCount = "SELECT count(id) AS tot FROM places WHERE public=1 AND cat like '%|".$cat."|%'";
				$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING public=1 AND cat like '%|".$cat."|%'";
			} else {
				$sqlCount = "SELECT count(id) AS tot FROM places WHERE public=1 AND (name like '%".$str."%' OR address like '%".$str."%' OR txt like '%".$str."%')";
				$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING public=1 AND (name like '%".$str."%' OR address like '%".$str."%' OR txt like '%".$str."%')";
			}
		} else {
			if ($cat) {
				$sqlCount = "SELECT count(id) AS tot FROM places where public=1 and cat like '%|".$cat."|%';";
				$sql = "SELECT * FROM places where public=1 and cat like '%|".$cat."|%' ORDER BY name LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";
			} else {
				$sqlCount = "SELECT count(id) AS tot FROM places where public=1 and (name like '%".$str."%' OR address like '%".$str."%' OR txt like '%".$str."%');";
				$sql = "SELECT * FROM places where public=1 and (name like '%".$str."%' OR address like '%".$str."%' OR txt like '%".$str."%') ORDER BY name LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";
			}
		}
		$sql = $sql.($radius ? " and distance < ".$radius." " : "")." ORDER BY hl desc, distance asc LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";
		$sqlCount = $sqlCount.($radius ? " and ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) < ".$radius." " : "");
		$this->db->query($sqlCount);
		//echo($sqlCount);
		$resCount=$this->db->fetch();
		//echo($sql);
		if ($resCount[0]->tot<$this->initObj['excludeRadiurLimit'] && $radius && $str!="") {
			return $this->searchPlaces($str,$ll,0,$cat,$skip);
		} else {
			$this->db->query($sql);
			$res=$this->db->fetch();
			if ($radius==0) {
				if ($resCount[0]->tot>count($res)) {
					$msg = $this->getLabel("Nessun risultato risultato nella posizione impostata").". ".(count($res)+$skip)." ".$this->getLabel("risultati su")." ".$resCount[0]->tot." ".$this->getLabel("fuori dalla posizione impostata").".";
				} else if ($resCount[0]->tot==1) {
					$msg = $this->getLabel("Nessun risultato risultato nella posizione impostata").". ".(count($res)+$skip)." ".$this->getLabel("risultato fuori dalla posizione impostata").".";
				} else if ($resCount[0]->tot) {
					$msg = $this->getLabel("Nessun risultato risultato nella posizione impostata").". ".(count($res)+$skip)." ".$this->getLabel("risultati fuori dalla posizione impostata").".";
				} else {
					$msg = $this->getLabel("Nessun risultato risultato nella posizione impostata").". ".(count($res)+$skip)." ".$this->getLabel("risultati fuori dalla posizione impostata").".";
				}
			} else {
				if ($resCount[0]->tot>count($res)) {
					$msg = (count($res)+$skip)." ".$this->getLabel("risultati su")." ".$resCount[0]->tot." ".$this->getLabel("vicino alla posizione impostata").".";
				} else if ($resCount[0]->tot==1) {
					$msg = (count($res)+$skip)." ".$this->getLabel("risultato vicino alla posizione impostata").".";
				} else if ($resCount[0]->tot) {
					$msg = (count($res)+$skip)." ".$this->getLabel("risultati vicino alla posizione impostata").".";
				} else {
					$msg = (count($res)+$skip)." ".$this->getLabel("risultati vicino alla posizione impostata, prova ad aumentare il raggio della ricerca").".";
				}
			}
			$res = json_encode(array("msg"=>$msg,"tot"=>$resCount[0]->tot,"skip"=>($skip+$this->initObj['searchLimit']<$resCount[0]->tot ? $skip+$this->initObj['searchLimit'] : ""),"data"=>$this->traslateCat($res)));
			return $res;
		}
		//insert into `iPagineBaby_db`.`places` ( `checkin`, `id`, `name`, `city`, `address`, `long`, `lat`) values ( '1', '0', 'Flyer communication', 'Roma', 'Via del Verano 39', '12.520055770874023', '41.899224357510526')
	}
	function searchFav($uid){
		$sql = "SELECT places.* FROM places,favorities where favorities.uid=".$uid." AND favorities.pid=places.id";
		//echo($sql);
		$this->db->query($sql);
		$res=$this->db->fetch();
		$res = json_encode($this->traslateCat($res));
		return $res;
		//insert into `iPagineBaby_db`.`places` ( `checkin`, `id`, `name`, `city`, `address`, `long`, `lat`) values ( '1', '0', 'Flyer communication', 'Roma', 'Via del Verano 39', '12.520055770874023', '41.899224357510526')
	}
	function searchDett($id){
		$sql = "SELECT * FROM places WHERE id=".$id;
		$this->db->query($sql);
		$res=$this->db->fetch();
		$res = json_encode($this->traslateCat($res));
		return $res;
	}
	function logLogin($data){
		/*$coo = explode(",",$ll);
		$lat = $coo[0];
		$lon = $coo[1];
		// SOLO CON IMMAGINE $sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING distance < 5000 and not img='' ORDER BY hl desc, distance asc LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";		
		$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places HAVING distance < ".$radius." ORDER BY hl desc, distance asc LIMIT ".$skip." , ".$this->initObj['searchLimit'].";";
		$this->db->query($sql);
		$res=$this->db->fetch();
		*/
		$this->db->query("select id from users where fbid='".$data['id']."'");
		$res=$this->db->fetch();
		if (!$res) {
			$sql = "INSERT INTO users (fbid,email,name,date,date_last,fbObj) values ('".$data['id']."', '".$data['email']."', '".mysql_real_escape_string($data['name'])."', NOW(), NOW(),'".mysql_real_escape_string(json_encode($data))."');";
			$this->db->query($sql);
			$res = json_encode(array("newid"=>$data['id']));
		} else {
			$sql = "UPDATE users SET date_last=NOW() WHERE id=".$res[0]->id.";";
			$this->db->query($sql);
			$res = json_encode(array("oldid"=>$data['id']));
		}
		return $res;
		//insert into `iPagineBaby_db`.`places` ( `checkin`, `id`, `name`, `city`, `address`, `long`, `lat`) values ( '1', '0', 'Flyer communication', 'Roma', 'Via del Verano 39', '12.520055770874023', '41.899224357510526')
	}
	function makeRichTextToPlain($str){
		$str=trim($str);
		$str=str_replace("\r","\n",$str);
		$str=str_replace(array("<br>\n\n","<BR>\n\n","<br/>\n\n","<BR/>\n\n","<br />\n\n","<BR />\n\n"),"\n",$str);
		$str=str_replace(array("<br>\n","<BR>\n","<br/>\n","<BR/>\n","<br />\n","<BR />\n"),"\n",$str);
		$str=str_replace(array("<br>","<BR>","<br/>","<BR/>","<br />","<BR />"),"\n",$str);
		$str=str_replace("<li>","<li> • ",$str);
		//$str=str_replace(array("<strong>","<b>"),"###b###",$str);
		//$str=str_replace(array("</strong>","</b>"),"###/b###",$str);
		$str=str_replace("</","\n\n</",$str);
		$str=str_replace("<ul>","\n\n<ul>",$str);
		$str=str_replace("<ol>","\n\n<ol>",$str);
		$str=str_replace(array("\n\n</a","\n\n</span","\n\n</font","\n\n</b","\n\n</strong","\n\n</i","\n\n</em","\n\n</u"),array("</a","</span","</font","</b","</strong","</i","</em","</u"),$str);
		$str=preg_replace("/<a href=\"(.*)\">(.*)<\/a>/",'$2 [$1]',$str);
		$str=strip_tags($str);
		$str=$this->eraseTripleNl($str);
		$str=trim($str);
		return $str;	
	}

	function makeTextPlainToRich($str){
		$str=str_replace('"','&quot;',$str);
		$str=str_replace("###b###","<b>",$str);
		$str=str_replace("###/b###","</b>",$str);
		$str=preg_replace('((mailto:|(news|(ht|f)tp(s?))://){1}\S+)','<a href="\0" target="_blank">\0</a>',$str);
		//$str = preg_replace('/((http(s)?:\/\/)|(www\\.))((\w|\.)+)(\/)?(\S+)?/i','<a href="\0" target="_blank">\0</a>', $str);
		$str=str_replace("\r\n","<br />",$str);
		$str=str_replace("\n","<br />",$str);
		//$str=$this->eraseTripleBr($str);
		//$str=htmlspecialchars($str);
		//$str=htmlentities($str);
		return $str;	
	}

	function eraseTripleNl($str){
		$str=str_replace("\n\n\n","\n\n",$str);
		if (strpos($str,"\n\n\n")) $str=$this->eraseTripleNl($str);
		return $str;	
	}

/*
	function getChiaviArray($kstr) {
		$ka=explode(",", $kstr);
		$karr = array();
		foreach($ka as $arrK){
			$arrKval=explode("|", $arrK);
			if(!is_array($karr[$arrKval[1]])) $karr[$arrKval[1]] = array();
			if($arrKval[2]) $karr[$arrKval[1]][]=$arrKval[2];
		}
		return $karr;
	}

	function updateChiavi($id,$kstr) {
		$this->db->query("select cat from places where id=".$id);
		$preRes=$this->db->fetch();
		if($preRes){
			$k_old_str=$preRes[0]->cat;
			if ($k_old_str==$kstr) {
				$k_new_str = $kstr;
			} else {
				$k_new = $this->getChiaviArray($kstr);
				$k_old = $this->getChiaviArray($k_old_str);
				foreach($k_old as $k=>$v) {
					
				}
			}
		} else {
			$k_new_str = $kstr;
		}
		print_r($karr);
		//aggiorno le chiavi
		print_r($karr);
		foreach($karr as $k){
				$n_karr[]=$k;
		}
		$cat=implode(",",$n_karr);
		return $k_new_str;
	}
	*/
	function invia($efrom,$nfrom,$eto,$nto,$ogg,$testo){
		$nfrom=$this->makePlainText($nfrom);
		$nto=$this->makePlainText($nto);
		$ogg=$this->makePlainText($ogg);
		$testo=$this->makePlainText($testo);						
		if($nfrom){
			$mittente  = $nfrom." <".$efrom.">";
		}else{
			$mittente = $efrom;
		}

		if($nto){
			$destinatario  = $nto." <".$eto.">";
		}else{
			$destinatario = $eto;
		}
		$intestazioni="";
	/* intestazioni addizionali */
	//	$intestazioni .= "To: ".$destinatario."\r\n";
		$intestazioni .= "From: ".$mittente."\r\n";
	//	$intestazioni .= "Cc: \r\n";
		$intestazioni .= "Bcc: Gianluca Del Gobbo <g.delgobbo@flyer.it>";
		/* ed infine l'invio */
		 
		//if($eto=="info@flxer.net" || $eto=="abuse@flxer.net" || $eto=="g.delgobbo@flyer.it" || $eto=="g.delgobbo@gmail.com" || $efrom=="g.delgobbo@flyer.it" || $efrom=="g.delgobbo@gmail.com"){
			$somecontent="*------------- ".date("Y-m-d H:i:s")." ---------- \n";
			$somecontent.="fromN:".$nfrom." fromE:".$efrom." toN:".$nto." toE:".$eto."\n";
			$somecontent.="oggetto:".$ogg."\n";		
			$somecontent.="testo:".$testo."\n\n";				
			$somecontent.=var_export($_SERVER,true);				
			$somecontent.="\n\n\n";
			//$handle = fopen("/sites/flxer/maillog.txt", 'a');
			//fwrite($handle, $somecontent);
			//fclose($handle);
		//}
		return mail($destinatario, $ogg, $testo, $intestazioni);
	}
	
	function makePlainText($str){
		$str=str_replace(array("<br>","<BR>","<br/>","<BR/>"),"\n",$str);
		$str=strip_tags($str);
		return $str;	
	}
	function sendMessage($get){
		$eto = $this->initObj['sections']['contacts'][$this->initObj['area']]['toEmail'];
		$nto = $this->initObj['sections']['contacts'][$this->initObj['area']]['toName'];
		$ogg = "Messaggio dalle API PB";
		$msg = $get['pid']."\n\n".$get['message'];
		return $this->invia($get['email'],$get['name'],$eto,$nto,$ogg,$msg);
	}
	function deletePlace($get){
		$sql = "delete from places where id=".$get['id'];
		return json_encode(array("msg"=>$this->db->query($sql)));
	}
	function newPlace($get){
		$ll = explode(",",$get['ll']);
		$lat = $ll[0];
		$lon = $ll[1];
		if($get['pid']) {
			$sql = "update `places` set 
			name='".mysql_real_escape_string($get['name'])."', 
			permalink='".mysql_real_escape_string($this->getPermalink($get['pid'],"places",$get['city'],$get['name']))."', 
			txt='".mysql_real_escape_string($this->makeRichTextToPlain($get['txt']))."', 
			city='".mysql_real_escape_string($get['city'])."', 
			country='".mysql_real_escape_string($get['country'])."', 
			address='".mysql_real_escape_string($get['address'])."', 
			zip='".mysql_real_escape_string($get['zip'])."', 
			street_number='".mysql_real_escape_string($get['street_number'])."', 
			lat='".$lat."', 
			lng='".$lon."', 
			date_edit=NOW(), 
			website='".mysql_real_escape_string($get['website'])."', 
			tel='".mysql_real_escape_string($get['tel'])."', 
			email='".mysql_real_escape_string($get['email'])."', 
			cat='".(is_array($get['cat']) && count($get['cat']) ? mysql_real_escape_string(implode(",",$get['cat'])) : "")."'
			where id=".$get['pid'];
			$this->db->query($sql);
			//echo($sql);
			$placeId = $get['pid'];
		} else {
			$sql = "insert into `places` ( `name`, `txt`, `website`, `email`, `city`, `address`, `zip`, `street_number`, `country`, `lat`, `lng`, `author`, `checkin`, `date_new`, `date_edit`, `tel`, `cat`, `permalink` ) values ( '".mysql_real_escape_string($get['name'])."', '".mysql_real_escape_string($this->makeRichTextToPlain($get['txt']))."', '".mysql_real_escape_string($get['website'])."', '".mysql_real_escape_string($get['email'])."', '".mysql_real_escape_string($get['city'])."', '".mysql_real_escape_string($get['address'])."', '".mysql_real_escape_string($get['zip'])."', '".mysql_real_escape_string($get['street_number'])."', '".mysql_real_escape_string($get['country'])."', '".$lat."', '".$lon."', '".mysql_real_escape_string($get['author'])."', '1', NOW(), NOW(), '".mysql_real_escape_string($get['tel'])."', '".(is_array($get['cat']) && count($get['cat']) ? mysql_real_escape_string(implode(",",$get['cat'])) : "")."','".mysql_real_escape_string($this->getPermalink($get['pid'],"places",$get['city'],$get['name']))."')";
			$this->db->query($sql);
			//echo($sql);
			$placeId = mysql_insert_id();

			$sql = "insert into `checkin` ( `uid`, `pid`, `date`) values ( '".$get['author']."', '".$placeId."', NOW())";
			$this->db->query($sql);
			//echo($sql);
		}
		$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places where id=".$placeId;
		//echo($sql);
		$this->db->query($sql);
		$res=$this->db->fetch();
		return json_encode($this->traslateCat($res));
		//return $placeId;
	}
	function savePlace($get){
		$ll = explode(",",$get['ll']);
		$lat = $ll[0];
		$lon = $ll[1];
		if($get['pid']) {
			$sql = "update `places` set 
			name='".mysql_real_escape_string($get['name'])."', 
			permalink='".mysql_real_escape_string($this->getPermalink($get['pid'],"places",$get['city'],$get['name']))."', 
			txt='".mysql_real_escape_string($this->makeRichTextToPlain($get['txt']))."', 
			city='".mysql_real_escape_string($get['city'])."', 
			country='".mysql_real_escape_string($get['country'])."', 
			address='".mysql_real_escape_string($get['address'])."', 
			zip='".mysql_real_escape_string($get['zip'])."', 
			street_number='".mysql_real_escape_string($get['street_number'])."', 
			lat='".$lat."', 
			lng='".$lon."', 
			date_edit=NOW(), 
			website='".mysql_real_escape_string($get['website'])."', 
			email='".mysql_real_escape_string($get['email'])."', 
			cat='".(is_array($get['cat']) && count($get['cat']) ? mysql_real_escape_string(implode(",",$get['cat'])) : "")."', 
			tel='".mysql_real_escape_string($get['tel'])."', 
			mtel='".mysql_real_escape_string($get['mtel'])."', 
			fax='".mysql_real_escape_string($get['fax'])."', 
			img='".mysql_real_escape_string($get['img'])."', 
			hl='".mysql_real_escape_string($get['hl'])."', 
			public='".mysql_real_escape_string($get['public'])."', 
			new='".mysql_real_escape_string($get['new'])."', 
			pbcard='".mysql_real_escape_string($get['pbcard'])."'
			
			where id=".$get['pid'];
			$this->db->query($sql);
			$placeId = $get['pid'];
		} else {
			$sql = "insert into `places` ( `name`, `txt`, `website`, `email`, `city`, `address`, `zip`, `street_number`, `country`, `lat`, `lng`, `author`, `checkin`, `date_new`, `date_edit`
			, `cat` 
			, `tel`
			, `mtel`
			, `fax`
			, `img`
			, `hl`
			, `pbcard`
			, `permalink`
			) values ( '".mysql_real_escape_string($get['name'])."', '".mysql_real_escape_string($this->makeRichTextToPlain($get['txt']))."', '".mysql_real_escape_string($get['website'])."', '".mysql_real_escape_string($get['email'])."', '".mysql_real_escape_string($get['city'])."', '".mysql_real_escape_string($get['address'])."', '".mysql_real_escape_string($get['zip'])."', '".mysql_real_escape_string($get['street_number'])."', '".mysql_real_escape_string($get['country'])."', '".$lat."', '".$lon."', '".mysql_real_escape_string($get['author'])."', '1', NOW(), NOW()
			".(is_array($get['cat']) && count($get['cat']) ? ", '".mysql_real_escape_string(implode(",",$get['cat']))."' " : ", ''")."
			, '".mysql_real_escape_string($get['tel'])."' 
			, '".mysql_real_escape_string($get['mtel'])."' 
			, '".mysql_real_escape_string($get['fax'])."' 
			, '".mysql_real_escape_string($get['img'])."' 
			, '".mysql_real_escape_string($get['hl'])."' 
			, '".mysql_real_escape_string($get['pbcard'])."',
			, '".mysql_real_escape_string($this->getPermalink(0,"places",$get['city'],$get['name']))."')";
			$this->db->query($sql);
			//echo($sql);
			$placeId = mysql_insert_id();
		}
		$sql = "SELECT *, ( 6371 * acos( cos( radians(".$lat.") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".$lon.") ) + sin( radians(".$lat.") ) * sin( radians( lat ) ) ) ) AS distance FROM places where id=".$placeId;
		//echo($sql);
		$this->db->query($sql);
		$res=$this->db->fetch();
		return json_encode($this->traslateCat($res));
		//return $placeId;
	}
	function ratePlace($get){
		$rate = $get['rate'];
		//$media = ($get['sugo']+$get['frittura'])/2;
		$sql = "select id from rates where uid=".$get['uid']." and pid=".$get['pid']."";
		$this->db->query($sql);
		$res=$this->db->fetch();
		if ($res) {
			$sql = "update rates set rate='".$rate."' where uid=".$get['uid']." and pid=".$get['pid']."";
		} else {
			$sql = "insert into `rates` ( `uid`, `pid`, `rate`, `date`) values ( '".$get['uid']."', '".$get['pid']."', '".$rate."', NOW())";
		}
		$this->db->query($sql);
		$sql = "select sum(rate)/count(id) as val from rates where pid=".$get['pid'];
		$this->db->query($sql);
		$res=$this->db->fetch();
		if ($res) {
			$sql = "update places set rate=".$res[0]->val." where id=".$get['pid'];
		}
		$this->db->query($sql);
		return json_encode($res[0]);
	}
	function checkinPlace($get){
		$data = array();
		$sql = "select id from checkin where uid=".$get['uid']." and pid=".$get['pid']." and date(NOW())=date(date)";
		//$sql = "select id from checkin where uid=".$get['uid']." and pid=".$get['pid']."";
		$data['sql'] = $sql;
		$this->db->query($sql);
		$res=$this->db->fetch();
		if ($res) {
			$data['err'] = "Hai gia effettuato il checkin qui oggi";
			return json_encode($data);
		} else {
			$sql = "insert into `checkin` ( `uid`, `pid`, `date`) values ( '".$get['uid']."', '".$get['pid']."', NOW())";
			//$sql = "insert into `checkin` ( `uid`, `pid`) values ( '".$get['uid']."', '".$get['pid']."')";
			$data['sql'].= "\n".$sql;
			$this->db->query($sql);
			$sql = "update places set checkin=(checkin+1) where id=".$get['pid'];
			$data['sql'].= "\n".$sql;
			$this->db->query($sql);
			return json_encode($data);
		}
	}
	function favPlace($get){
		$data = array();
		$sql = "select id from favorities where uid=".$get['uid']." and pid=".$get['pid']."";
		//$sql = "select id from checkin where uid=".$get['uid']." and pid=".$get['pid']."";
		$data['sql'] = $sql;
		$this->db->query($sql);
		$res=$this->db->fetch();
		if ($res) {
			$sql = "delete from favorities where uid=".$get['uid']." and pid=".$get['pid']."";
			//$sql = "select id from checkin where uid=".$get['uid']." and pid=".$get['pid']."";
			$data['sql'] = $sql;
			$this->db->query($sql);
			$data['err'] = "Rimosso i preferiti";
		} else {
			$sql = "insert into `favorities` ( `uid`, `pid`, `date`) values ( '".$get['uid']."', '".$get['pid']."', NOW())";
			$data['sql'].= "\n".$sql;
			$this->db->query($sql);
		}
		return json_encode($data);
	}
	function getNomeChiave($idk){
		$this->db->query("select * from chiavi where id=".$idk." order by nome");
		$res= $this->db->fetch();
		if($res)
			return $res[0]->nome;
		else
			return "";	
	}
function placesSrc($data) {
	$str = "";
	$data = $this->traslateCat($data);
	if ($data && count($data)) {
		for ($i = 0; $i < count($data); $i++) {
			$str.= '<li class="arrow'.($data[$i]->hl==1 ? ' hl' : '').'">';
			$str.= '<a href="#ipbdett" onclick="fillDett('.i.'); return false;">';
			if ($data[$i]->img) {
				$str.= '<span class="imgListCnt"><img src="'.$data[$i]->img.'" align="left" height="40" class="imgList" /></span>';
			}
			$str.= '<span class="txtListCnt"><span class="titList">'.$data[$i]->name.' <br /></span>';
			if ($data[$i]->pbcard==1) {
				$str.= '<span class="pbcListCnt">PB-CARD</span> ';
			}
			if ($data[$i]->distance) {
				$str.= "<span class=\"stitTitList\">".$this->getLabel("Distanza").": </span> <span class=\"stitList\">".(round($data[$i]->distance*100)/100)." Km</span> ";
			}
			if ($data[$i]->cat_str) $str.= "<span class=\"stitTitList\">".$this->getLabel("Categorie").": </span> <span class=\"stitList\">".$data[$i]->cat_str."</span> ";
			$str.= '</span> ';
			$str.= '</a>';
			/*$str.= '<small class="counter">'.$data[$i].checkin.'</small>';*/
			$str.= '</li>';
		}
	} else {
		$str.= "<li>".$this->getLabel("Nessun risultato")."<br />".$this->getLabel("Se non trovi risultati prova ad aumentare il raggio della ricerca dalle Impostazioni. Oppure fai di meglio: utilizza la funzione Aggiungi per inserire le strutture che conosci")."</li>";
	}
	return $str;
}
function fillDett($row) {
	$str = "<div class=\"dettCntMain\">";
	if ($row->img) $str.= "<p class=\"imgCnt\"><img src=\"".$row->img."\" /></p>";
	if ($row->gallery) $str.= "<p><a class=\"galleryButton\" href=\"#photo\" onclick=\"generateGallery(".i.");return false;\">".$this->getLabel("Gallery")."</a></p>";
	if ($row->img && $row->gallery) $str.= "<hr />";
	/*
	$row->gallery = [
		  {src:"http://www.nasa.gov/images/content/450090main_image_1653_946-710.jpg",width: 946,height: 710},
		  {src:"http://www.thinkgeek.com/images/products/front/b597_rock_paper_scissors_lizard_spock.jpg"}
	];
	*/
	$str.= "<h3>".$row->name."</h3>";
	$str.= "<p>".$row->address.($row->street_number ? " ".$row->street_number : "").", ".$row->zip." - ".$row->city."</p>";
	if ($row->pbcard==1) $str.= "<p><span class=\"pbcListCnt\">PB-CARD</span></p>";
	if ($row->distance) $str.= "<p><span class=\"stitTit\">".$this->getLabel("Distanza").": </span> ".(floor($row->distance*100)/100)." km</p>";
	$str.= "<div><div class=\"stitTit left\">".$this->getLabel("Voto").": </div> <div id=\"r1\" class=\"rate_widget\"><div class=\"star_1 ratings_stars\"></div><div class=\"star_2 ratings_stars\"></div><div class=\"star_3 ratings_stars\"></div><div class=\"star_4 ratings_stars\"></div><div class=\"star_5 ratings_stars\"></div></div><br class=\"myClear\" /></div>";
	$str.= "<p><span class=\"stitTit\">".$this->getLabel("Checkins").": </span> ".$row->checkin."</p>";
	if ($row->tel) $str.= "<p><span class=\"stitTit\">".$this->getLabel("Tel").": </span> <a href=\"tel:".$row->tel."\">".$row->tel."</a></p>";
	if ($row->fax) $str.= "<p><span class=\"stitTit\">".$this->getLabel("Fax").": </span> ".$row->fax."</p>";
	if ($row->email) $str.= "<p><span class=\"stitTit\">".$this->getLabel("Email").": </span> <a href=\"mailto:".$row->email."\">".$row->email."</a></p>";
	if ($row->website) $str.= "<p class=\"website\"><span class=\"stitTit\">".$this->getLabel("Sito web").": </span> <a href=\"".$row->website."\" target=\"_blank\">".$row->website."</a></p>";
	if ($row->cat) $str.= "<p class=\"catcat\"><span class=\"stitTit\">".$this->getLabel("Categorie").": </span> ".$row->cat_str."</p>";
	/*$str.= "<p>categories:</p>";
	for (var a = 0; a < places[i].categories.length; a..) {
		$str.= "<p>".places[i].categories[a].name."</p>";
	}*/
	$str.= "<hr />";
	$str.= "<div class=\"txt\">".$row->txtHtml."<br class=\"myClear\" /></div>";
	//$str.= "<p>tipCount: ".$row->stats['tipCount."</p>";
	//$str.= "<p>usersCount: ".$row->stats['usersCount."</p>";
	$str.= "<hr />";
	$str.= "<div id=\"mappaDett\"><img alt=\"".$this->getLabel("Mappa di").": ".$row->name."\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x200&maptype=roadmap&markers=color:red|color:red|label:S|".$row->lat.",".$row->lng."&sensor=false\" /></div>";
	$str.= "<hr />";
	/*
	$row->gallery = [
		  {src:"http://www.nasa.gov/images/content/450090main_image_1653_946-710.jpg",width: 946,height: 710},
		  {src:"http://www.thinkgeek.com/images/products/front/b597_rock_paper_scissors_lizard_spock.jpg"}
	];
	jQT.generateGallery("photo", $row->gallery,{defaultIndex:0});
	$str.= "<hr />";
	*/
	$str.= "<ul class=\"individual\">";
	//console.log(fava);
		$str.= "<li><a class=\"greenButton\" rel=\"external\" target=\"_blank\" href=\"#\" id=\"openmaps\">".$this->getLabel("ARRIVA QUI")."</a></li>";
		$str.= "<li><a class=\"greenButton\" onclick=\"$('#pid2').val(".$row->id.");\" href=\"#propietario\">".$this->getLabel("SEGNALA")."</a></li>";

	if ($fava && in_array($row->id,$fava)!=-1) {
		$str.= "<li><a class=\"greenButton\" onclick=\"favorities(".$row->id.");\" href=\"#\">".$this->getLabel("RIMUOVI DAI PREFERITI")."</a></li>";
	} else {
		$str.= "<li><a class=\"greenButton\" onclick=\"favorities(".$row->id.");\" href=\"#\">".$this->getLabel("AGGIUNGI AI PREFERITI")."</a></li>";
	}
	$str.= "<li><a class=\"greenButton\" onclick=\"checkin(".$row->id.");\" href=\"#\">".$this->getLabel("CHECK-IN")."</a></li>";
	//$str.= "</ul>";
	//$str.= "<ul class=\"individual\">";
	$str.= "<li><a class=\"greenButton\" onclick=\"rate(".$row->id.");\" href=\"#rate\">".$this->getLabel("VOTA")."</a></li>";
	//console.log($('#uid').val());
	if ($row->author==$uid) {
		$str.= "<li><a class=\"greenButton\" onclick=\"onEdit(".i.");\" href=\"#aggiungi\">".$this->getLabel("MODIFICA")."</a></li>";
	} else {
		$str.= "<li><a class=\"greenButton\" onclick=\"$('#pid2').val(".$row->id.");\" href=\"#propietario\">".$this->getLabel("SEI IL TITOLARE")."?</a></li>";
	}
	$str.= "</ul>";
	$str.= "</div>";
	return $str;
}
}
?>
