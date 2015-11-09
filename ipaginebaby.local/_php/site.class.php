<?
session_start();
class site {
	
	function site($area="en"){
		$availableLang = array("it","en");
		$_SESSION['area'] = $this->area = (in_array($area, $availableLang) ? $area : "it");
		$this->siteUrl 		= "http://ipb.flyer.it";
		$this->site_path	= "/sites.local/ipaginebaby/";		// Cambiare anche in _php/php/getLanguageLabel.php e _php/php/includeManage.php

		include_once($this->site_path."_php/db.class.php");
		include_once($this->site_path."_php/dataObj.class.php");	

		include($this->site_path."_php/conf/".$this->area.".php");
		$this->dictionary=$dictionary;
		
		$initObj=array(
			"adminID"							=> array(),		   
			"database"							=>"paginebaby2_db", 
			"dictionary"						=>$this->dictionary, 
			"site_url"							=>$this->siteUrl, 
			"site_path"							=>$this->site_path, 
			"area"								=>$this->area, 
			"excludeRadiurLimit"				=> 5, 
			"searchLimit"						=> 20, 

			"sections"							=>array(
				"analytics"=>array(
					"it"=>"UA-8844617-6", "en"=>"UA-8844617-6", "es"=>"UA-8844617-6", "fr"=>"UA-8844617-6", "pl"=>"UA-8844617-6", "ru"=>"UA-8844617-6", "hu"=>"UA-8844617-6"
				),
				"contacts"=>array(
					"it"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"), 
					"en"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"),
					"es"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"), 
					"fr"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"), 
					"pl"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"),
					"ru"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER"), 
					"hu"=>array("fromEmail"=>"attivita@paginebaby.it", "fromName"=>"PagineBaby", "toEmail"=>"attivita@paginebaby.it", "toName"=>"PagineBaby", "subject"=>"Mail from FLxER") 
				),
				"abuse"=>array(
					"it"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"), 
					"en"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"),
					"es"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"), 
					"fr"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"), 
					"pl"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"),
					"ru"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER"),
					"hu"=>array("fromEmail"=>"abuse@flxer.net", "fromName"=>"PagineBaby", "toEmail"=>"abuse@flxer.net", "toName"=>"PagineBaby", "subject"=>"Abuse from FLxER") 
				),
				"terms"=>				54,
				"privacy"=>					54
			)
		);
		$this->dataObj=new dataObj($initObj);					
	}

		
	function out($mod="_php/modelli/modello_body.php"){
		include_once($this->site_path.$mod);
	}	

}
?>
