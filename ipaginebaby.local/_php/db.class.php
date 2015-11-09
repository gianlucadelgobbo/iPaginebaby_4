<?

Class db {

	var $link;
	var $database = "test";
	var $host = "localhost";//"172.16.251.233";
	var $user = "root";//"flyer_php";
	var $passwd = "db4fly3r";//"flyer!123_php";
	var $sql_list=array();
	var $err_msg;
	var $res;
	var $last_sql;

	function e($f) {
		if (mysql_errno()) {
			$this->err_msg = "Error in (".get_class($this)."->".$f."):: ".mysql_errno().": ".mysql_error()."\n";
			return false;
		}
		return true;
	}

	function connect() {
		$this->link = mysql_connect($this->host, $this->user, $this->passwd);
		if ($this->database) {
			mysql_select_db($this->database, $this->link);
		}
		return $this->e('connect');
	}

	function switch_db() {
		if ($this->database) {
			mysql_select_db($this->database, $this->link);
		}
		return $this->e('connect');
	}
	function query($sql) {
//		$this->sql_list[]=$sql;
		$this->last_sql=$sql;
		$this->res = mysql_query($sql, $this->link);
		return $this->e("sql error");
	}

	function fetch() {
		if($this->res){
			$a = array();
			while($r = mysql_fetch_object($this->res)) {
				$a[] = $r;
			}
			return $a;
		}//else{
		//	echo($this->last_sql);
		//}	
	}

	function fetch_xml_str($gettati=array()) {
		
		
		$a = array();
		$xml = "<?xml version=\"1.0\"?>\n<root>\n";
		if(!empty($gettati)) {
			while (list($key,$value)=each($gettati)) {
   			$xml.= "<get nome=\"$key\">".$value."</get>\n";
		}
		}
		$xml.="<recordset><num_rows>".mysql_num_rows($this->res)."</num_rows></recordset>\n";
		$xml.="<data>\n";
		while ($row = mysql_fetch_assoc($this->res)) {
			$xml .= "<nodo>\n";
			while(list($nodo,$valore)=each($row)){
				$xml .= "<campo nome=\"".$nodo."\">".$valore."</campo>\n";
			}
			$xml .= "</nodo>\n";		   
		}
		$xml.="</data>\n";				
		$xml .= "</root>";
		return $xml;
		
	}	

	function fetch_xml_str_files($gettati=array()) {
		
		
		$a = array();
		$xml = "<?xml version=\"1.0\"?>\n<root>\n";
		if(!empty($gettati)) {
			while (list($key,$value)=each($gettati)) {
   			$xml.= "<get nome=\"$key\">".$value."</get>\n";
		}
		}
		$xml.="<recordset><num_rows>".mysql_num_rows($this->res)."</num_rows></recordset>\n";
		$xml.="<data>\n";
		while ($row = mysql_fetch_assoc($this->res)) {
			$xml .= "<nodo>\n";
			while(list($nodo,$valore)=each($row)){
				$xml .= "<campo nome=\"".$nodo."\">".$valore."</campo>\n";
			}
			$xml .= "</nodo>\n";		   
		}
		
		$_sql_files = "SELECT id,tipo FROM tipi_file";
		
		$this->query($_sql_files);
		$res_file = $this->fetch();
		
		if ($res_file) {
			$xml.="<tipifile>\n"; 
			foreach ($res_file as $riga){
			$xml.= "<campo nome=\"".$riga->tipo."\">".$riga->id."</campo>\n";
			}
			$xml.="</tipifile>\n"; 
		} 
		
		$xml.="</data>\n";				
		$xml.= "</root>";
		
		return $xml;
		
	}


	function fetch_xml_obj() {
		$a = array();
		$doc = domxml_new_doc("1.0");
		$root = $doc->add_root("root");
		while ($row = mysql_fetch_assoc($this->res)) {
			$nodo = $root->new_child("nodo", "");
			while(list($nomeNodo,$valoreNodo)=each($row)){
				$elem=$nodo->new_child($nomeNodo, $valoreNodo);
			}
		}	
		return $doc;
	}

	function fetch_structure() {
		$_sql = "SHOW fields FROM $this->db.$this->table";
		$this->query($_sql);
		return $this->fetch();
	}

	function free_results() {
		mysql_free_result($this->res);
	}
	
	function dbEncode($str,$isHtml){
		if($isHtml){
			$str="<![CDATA[".$str."]]>";
		}else{
			$str="<![CDATA[".htmlentities($str)."]]>";
		}
		return $str;
	}	

	function mysql_string($str){
		$str=str_replace("\'","'",$str);
		$str=str_replace("'","''",$str);
		return $str;
	}	

	function mysql_string_strip_tags($str){
		$str=strip_tags($str, "<p><a><ol><ul><em><strong><li><br>");
		$str=str_replace("\'","'",$str);
		$str=str_replace("'","''",$str);
		return $str;
	}
	
	function xmlentities($string, $quote_style=ENT_QUOTES){
	   static $trans;
	   if (!isset($trans)) {
		   $trans = get_html_translation_table(HTML_ENTITIES, $quote_style);
		   foreach ($trans as $key => $value)
			   $trans[$key] = '&#'.ord($key).';';
		   // dont translate the '&' in case it is part of &xxx;
		   $trans[chr(38)] = '&';
	   }
	   // after the initial translation, _do_ map standalone '&' into '&#38;'
		$string =  preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&#38;" , strtr($string, $trans));
	   return str_replace(array("&#38;#"),array("&#"),$string);
	}
	function htmlnumericentities($str){
		$new_str="";
		for($i=0;$i<strlen($str);$i++){
			//$new_str.="&#".ord(mb_substr($str,$i,1,'utf-8')).";";
			$c=mb_substr($str,$i,1,'utf-8');
			if(mb_detect_encoding($c)=="UTF-8"){
				$new_str.="&#".$this->uniord($c).";";
			}else{
				$new_str.=$c;
			}
		}
		return str_replace(array("&#0;","&#38;#"),array("","&#"),$new_str);
	}
	function uniord($ch) {
		$n = ord($ch{0});
		if ($n < 128) {
			return $n; // no conversion required
		}
		if ($n < 192 || $n > 253) {
			return false; // bad first byte || out of range
		}
		$arr = array(1 => 192, 2 => 224, 3 => 240, 4 => 248, 5 => 252,);
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
	
	function htmlnumericentities_OLD_PER_ISO($str){
	  $enc_str = preg_replace('/[^!-#%<>\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
	  return str_replace("&#38;","&",$enc_str);
	}

	function fullUpper($str){
	   // convert to entities
	   $subject = htmlentities($str,ENT_QUOTES);
	   $pattern = '/&([a-z])(uml|acute|circ';
	   $pattern.= '|tilde|ring|elig|grave|slash|horn|cedil|th);/e';
	   $replace = "'&'.strtoupper('\\1').'\\2'.';'";
	   $result = preg_replace($pattern, $replace, $subject);
	   // convert from entities back to characters
	   $htmltable = get_html_translation_table(HTML_ENTITIES);
	   foreach($htmltable as $key => $value) {
		  $result = preg_replace(addslashes($value),$key,$result);
	   }
	   return(strtoupper($result));
	}
	function unhtmlentitiesISO($string) {
		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	function unhtmlentitiesUtf8($string) {
		// replace numeric entities
		$string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
		$string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string);
		// replace literal entities
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		// changing translation table to UTF-8
		foreach( $trans_tbl as $key => $value ) {
			$trans_tbl[$key] = iconv( 'ISO-8859-1', 'UTF-8', $value );
		}
		return strtr($string, $trans_tbl);
	}
	function unhtmlentities($string){
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		$trans_tbl["&Prime;"]="\"";
		$trans_tbl["&lsquo;"]="'";
		$trans_tbl["&rsquo;"]="'";
		$trans_tbl["&ndash;"]="-";		
		$trans_tbl["&hellip;"]="...";
		$trans_tbl["&gt;"]="&gt;";
		$trans_tbl["&lt;"]="&lt;";	
		return strtr($string, $trans_tbl);
	}

	function xmlStringEncode($str,$isHtml){
		if(!$isHtml){
			$str=htmlentities($str);
		}
		$str=$this->htmlnumericentities($str);
		$str="<![CDATA[".$str."]]>";
		return $str;
	}
	
	function attributeStringEncode($str){
		$str=strip_tags($str);
		$str=str_replace(array("\"","<",">")," ",$str);
		$str=str_replace(array("& "),"&amp; ",$str);
		$str=$this->htmlnumericentities($str);
		return $str;		
	}

	function attributeHTMLStringEncode($str){
		$str=strip_tags($str);
		$str=str_replace(array("\"","<",">","& "),array("&quot;","&lt;","&gt;","&amp; "),$str);
		return $str;		
	}

	function FormataData($sStr) {
     list ($sDay,$sMonth,$sYear) = split ('-', $sStr);
     $sStr = $sYear."-".$sMonth."-".$sDay;       
     return $sStr;
	}
	function stringAmpCheckAndReplace($str){
		$i=0;
		$lastpos=0;
		do {
			$pos = strpos($str,"&",$lastpos);
			if($pos!==false){
				$lastpos=$pos+1;
				$is_correct_entity=false;
				for($k=3;$k<8;$k++){
					if(strlen($str)>=$pos+$k){
						$endEntity=substr($str,$pos+$k,1);
						if($endEntity==";"){
							$is_correct_entity=true;
							break;
						}
					}
				}
				if(!$is_correct_entity)
					$str=substr($str,0,$pos)."&amp;".substr($str,$pos+1);
			}
		}while ($pos!==false);
		return $str;
	}	

	function getMLFiledValue($xmlfiledValue,$lng,$etichetta=false){
		$trovato="";
		$myparser = new xmlParser();
		$myparser->parseStr("<root>".$xmlfiledValue."</root>");
		if(isset($myparser->struct[0]["child"])){
			foreach($myparser->struct[0]["child"] as $lingue){
				if(isset($lingue["attrs"]["LANG"])){
					if($lingue["attrs"]["LANG"]==$lng){
						$trovato=(isset($lingue["data"]) ? $lingue["data"] : "");
					}
				}	
			}
		}	
		if(!$trovato){
			if(isset($myparser->struct[0]["child"])){
				foreach($myparser->struct[0]["child"] as $lingue ){
					if(isset($lingue["attrs"]["LANG"])){					
						if($lingue["attrs"]["LANG"]=="it"){
							$trovato=(isset($lingue["data"]) ? $lingue["data"] : "");
						}
					}	
				}		
			}	
		}
		return $trovato;	
	}
	
}

?>