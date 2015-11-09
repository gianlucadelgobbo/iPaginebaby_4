<?
	$skip=$_GET['skip']=(isset($_GET['skip']) ? $_GET['skip'] : 0);
	$limit=$_GET['limit']=(isset($_GET['limit']) ? $_GET['limit'] : 50);
	$_GET['cat']=(!isset($_GET['cat']) ? array() : $_GET['cat']);

	// CHIAVI
	$site->dataObj->db->query("select * from chiavi");
	$rres=$site->dataObj->db->fetch();
	if($rres){
		$cat = array();
		$catJS = array();
		foreach($rres as $row) {
			if ($row->id_p) {
				$cat[$row->id_p]['items'][] = array("id"=>$row->id,"name"=>$row->nome);
				$catJS[$row->id_p]['items'][] = array("id"=>$row->id,"name"=>"getLabel('".$row->nome."')");
			} else {
				$cat[$row->id]['title'] = $row->nome;
				$catJS[$row->id]['title'] = "getLabel('".$row->nome."')";
			}			
		}
		$catStr = "<ul id=\"cat\">\n";
		foreach($cat as $k=>$v) {
			$catStr.= "	<li class=\"nowrap left mr10\">";
			$selStr.= "	<select id=\"cat".$k."\" name=\"cat".$k."\">\n";
			$selStr.= "		<option>".$v['title']."</option>\n";
			$catStr.= "<input type=\"checkbox\" onchange=\"\$('#box".$k."').toggle();\" id=\"id".$k."\" name=\"cat[]\" value=\"".$k."\" ".(in_array($k,$_GET['cat']) ? "checked=\"checked\"" : "")." /> <label for=\"id".$k."\">".$v['title']."</label>";
			if ($v['items']) {
				$catStr.= "\n		<ul id=\"box".$k."\"".(in_array($k,$_GET['cat']) ? "" : " style=\"display:none;\" ").">\n";
				foreach($v['items'] as $vv) {
					$catStr.= "			<li><input type=\"checkbox\" id=\"id".$vv['id']."\" name=\"cat[]\" value=\"".$vv['id']."\" ".(in_array($vv['id'],$_GET['cat']) ? "checked=\"checked\"" : "")." /> <label for=\"id".$vv['id']."\">".$vv['name']."</label></li>\n";
					$selStr.= "		<option value=\"".$vv['id']."\">".$vv['name']."</option>\n";
				}
				$catStr.= "		</ul>\n";
			}
			$catStr.= "	</li>\n";
			$selStr.= "	</select>\n";
		}
		$catStr.= "</ul>\n";
	}

	// CITTA
	$site->dataObj->db->query("select DISTINCT(city) as city from places");
	$cres=$site->dataObj->db->fetch();
	$cittaListSearch = "";
	if($cres){
		foreach($cres as $row) {
			$cittaListSearch.= "<option value=\"".$row->city."\" ".($_GET['city']==$row->city ? "selected=\"selected\"" : "").">".$row->city."</option>\n";
		}
	}

	/*
	$salaList = "";
	$salaListSearch = "";
	$site->dataObj->db->query("select * from chiavi where id_p=".$salaBase);
	$rres=$site->dataObj->db->fetch();
	if($rres){
		foreach($rres as $row) {
			$salaListSearch.= "
				<option value=\"".$salaBase."|".$row->id."\" ".($_GET['chiavi3']==$salaBase."|".$row->id ? " selected=\"selected\"" : "").">".$row->nome."</option>";
			$salaListShow.= "
				<label for=\"room".$row->id."\">|<input type=\"checkbox\" value=\"1\"".($_GET["room".$row->id]==1 ? " checked=\"checked\"" : "")." name=\"room".$row->id."\" id=\"room".$row->id."\"> ".$row->nome."</label>";
		}
	}
	*/
	
	$selStr = "";
	
	$search = array();
	$search[1] = "
			<fieldset class=\"nowrap left mr10\">
				<legend><label for=\"confirm\">Categories</label></legend>
				".$catStr."
				".$selStr."
			</fieldset>
			<fieldset class=\"nowrap left mr10\">
				<legend><label for=\"city\">City</label></legend>
				<select name=\"city\" id=\"city\">
					<option value=\"0\">Any City</option>
					".$cittaListSearch."
				</select>
			</fieldset>
			<fieldset class=\"nowrap left mr10\">
				<legend><label>Text search</label></legend>
				<p><input type=\"text\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" /> <label for=\"id\">ID</label></p>
				<p><input type=\"text\" name=\"name\" id=\"name\" value=\"".$_GET['name']."\" /> <label for=\"name\">Name</label></p>
				<p><input type=\"text\" name=\"txt\" id=\"txt\" value=\"".$_GET['txt']."\" /> <label for=\"txt\">Txt</label></p>
				<p><input type=\"text\" name=\"notes\" id=\"notes\" value=\"".$_GET['notes']."\" /> <label for=\"notes\">Notes</label></p>
				<input type=\"checkbox\" name=\"lat\" id=\"lat\" value=\"0\" ".($_GET['lat']=="0" ? "checked=\"checked\"" : "")." /> <label for=\"lat\">NO LATITUDE</label>
				<input type=\"checkbox\" name=\"lng\" id=\"lng\" value=\"0\" ".($_GET['lng']=="0" ? "checked=\"checked\"" : "")." /> <label for=\"lng\">NO LONGITUDE</label>
				<br />
				<input type=\"checkbox\" name=\"hl\" id=\"hl\" value=\"1\" ".($_GET['hl']=="1" ? "checked=\"checked\"" : "")." /> <label for=\"hl\">Primopiano</label>
				<input type=\"checkbox\" name=\"pbcard\" id=\"pbcard\" value=\"1\" ".($_GET['pbcard']=="1" ? "checked=\"checked\"" : "")." /> <label for=\"pbcard\">PB Card</label>
				<br />
				<input type=\"checkbox\" name=\"public\" id=\"public\" value=\"1\" ".($_GET['public']=="1" ? "checked=\"checked\"" : "")." /> <label for=\"public\">Public</label>
				<input type=\"checkbox\" name=\"new\" id=\"new\" value=\"1\" ".($_GET['new']=="1" ? "checked=\"checked\"" : "")." /> <label for=\"new\">New</label>
			</fieldset>
			<fieldset class=\"nowrap left mr10\">
				<legend><label>Options</label></legend>
				<p><input type=\"text\" name=\"skip\" id=\"skip\" value=\"".$_GET['skip']."\" /> <label for=\"skip\">Skip</label></p>
				<p><input type=\"text\" name=\"limit\" id=\"txt\" value=\"".$_GET['limit']."\" /> <label for=\"limit\">Limit</label></p>
			</fieldset>
			";
	$searchBlock[2] = "
			<fieldset class=\"nowrap left mr10\">
				<legend><label>Options</label></legend>
				<p><input type=\"text\" name=\"skip\" id=\"skip\" value=\"".$_GET['skip']."\" /> <label for=\"skip\">Skip</label></p>
				<p><input type=\"text\" name=\"limit\" id=\"txt\" value=\"".$_GET['limit']."\" /> <label for=\"limit\">Limit</label></p>
				<p><input type=\"checkbox\" name=\"fbObj\" id=\"fbObj\" value=\"1\"".($_GET['fbObj']==1 ? "checked=\"checked\"" : "")." /> <label for=\"fbObj\">fbObj</label></p>
			</fieldset>";

/***************************************************************************
 *
 *             Encode Explorer
 *
 *             Author / Autor : Marek Rei (marek ät siineiolekala dot net)
 *
 *             Version / Versioon : 6.3
 *
 *             Last change / Viimati muudetud: 23.09.2011
 *
 *             Homepage / Koduleht: encode-explorer.siineiolekala.net
 *
 *
 *             NB!: Comments are in english.
 *                  Comments needed for configuring are in both estonian and english.
 *                  If you change anything, save with UTF-8! Otherwise you may
 *                  encounter problems, especially when displaying images.
 *                  
 *             NB!: Kommentaarid on inglise keeles.
 *                  Seadistamiseks vajalikud kommentaarid on eesti ja inglise keeles.
 *                  Kui midagi muudate, salvestage UTF-8 formaati! Vastasel juhul
 *                  võivad probleemid tekkida, eriti piltide kuvamisega.
 *
 ***************************************************************************/

/***************************************************************************
 *
 *   This is free software and it's distributed under GPL Licence.
 *
 *   Encode Explorer is written in the hopes that it can be useful to people.
 *   It has NO WARRANTY and when you use it, the author is not responsible
 *   for how it works (or doesn't).
 *   
 *   The icon images are designed by Mark James (http://www.famfamfam.com) 
 *   and distributed under the Creative Commons Attribution 3.0 License.
 *
 ***************************************************************************/

/***************************************************************************/
/*   SIIN ON SEADED                                                        */
/*                                                                         */
/*   HERE ARE THE SETTINGS FOR CONFIGURATION                               */
/***************************************************************************/

//
// Algväärtustame muutujad. Ära muuda.
//
// Initialising variables. Don't change these.
//

$_CONFIG = array();
$_ERROR = "";
$_START_TIME = microtime(TRUE);

/* 
 * GENERAL SETTINGS
 */

//
// Vali sobiv keel. Allpool on näha võimalikud valikud. Vaikimisi: en
//
// Choose a language. See below in the language section for options.
// Default: $_CONFIG['lang'] = "en";
//
$_CONFIG['lang'] = "en";

//
// Kuva pildifailidele eelvaated. Vaikimisi: true
// Levinumad pildifailide tüübid on toetatud (jpeg, png, gif).
// Pdf failid on samuti toetatud kuid ImageMagick peab olema paigaldatud.
//
// Display thumbnails when hovering over image entries in the list.
// Common image types are supported (jpeg, png, gif).
// Pdf files are also supported but require ImageMagick to be installed.
// Default: $_CONFIG['thumbnails'] = true;
//
$_CONFIG['thumbnails'] = true;

//
// Eelvaadete maksimumsuurused pikslites. Vaikimisi: 200, 200
//
// Maximum sizes of the thumbnails.
// Default: $_CONFIG['thumbnails_width'] = 200;
// Default: $_CONFIG['thumbnails_height'] = 200;
//
$_CONFIG['thumbnails_width'] = 300;
$_CONFIG['thumbnails_height'] = 300;

//
// Mobillidele mõeldud kasutajaliides. true/false
// Vaikimisi: true
// 
// Mobile interface enabled. true/false
// Default: $_CONFIG['mobile_enabled'] = true;
//
$_CONFIG['mobile_enabled'] = true;

//
// Mobiilidele mõeldud kasutajaliides avaneb automaatselt. true/false
// Vaikimisi: false
//
// Mobile interface as the default setting. true/false
// Default: $_CONFIG['mobile_default'] = false;
//
$_CONFIG['mobile_default'] = false;

/*
 * USER INTERFACE
 */

//
// Kas failid avatakse uues aknas? true/false
//
// Will the files be opened in a new window? true/false 
// Default: $_CONFIG['open_in_new_window'] = false;
//
$_CONFIG['open_in_new_window'] = false;

//
// Kui sügavalt alamkataloogidest suurust näitav script faile otsib? 
// Määra see nullist suuremaks, kui soovid kogu kasutatud ruumi suurust kuvada.
// Vaikimisi: 0
//
// How deep in subfolders will the script search for files? 
// Set it larger than 0 to display the total used space.
// Default: $_CONFIG['calculate_space_level'] = 0;
//
$_CONFIG['calculate_space_level'] = 0;

//
// Kas kuvatakse lehe päis? true/false
// Vaikimisi: true;
//
// Will the page header be displayed? 0=no, 1=yes. 
// Default: $_CONFIG['show_top'] = true;
//
$_CONFIG['show_top'] = true;

//
// Veebilehe pealkiri
//
// The title for the page
// Default: $_CONFIG['main_title'] = "Encode Explorer";
//
$_CONFIG['main_title'] = "PagineBaby Explorer";

//
// Pealkirjad, mida kuvatakse lehe päises, suvalises järjekorras.
//
// The secondary page titles, randomly selected and displayed under the main header.
// For example: $_CONFIG['secondary_titles'] = array("Secondary title", "&ldquo;Secondary title with quotes&rdquo;");
// Default: $_CONFIG['secondary_titles'] = array();
//
$_CONFIG['secondary_titles'] = array();

//
// Kuva asukoht kataloogipuus. true/false
// Vaikimisi: true
//
// Display breadcrumbs (relative path of the location).
// Default: $_CONFIG['show_path'] = true;
//
$_CONFIG['show_path'] = true;

//
// Kuva lehe laadimise aega. true/false
// Vaikimisi: false
// 
// Display the time it took to load the page.
// Default: $_CONFIG['show_load_time'] = true;
//
$_CONFIG['show_load_time'] = true;

//
// Formaat faili muutmise aja kuvamiseks.
//
// The time format for the "last changed" column.
// Default: $_CONFIG['time_format'] = "d.m.y H:i:s";
//
$_CONFIG['time_format'] = "d.m.y H:i:s";

//
// Kodeering, mida lehel kasutatakse. 
// Tuleb panna sobivaks, kui täpitähtedega tekib probleeme. Vaikimisi: UTF-8
//
// Charset. Use the one that suits for you. 
// Default: $_CONFIG['charset'] = "UTF-8";
//
$_CONFIG['charset'] = "UTF-8";

/*
* PERMISSIONS
*/

//
// Kaustade varjamine. Kaustade nimed mida lehel ei kuvata.
// Näiteks: CONFIG['hidden_dirs'] = array("ikoonid", "kaustanimi", "teinekaust");
//
// The array of folder names that will be hidden from the list.
// Default: $_CONFIG['hidden_dirs'] = array();
//
$_CONFIG['hidden_dirs'] = array();

//
// Failide varjamine. Failide nimed mida lehel ei kuvata.
// NB! Märgitud nimega failid ja kaustad varjatakse kõigis alamkaustades.
//
// Filenames that will be hidden from the list.
// Default: $_CONFIG['hidden_files'] = array(".ftpquota", "index.php", "index.php~", ".htaccess", ".htpasswd");
//
$_CONFIG['hidden_files'] = array(".ftpquota", "index.php", "index.php~", ".htaccess", ".htpasswd");

//
// Määra kas lehe nägemiseks peab sisse logima.
// 'false' tähendab, et leht on avalik
// 'true' tähendab, et kasutaja peab sisestama parooli (vaata allpool).
//
// Whether authentication is required to see the contents of the page.
// If set to false, the page is public.
// If set to true, you should specify some users as well (see below).
// Important: This only prevents people from seeing the list.
// They will still be able to access the files with a direct link.
// Default: $_CONFIG['require_login'] = false;
//
$_CONFIG['require_login'] = true;

//
// Kasutajanimed ja paroolid, lehele ligipääsu piiramiseks.
// Näiteks: $_CONFIG['users'] = array(array("user1", "pass1"), array("user2", "pass2"));
// Võimalik lehte kaitsta ka ainult üldise parooliga.
// Näiteks: $_CONFIG['users'] = array(array(null, "pass"));
// Kui ühtegi kasutajat märgitud ei ole, siis parooli ei küsita.
//
// Usernames and passwords for restricting access to the page.
// The format is: array(username, password, status)
// Status can be either "user" or "admin". User can read the page, admin can upload and delete.
// For example: $_CONFIG['users'] = array(array("username1", "password1", "user"), array("username2", "password2", "admin"));
// You can also keep require_login=false and specify an admin. 
// That way everyone can see the page but username and password are needed for uploading.
// For example: $_CONFIG['users'] = array(array("username", "password", "admin"));
// Default: $_CONFIG['users'] = array();
//
$_CONFIG['users'] = array(array("flyer", "070190", "admin"));

//
// Seaded uploadimiseks, uute kaustade loomiseks ja kustutamiseks.
// Valikud kehtivad ainult andmin kontode jaoks, tavakasutajatel pole need kunagi lubatud.
//
// Permissions for uploading, creating new directories and deleting.
// They only apply to admin accounts, regular users can never perform these operations.
// Default:
// $_CONFIG['upload_enable'] = true;
// $_CONFIG['newdir_enable'] = true;
// $_CONFIG['delete_enable'] = false;
//
$_CONFIG['upload_enable'] = true;
$_CONFIG['newdir_enable'] = true;
$_CONFIG['delete_enable'] = true;

/*
 * UPLOADING
 */

//
// Nimekiri kaustadest kuhu on lubatud uploadida ja uusi kaustu luua.
// Näiteks: $_CONFIG['upload_dirs'] = array("./myuploaddir1/", "./mydir/upload2/");
// Kausta asukoht peab olema määratud põhikausta suhtes, algama "./" ja lõppema "/" märgiga.
// Kõik kaustad märgitute all on automaatselt kaasa arvatud.
// Kui nimekiri on tühi (vaikimisi), siis on kõikidesse kaustadesse failide lisamine lubatud.
//
// List of directories where users are allowed to upload. 
// For example: $_CONFIG['upload_dirs'] = array("./myuploaddir1/", "./mydir/upload2/");
// The path should be relative to the main directory, start with "./" and end with "/".
// All the directories below the marked ones are automatically included as well.
// If the list is empty (default), all directories are open for uploads, given that the password has been set.
// Default: $_CONFIG['upload_dirs'] = array();
//
$_CONFIG['upload_dirs'] = array("./warehouse/");

//
// MIME failitüübid mis on uploadimiseks lubatud.
// Näiteks: $_CONFIG['upload_allow_type'] = array("image/png", "image/gif", "image/jpeg");
//
// MIME type that are allowed to be uploaded.
// For example, to only allow uploading of common image types, you could use:
// $_CONFIG['upload_allow_type'] = array("image/png", "image/gif", "image/jpeg");
// Default: $_CONFIG['upload_allow_type'] = array();
//
$_CONFIG['upload_allow_type'] = array("image/png", "image/gif", "image/jpeg");

//
// Uploadimiseks keelatud faililaiendid
//
// File extensions that are not allowed for uploading.
// For example: $_CONFIG['upload_reject_extension'] = array("php", "html", "htm");
// Default: $_CONFIG['upload_reject_extension'] = array();
//
$_CONFIG['upload_reject_extension'] = array();

/*
 * LOGGING
 */

//
// Failide lisamisest teatamise e-maili aadress.
// Kui määratud, siis saadetakse sellele aadressile e-mail iga kord kui keegi lisab uue faili või kausta.
//
// Upload notification e-mail.
// If set, an e-mail will be sent every time someone uploads a file or creates a new dirctory.
// Default: $_CONFIG['upload_email'] = "";
//
$_CONFIG['upload_email'] = "";

//
// Logifail. Kui määratud, siis kirjutatakse kaustade ja failide avamise kohta logi faili.
// Näiteks: $_CONFIG['log_file'] = ".log.txt";
//
// Logfile name. If set, a log line will be written there whenever a directory or file is accessed.
// For example: $_CONFIG['log_file'] = ".log.txt";
// Default: $_CONFIG['log_file'] = "";
//
$_CONFIG['log_file'] = "";

/*
 * SYSTEM
 */

//
// Algkataloogi suhteline aadress. Reeglina ei ole vaja muuta. 
// Kasutage ainult suhtelisi alamkatalooge!
// Vaikimisi: .
//
// The starting directory. Normally no need to change this.
// Use only relative subdirectories! 
// For example: $_CONFIG['starting_dir'] = "./mysubdir/";
// Default: $_CONFIG['starting_dir'] = ".";
//
$_CONFIG['starting_dir'] = "./warehouse/";

//
// Asukoht serveris. Tavaliselt ei ole vaja siia midagi panna kuna script leiab ise õige asukoha. 
// Mõnes serveris tuleb piirangute tõttu see aadress ise teistsuguseks määrata.
// See fail peaks asuma serveris aadressil [AADRESS]/index.php
// Aadress võib olla näiteks "/www/data/www.minudomeen.ee/minunimi"
//
// Location in the server. Usually this does not have to be set manually.
// Default: $_CONFIG['basedir'] = "";
//
$_CONFIG['basedir'] = "/sites.local/ipaginebaby";

//
// Suured failid. Kui sul on failiruumis väga suured failid (>4GB), siis on see vajalik
// faili suuruse õigeks määramiseks. Vaikimisi: false
//
// Big files. If you have some very big files (>4GB), enable this for correct
// file size calculation.
// Default: $_CONFIG['large_files'] = false;
//
$_CONFIG['large_files'] = true;

//
// Küpsise/sessiooni nimi. 
// Anna sellele originaalne väärtus kui soovid samas ruumis kasutada mitut koopiat
// ning ei taha, et toimuks andmete jagamist nende vahel.
// Väärtus tohib sisaldada ainult tähti ja numbreid. Näiteks: MYSESSION1
//
// The session name, which is used as a cookie name. 
// Change this to something original if you have multiple copies in the same space
// and wish to keep their authentication separate. 
// The value can contain only letters and numbers. For example: MYSESSION1
// More info at: http://www.php.net/manual/en/function.session-name.php
// Default: $_CONFIG['session_name'] = "";
//
$_CONFIG['session_name'] = "";

/***************************************************************************/
/*   TÕLKED                                                                */
/*                                                                         */
/*   TRANSLATIONS.                                                         */
/***************************************************************************/

$_TRANSLATIONS = array();

// Albanian
$_TRANSLATIONS["al"] = array(
	"file_name" => "Emri Skedarit",
	"size" => "Madhësia",
	"last_changed" => "Ndryshuar",
	"total_used_space" => "Memorija e përdorur total",
	"free_space" => "Memorija e lirë",
	"password" => "Fjalëkalimi",
	"upload" => "Ngarko skedarë",
	"failed_upload" => "Ngarkimi i skedarit dështoi!",
	"failed_move" => "Lëvizja e skedarit në udhëzuesin e saktë deshtoi!",
	"wrong_password" => "Fjalëkalimi i Gabuar!!",
	"make_directory" => "New dir",
	"new_dir_failed" => "Failed to create directory",
	"chmod_dir_failed" => "Failed to change directory rights",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// Dutch
$_TRANSLATIONS["nl"] = array(
	"file_name" => "Bestandsnaam",
	"size" => "Omvang",
	"last_changed" => "Laatst gewijzigd",
	"total_used_space" => "Totaal gebruikte ruimte",
	"free_space" => "Beschikbaar",
	"password" => "Wachtwoord",
	"upload" => "Upload",
	"failed_upload" => "Fout bij uploaden van bestand!",
	"failed_move" => "Fout bij het verplaatsen van tijdelijk uploadbestand!",
	"wrong_password" => "Fout wachtwoord!",
	"make_directory" => "Nieuwe folder",
	"new_dir_failed" => "Fout bij aanmaken folder!",
	"chmod_dir_failed" => "Rechten konden niet gewijzigd worden!",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// English
$_TRANSLATIONS["en"] = array(
	"file_name" => "File name",
	"size" => "Size",
	"last_changed" => "Last changed",
	"total_used_space" => "Total used space",
	"free_space" => "Free space",
	"password" => "Password",
	"upload" => "Upload",
	"failed_upload" => "Failed to upload the file!",
	"failed_move" => "Failed to move the file into the right directory!",
	"wrong_password" => "Wrong password",
	"make_directory" => "New dir",
	"new_dir_failed" => "Failed to create directory",
	"chmod_dir_failed" => "Failed to change directory rights",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root",
	"log_file_permission_error" => "The script does not have permissions to write the log file.",
	"upload_not_allowed" => "The script configuration does not allow uploading in this directory.",
	"upload_dir_not_writable" => "This directory does not have write permissions.",
	"mobile_version" => "Mobile view",
	"standard_version" => "Standard view",
	"page_load_time" => "Page loaded in %.2f ms",
	"wrong_pass" => "Wrong username or password",
	"username" => "Username",
	"log_in" => "Log in",
	"upload_type_not_allowed" => "This file type is not allowed for uploading.",
	"del" => "Del", // short for Delete
	"log_out" => "Log out"
);

// Estonian
$_TRANSLATIONS["et"] = array(
	"file_name" => "Faili nimi",
	"size" => "Suurus",
	"last_changed" => "Viimati muudetud",
	"total_used_space" => "Kokku kasutatud",
	"free_space" => "Vaba ruumi",
	"password" => "Parool",
	"upload" => "Uploadi",
	"failed_upload" => "Faili ei &otilde;nnestunud serverisse laadida!",
	"failed_move" => "Faili ei &otilde;nnestunud &otilde;igesse kausta liigutada!",
	"wrong_password" => "Vale parool",
	"make_directory" => "Uus kaust",
	"new_dir_failed" => "Kausta loomine ebaõnnestus",
	"chmod_dir_failed" => "Kausta õiguste muutmine ebaõnnestus",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Asukoht",
	"root" => "Peakaust"
);

// Finnish
$_TRANSLATIONS["fi"] = array(
	"file_name" => "Tiedoston nimi",
	"size" => "Koko",
	"last_changed" => "Muokattu",
	"total_used_space" => "Yhteenlaskettu koko",
	"free_space" => "Vapaa tila",
	"password" => "Salasana",
	"upload" => "Lisää tiedosto",
	"failed_upload" => "Tiedoston lisäys epäonnistui!",
	"failed_move" => "Tiedoston siirto kansioon epäonnistui!",
	"wrong_password" => "Väärä salasana",
	"make_directory" => "Uusi kansio",
	"new_dir_failed" => "Uuden kansion luonti epäonnistui!",
	"chmod_dir_failed" => "Kansion käyttäjäoikeuksien muuttaminen epäonnistui!",
	"unable_to_read_dir" => "Kansion sisältöä ei voi lukea.",
	"location" => "Paikka",
	"root" => "Juurihakemisto",
	"log_file_permission_error" => "Ohjelman ei ole sallittu kirjoittaa lokiin.",
	"upload_not_allowed" => "Ohjelman asetukset eivät salli tiedoston lisäämistä tähän kansioon.",
	"upload_dir_not_writable" => "Kansioon tallentaminen epäonnistui.",
	"mobile_version" => "Mobiilinäkymä",
	"standard_version" => "Tavallinen näkymä",
	"page_load_time" => "Sivu ladattu %.2f ms:ssa",
	"wrong_pass" => "Väärä käyttäjätunnus tai salasana",
	"username" => "Käyttäjätunnus",
	"log_in" => "Kirjaudu sisään",
	"log_out" => "Kirjaudu ulos",
	"upload_type_not_allowed" => "Tämän tiedostotyypin lisääminen on estetty.",
	"del" => "Poista"
);

// French
$_TRANSLATIONS["fr"] = array(
	"file_name" => "Nom de fichier",
	"size" => "Taille",
	"last_changed" => "Ajout&eacute;",
	"total_used_space" => "Espace total utilis&eacute;",
	"free_space" => "Espace libre",
	"password" => "Mot de passe",
	"upload" => "Envoyer un fichier",
	"failed_upload" => "Erreur lors de l'envoi",
	"failed_move" => "Erreur lors du changement de dossier",
	"wrong_password" => "Mauvais mot de passe",
	"make_directory" => "Nouveau dossier",
	"new_dir_failed" => "Erreur lors de la cr&eacute;ation du dossier",
	"chmod_dir_failed" => "Impossible de changer les permissions du dossier",
	"unable_to_read_dir" => "Impossible de lire le dossier",
	"location" => "Localisation",
	"root" => "Racine"
);

// German
$_TRANSLATIONS["de"] = array(
	"file_name" => "Dateiname",
	"size" => "Größe",
	"last_changed" => "Letzte Änderung",
	"total_used_space" => "Benutzter Speicher",
	"free_space" => "Freier Speicher",
	"password" => "Passwort",
	"upload" => "Upload",
	"failed_upload" => "Upload ist fehlgeschlagen!",
	"failed_move" => "Verschieben der Datei ist fehlgeschlagen!",
	"wrong_password" => "Falsches Passwort",
	"make_directory" => "Neuer Ordner",
	"new_dir_failed" => "Erstellen des Ordners fehlgeschlagen",
	"chmod_dir_failed" => "Veränderung der Zugriffsrechte des Ordners fehlgeschlagen",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// Greek
$_TRANSLATIONS["el"] = array(
	"file_name" => "Όνομα αρχείου",
	"size" => "Μέγεθος",
	"last_changed" => "Τροποποιημένο",
	"total_used_space" => "Χρησιμοποιημένος χώρος",
	"free_space" => "Ελεύθερος χώρος",
	"password" => "Εισάγεται κωδικό",
	"upload" => "Φόρτωση",
	"failed_upload" => "Αποτυχία φόρτωσης αρχείου!",
	"failed_move" => "Αποτυχία μεταφοράς αρχείου στον κατάλληλο φάκελο!",
	"wrong_password" => "Λάθος κωδικός",
	"make_directory" => "Δημιουργία νέου φακέλου",
	"new_dir_failed" => "Αποτυχία δημιουργίας νέου φακέλου",
	"chmod_dir_failed" => "Αποτυχία τροποποίησης δικαιωμάτων φακέλου",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// Hungarian
$_TRANSLATIONS["hu"] = array(
	"file_name" => "Fájl név",
	"size" => "Méret",
	"last_changed" => "Utolsó módosítás",
	"total_used_space" => "Összes elfoglalt terület",
	"free_space" => "Szabad terület",
	"password" => "Jelszó",
	"upload" => "Feltöltés",
	"failed_upload" => "A fájl feltöltése nem sikerült!",
	"failed_move" => "A fájl mozgatása nem sikerült!",
	"wrong_password" => "Hibás jelszó",
	"make_directory" => "Új mappa",
	"new_dir_failed" => "A mappa létrehozása nem sikerült",
	"chmod_dir_failed" => "A mappa jogainak megváltoztatása nem sikerült",
	"unable_to_read_dir" => "A mappa nem olvasható",
	"location" => "Hely",
	"root" => "Gyökér",
	"log_file_permission_error" => "A log fájl írása jogosultsági okok miatt nem sikerült.",
	"upload_not_allowed" => "Ebbe a mappába a feltöltés nem engedélyezett.",
	"upload_dir_not_writable" => "A mappa nem írható.",
	"mobile_version" => "Mobil nézet",
	"standard_version" => "Web nézet",
	"page_load_time" => "Letöltési id? %.2f ms",
	"wrong_pass" => "Rossz felhasználónév vagy jelszó",
	"username" => "Felhasználónév",
	"log_in" => "Belépés",
	"upload_type_not_allowed" => "A fájltípus feltöltése tiltott."
);

// Italian
$_TRANSLATIONS["it"] = array(
	"file_name" => "Nome file",
	"size" => "Dimensione",
	"last_changed" => "Ultima modifica",
	"total_used_space" => "Totale spazio usato",
	"free_space" => "Spazio disponibile",
	"password" => "Parola chiave",
	"upload" => "Caricamento file",
	"failed_upload" => "Caricamento del file fallito!",
	"failed_move" => "Spostamento del file nella cartella fallito!",
	"wrong_password" => "Password sbagliata",
	"make_directory" => "Nuova cartella",
	"new_dir_failed" => "Creazione cartella fallita!",
	"chmod_dir_failed" => "Modifica dei permessi della cartella fallita!",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// Norwegian
$_TRANSLATIONS["no"] = array(
	"file_name" => "Navn",
	"size" => "Størrelse",
	"last_changed" => "Endret",
	"total_used_space" => "Brukt plass",
	"free_space" => "Resterende plass",
	"password" => "Passord",
	"upload" => "Last opp",
	"failed_upload" => "Opplasting gikk galt",
	"failed_move" => "Kunne ikke flytte objektet",
	"wrong_password" => "Feil passord",
	"make_directory" => "Ny mappe",
	"new_dir_failed" => "Kunne ikke lage ny mappe",
	"chmod_dir_failed" => "Kunne ikke endre rettigheter",
	"unable_to_read_dir" => "Kunne ikke lese mappen",
	"location" => "Område",
	"root" => "Rot"
);

//Polish
$_TRANSLATIONS["pl"] = array(
	"file_name" => "Nazwa Pliku",
	"size" => "Rozmiar",
	"last_changed" => "Data Zmiany",
	"total_used_space" => "Total used space",
	"free_space" => "Wolnego obszaru",
	"password" => "Haslo",
	"upload" => "Przeslij",
	"failed_upload" => "Przeslanie pliku nie powiodlo sie",
	"failed_move" => "Przenosienie pliku nie powidlo sie!",
	"wrong_password" => "Niepoprawne haslo",
	"make_directory" => "Nowy folder",
	"new_dir_failed" => "Blad podczas tworzenia nowego foldera",
	"chmod_dir_failed" => "Blad podczas zmiany uprawnienia foldera",
	"unable_to_read_dir" => "Odczytanie foldera nie powiodlo sie",
	"location" => "Miejsce",
	"root" => "Root",
	"log_file_permission_error" => "Brak uprawnien aby utowrzyc dziennik dzialan.",
	"upload_not_allowed" => "Konfiguracja zabrania przeslanie pliku do tego foldera.",
	"upload_dir_not_writable" => "Nie mozna zapisac pliku do tego foldera.",
	"mobile_version" => "Wersja Mobile",
	"standard_version" => "Widok standardowy",
	"page_load_time" => "Zaladowano w %.2f ms",
	"wrong_pass" => "Nie poprawna nazwa uzytkownika lub hasla",
	"username" => "Uzytkownik",
	"log_in" => "Zaloguj sie",
	"upload_type_not_allowed" => "Ten rodazaj pliku jest zabrioniony."
);

// Portuguese (Brazil)
$_TRANSLATIONS["pt_BR"] = array(
	"file_name" => "Nome do arquivo",
	"size" => "Tamanho",
	"last_changed" => "Modificado em",
	"total_used_space" => "Total de espaço utilizado",
	"free_space" => "Espaço livre",
	"password" => "Senha",
	"upload" => "Enviar",
	"failed_upload" => "Falha ao enviar o arquivo!",
	"failed_move" => "Falha ao mover o arquivo para o diretório correto!",
	"wrong_password" => "Senha errada",
	"make_directory" => "Nova pasta",
	"new_dir_failed" => "Falha ao criar diretório",
	"chmod_dir_failed" => "Falha ao mudar os privilégios do diretório",
	"unable_to_read_dir" => "Não foi possível ler o diretório",
	"location" => "Localização",
	"root" => "Raíz",
	"log_file_permission_error" => "O script não tem permissão para escrever o arquivo de log.",
	"upload_not_allowed" => "A configuração do script não permite envios neste diretório.",
	"upload_dir_not_writable" => "Não há permissão para escrita neste diretório.",
	"mobile_version" => "Versão Móvel",
	"standard_version" => "Versão Padrão",
	"page_load_time" => "Página carregada em %.2f ms",
	"wrong_pass" => "Nome de usuário ou senha errados",
	"username" => "Nome de Usuário",
	"log_in" => "Log in",
	"upload_type_not_allowed" => "Não é permitido envio de arquivos deste tipo."
);

// Romanian
$_TRANSLATIONS["ro"] = array(
	"file_name" => "Nume fisier",
	"size" => "Marime",
	"last_changed" => "Ultima modificare",
	"total_used_space" => "Spatiu total utilizat",
	"free_space" => "Spatiu disponibil",
	"password" => "Parola",
	"upload" => "Incarcare fisier",
	"failed_upload" => "Incarcarea fisierului a esuat!",
	"failed_move" => "Mutarea fisierului in alt director a esuat!",
	"wrong_password" => "Parol gresita!",
	"make_directory" => "Director nou",
	"new_dir_failed" => "Eroare la crearea directorului",
	"chmod_dir_failed" => "Eroare la modificarea drepturilor pe director",
	"unable_to_read_dir" => "Nu s-a putut citi directorul",
	"location" => "Locatie",
	"root" => "Root"
);

// Russian
$_TRANSLATIONS["ru"] = array(
    "file_name" => "Имя файла",
    "size" => "Размер",
    "last_changed" => "Последнее изменение",
    "total_used_space" => "Всего использовано",
    "free_space" => "Свободно",
    "password" => "Пароль",
    "upload" => "Загрузка",
    "failed_upload" => "Не удалось загрузить файл!",
    "failed_move" => "Не удалось переместить файл в нужную папку!",
    "wrong_password" => "Неверный пароль",
    "make_directory" => "Новая папка",
    "new_dir_failed" => "Не удалось создать папку",
    "chmod_dir_failed" => "Не удалось изменить права доступа к папке",
    "unable_to_read_dir" => "Не возможно прочитать папку",
    "location" => "Расположение",
    "root" => "Корневая папка",
    "log_file_permission_error" => "Скрипт не имеет прав для записи лога файла.",
    "upload_not_allowed" => "Загрузка в эту папку запрещена в настройках скрипта",
    "upload_dir_not_writable" => "В эту папку запрещена запись",
    "mobile_version" => "Мобильный вид",
    "standard_version" => "Обычный вид",
    "page_load_time" => "Страница загружена за %.2f мс.",
    "wrong_pass" => "Неверное имя пользователя или пароль",
    "username" => "Имя пользователя",
    "log_in" => "Войти",
    "upload_type_not_allowed" => "Этот тип файла запрещено загружать"
);

// Slovensky
$_TRANSLATIONS["sk"] = array(
	"file_name" => "Meno súboru",
	"size" => "Ve?kos?",
	"last_changed" => "Posledná zmena",
	"total_used_space" => "Použité miesto celkom",
	"free_space" => "Vo?né miesto",
	"password" => "Heslo",
	"upload" => "Nahranie súborov",
	"failed_upload" => "Chyba nahrávania súboru!",
	"failed_move" => "Nepodarilo sa presunú? súbor do vybraného adresára!",
	"wrong_password" => "Neplatné heslo!",
	"make_directory" => "Nový prie?inok",
	"new_dir_failed" => "Nepodarilo sa vytvori? adresár!",
	"chmod_dir_failed" => "Nepodarilo sa zmeni? práva adresára!",
	"unable_to_read_dir" => "Nemôžem ?íta? adresár",
	"location" => "Umiestnenie",
	"root" => "Domov"
);

// Spanish
$_TRANSLATIONS["es"] = array(
	"file_name" => "Nombre de archivo",
	"size" => "Medida",
	"last_changed" => "Ultima modificaciÃ³n",
	"total_used_space" => "Total espacio usado",
	"free_space" => "Espacio libre",
	"password" => "ContraseÃ±a",
	"upload" => "Subir el archivo",
	"failed_upload" => "Error al subir el archivo!",
	"failed_move" => "Error al mover el archivo al directorio seleccionado!",
	"wrong_password" => "ContraseÃ±a incorrecta",
	"make_directory" => "New dir",
	"new_dir_failed" => "Failed to create directory",
	"chmod_dir_failed" => "Failed to change directory rights",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

// Swedish
$_TRANSLATIONS["sv"] = array(
	"file_name" => "Filnamn",
	"size" => "Storlek",
	"last_changed" => "Senast andrad",
	"total_used_space" => "Totalt upptaget utrymme",
	"free_space" => "Ledigt utrymme",
	"password" => "Losenord",
	"upload" => "Ladda upp",
	"failed_upload" => "Fel vid uppladdning av fil!",
	"failed_move" => "Fel vid flytt av fil till mapp!",
	"wrong_password" => "Fel losenord",
	"make_directory" => "Ny mapp",
	"new_dir_failed" => "Fel vid skapande av mapp",
	"chmod_dir_failed" => "Fel vid andring av mappens egenskaper",
	"unable_to_read_dir" => "Kan inte lasa den filen",
	"location" => "Plats",
	"root" => "Hem"
);

// Turkish
$_TRANSLATIONS["tr"] = array(
	"file_name" => "Dosya ismi",
	"size" => "Boyut",
	"last_changed" => "gecmis",
	"total_used_space" => "Toplam dosya boyutu",
	"free_space" => "Bos alan",
	"password" => "Sifre",
	"upload" => "Yükleyen",
	"failed_upload" => "Hatali dosya yüklemesi!",
	"failed_move" => "Hatali dosya tasimasi!",
	"wrong_password" => "Yeniden sifre",
	"make_directory" => "Yeni dosya",
	"new_dir_failed" => "Dosya olusturalamadi",
	"chmod_dir_failed" => "Dosya ayari deqistirelemedi",
	"unable_to_read_dir" => "Unable to read directory",
	"location" => "Location",
	"root" => "Root"
);

/***************************************************************************/
/*   CSS KUJUNDUSE MUUTMISEKS                                              */
/*                                                                         */
/*   CSS FOR TWEAKING THE DESIGN                                           */
/***************************************************************************/
?>