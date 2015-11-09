<?
	include_once("../_php/site.class.php");
	include_once("../_php/admin.class.php");
	$site = new site();

	include_once("config.php");
	
	GateKeeper::init();
	if (GateKeeper::isUserLoggedIn() && $_SERVER['REQUEST_URI']=="/_admin/") {
		header("Location: /_admin/places/");
		exit;
	}
	if (!GateKeeper::isUserLoggedIn() && $_SERVER['REQUEST_URI']!="/_admin/") {
		header("Location: /_admin/");
		exit;
	}

	$menu = "
		<ul class=\"mainMenu\">
			<li>".(strpos($_SERVER['REQUEST_URI'], "/_admin/users/")===false ? "<a href=\"/_admin/users/\">USERS</a>" : "USERS")."</li>
			<li>".(strpos($_SERVER['REQUEST_URI'], "/_admin/places/")===false ? "<a href=\"/_admin/places/\">PLACES</a>" : "PLACES")."</li>
			<li>".(strpos($_SERVER['REQUEST_URI'], "/_admin/labels/")===false ? "<a href=\"/_admin/labels/\">LABELS</a>" : "LABELS")."</li>
			<li>".(strpos($_SERVER['REQUEST_URI'], "/_admin/gallery/")===false ? "<a href=\"/_admin/gallery/\">GALLERY</a>" : "GALLERY")."</li>
			".(GateKeeper::isUserLoggedIn() ? "<li><a href=\"/_admin/?logout\">logout</a></li>" : "")."
		</ul>";
		
	if(!GateKeeper::isUserLoggedIn() || isset($_GET['logout'])){
		$inc = "_inc/login.php";
		$title = "Pagine Baby Manager: LOGIN";
	} else {
		$params = explode("/",$_GET['param']);
		
		if(strlen($params[count($params)-1])==0) array_pop($params);
		if(count($params)==1) {
			if($params[0] == "places") {
				$inc = "_inc/places.php";
					$title = "Pagine Baby Manager: PLACES";
				$mode = 1;
			} else if($params[0] == "users") {
				$inc = "_inc/users.php";
					$title = "Pagine Baby Manager: USERS";
				$mode = 2;
			} else if($params[0] == "labels") {
				$inc = "_inc/labels.php";
					$title = "Pagine Baby Manager: LABELS";
				$mode = 3;
			} else if($params[0] == "gallery") {
				$inc = "_inc/files.php";
					$title = "Pagine Baby Manager: GALLERY";
				$mode = 4;
			} else {
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
				include_once("error.php");
				exit();
			}
		} else if (count($params)==2) {
			if($params[0] == "tools") {
				if($params[1] == "geoupdate") {
					$inc = "_tools/geoupdate.php";
				} else if($params[1] == "cleanhtml") {
					$inc = "_tools/cleanhtml.php";
				} else if($params[1] == "copyimages") {
					$inc = "_tools/copyimages.php";
				} else if($params[1] == "updatePermalink") {
					$inc = "_tools/updatePermalink.php";
				}
			} else {
				header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
				include_once("error.php");
				exit();
			}
		} else {
			header("Location: /_admin/places/");
			exit();
		}
	}
	$searchBlock = "
	<fieldset style=\"margin-top:10px\">
		<legend>Filters</legend>
		<form name=\"filter\" action=\"#\" method=\"get\">
			".$search[$mode]."
			<div class=\"left\">
				<input type=\"submit\" name=\"submit_search\" value=\"FILTER\" style=\"font-size: 2em;margin-top: 10px;\" />
			</div>
			<br class=\"myClear\" />
			<textarea cols=\"100\">var catMain = ".json_encode($catJS)."</textarea>
		</form>
	</fieldset>\n";
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Admin</title>
<link type="text/css" href="/_admin/_script/jquery/css/smoothness/jquery-ui-1.8.17.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="/_admin/_script/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="/_admin/_script/jquery/js/jquery-ui-1.8.17.custom.min.js"></script>
<!-- GOOGLEAPI -->
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
<!-- GOOGLEAPI -->

<script type="text/javascript" src="/_admin/_script/script.js"></script>
<link type="text/css" rel="stylesheet" href="/_admin/style.css" />	
</head>
<body>
<h1><? echo($title); ?><? echo($tita); ?></h1>
<? echo($menu); ?>
<? 
	if ($inc) {
		include_once($inc);
	}
?>
</body>
</html>
