<?
include_once("_php/site.class.php");
$site = new site();

$params = explode("/",$_GET['param']);

if(strlen($params[count($params)-1])==0) array_pop($params);

if(count($params)>0) {
	if($params[0]=="about-us" && count($params)==1) {
		/*
		$site->dataObj->db->query("select distinct(city) as city from places order by city");
		$res=$site->dataObj->db->fetch();
		$str = "<ul>";
		foreach($res as $row) {
			if ($row->city) $str.= "<li><a href=\"/".$site->dataObj->sanitize_title_with_dashes($row->city)."\">".$row->city."</a></li>";
		}
		$str.= "</ul>";
		*/
		$title = $site->dataObj->getLabel("Find by city");
		$str = "<h2 id=\"titleSearch\">".$params[0]."</h2>";
		$str.= "<p>testo sezione</p>";
		$inc = "web/inc/cnt.php";
	} else if($params[0]=="explore" && count($params)==1) {
		$site->dataObj->db->query("select distinct(city) as city from places order by city");
		$res=$site->dataObj->db->fetch();
		$title = $site->dataObj->getLabel("Find by city");
		$str = "<ul>";
		foreach($res as $row) {
			if ($row->city) $str.= "<li><a href=\"/".$site->dataObj->sanitize_title_with_dashes($row->city)."\">".$row->city."</a></li>";
		}
		$str.= "</ul>";
		$inc = "web/inc/explore.php";
	} else {
		$inc = "web/inc/explore.php";
		$sql = "select id,city from places where permalink like '".$params[0]."/%' limit 1";
		//print_r($sql);
		$site->dataObj->db->query($sql);
		$resCity=$site->dataObj->db->fetch();
		if($resCity) {
			$title = $resCity[0]->city;
			if(count($params)==1) {
				$sql = "select cat from places where permalink like '".$params[0]."/%'";
				//print_r($sql);
				$site->dataObj->db->query($sql);
				$res=$site->dataObj->db->fetch();
				$cat = array();
				foreach($res as $row) {
					if ($row->cat) {
						$tmp = explode(",",$row->cat);
						foreach($tmp as $k) {
							$tmp2 = explode("|",$k);
							if ($tmp2[2]) {
								if (!$cat[$tmp2[1]]) {
									$cat[$tmp2[1]] = array();
								}
								if (!$cat[$tmp2[1]][$tmp2[2]]) {
									$cat[$tmp2[1]][$tmp2[2]] = 1;
								} else {
									$cat[$tmp2[1]][$tmp2[2]] = $cat[$tmp2[1]][$tmp2[2]]+1;
								}
							} else if ($tmp2[1]) {
								$cat[$tmp2[1]] = array();
							}
						}
					}
				}
				ksort($cat);
				$str = "<ul>";
				foreach($cat as $k=>$v) {
					$conta = 0;
					$str2 = "<ul>";
					foreach($v as $kk=>$vv) {
						$conta = $conta+$vv;
						$str2.= "<li><a href=\"/".$params[0]."/category/".$kk."\">".$k." ".$site->dataObj->getNomeChiave($kk)." [".$vv."]</a></li>";
					}
					$str2.= "</ul>";
					$str.= "<li><a href=\"/".$params[0]."/category/".$k."\">".$k." ".$site->dataObj->getNomeChiave($k)." [".$conta."]</a>".$str2."</li>";
				}
				$str.= "</ul>";
			} else if (count($params)==2) {
				$site->dataObj->db->query("select * from places where permalink = '".$params[0]."/".$params[1]."' limit 1");
				$res=$site->dataObj->db->fetch();
	
				if($res) {
					$title = $res[0]->name;
					$str = $site->dataObj->fillDett($res[0]);
				} else {
					header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
					include_once("error.php");
					exit();
				}
			} else if (count($params)==3) {
				if($params[1]=="category") {
					$site->dataObj->db->query("select * from chiavi where id = '".$params[2]."' limit 1");
					$resCat=$site->dataObj->db->fetch();
					if($resCat) {
						$title = trim($title).": ".($resCat[0]->id_p ? $site->dataObj->getNomeChiave($resCat[0]->id_p)." / " : "").$resCat[0]->nome;
						$sql = "select * from places where permalink like '".$params[0]."/%' and cat like '%|".$params[2]."|%' order by hl desc";
						//print_r($sql);
						$site->dataObj->db->query($sql);
						$res=$site->dataObj->db->fetch();
						$str = "<ul>";
						foreach($res as $row) {
							$str.= "<li><a href=\"/".$row->permalink."\">".$row->name."</a></li>";
						}
						$str.= "</ul>";
						$str = $site->dataObj->placesSrc($res);
					} else {
						header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
						include_once("error.php");
						exit();
					}
				}
			} else {
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
				include_once("error.php");
				exit();
			}
		} else {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
			include_once("error.php");
			exit();
		}
	}
} else {
	$inc = "web/inc/home.php";
}
include_once("web/template.php");
?>
