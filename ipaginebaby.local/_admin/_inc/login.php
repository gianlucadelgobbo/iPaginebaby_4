<?php
class Login {
	var $lang;
	function init() {
		global $_TRANSLATIONS;
		if(isset($_GET['lang']) && isset($_TRANSLATIONS[$_GET['lang']]))
			$this->lang = $_GET['lang'];
		else
			$this->lang = GateKeeper::getConfig("lang");
	}
	public static function getLangString($stringName, $lang) {
		global $_TRANSLATIONS;
		if(isset($_TRANSLATIONS[$lang]) && is_array($_TRANSLATIONS[$lang]) 
			&& isset($_TRANSLATIONS[$lang][$stringName]))
			return $_TRANSLATIONS[$lang][$stringName];
		else if(isset($_TRANSLATIONS["en"]))// && is_array($_TRANSLATIONS["en"]) 
			//&& isset($_TRANSLATIONS["en"][$stringName]))
			return $_TRANSLATIONS["en"][$stringName];
		else
			return "Translation error";
	}
	function getString($stringName) {
		return Login::getLangString($stringName, $this->lang);
	}
}

$Login = new Login();
if(!GateKeeper::isUserLoggedIn()) { ?>
<div id="login">
	<form enctype="multipart/form-data" action="/_admin/" method="post">
		<div><label for="user_name"><?php print $Login->getString("username"); ?>:</label>
		<input type="text" name="user_name" value="" id="user_name" /></div>
		<div><label for="user_pass"><?php print $Login->getString("password"); ?>:</label>
		<input type="password" name="user_pass" id="user_pass" /></div>
		<div><input type="submit" value="<?php print $Login->getString("log_in"); ?>" class="button" /></div>
	</form>
</div>
<?php } ?>
