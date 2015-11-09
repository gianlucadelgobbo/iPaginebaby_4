<?
include_once("_php/site.class.php");
$site = new site();

$params = explode("/",$_GET['param']);

if(strlen($params[count($params)-1])==0) array_pop($params);

if(count($params)>0) {
	if($params[0]=="explore" && count($params)==1) {
		$site->dataObj->db->query("select distinct(city) as city from places order by city");
		$res=$site->dataObj->db->fetch();
		$str = "<ul>";
		foreach($res as $row) {
			if ($row->city) $str.= "<li><a href=\"/".$site->dataObj->sanitize_title_with_dashes($row->city)."\">".$row->city."</a></li>";
		}
		$str.= "</ul>";
	} else {
		$sql = "select id from places where permalink like '".$params[0]."/%' limit 1";
		//print_r($sql);
		$site->dataObj->db->query($sql);
		$resCity=$site->dataObj->db->fetch();
		if($resCity) {
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
				$str = "<ul>";
				ksort($cat);
				foreach($cat as $k=>$v) {
					$str.= "<li><a href=\"/".$params[0]."/category/".$k."\">".$k." ".$site->dataObj->getNomeChiave($k)." [".$v."]</a></li>";
					foreach($v as $kk=>$vv) {
						$str.= "<li><a href=\"/".$params[0]."/category/".$kk."\">".$k." ".$site->dataObj->getNomeChiave($kk)." [".$vv."]</a></li>";
					}
				}
				$str.= "</ul>";
			} else if (count($params)==2) {
				$site->dataObj->db->query("select * from places where permalink = '".$params[0]."/".$params[1]."' limit 1");
				$res=$site->dataObj->db->fetch();
	
				if($res) {
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
						$sql = "select * from places where permalink like '".$params[0]."/%' and cat like '%|".$params[2]."|%'";
						//print_r($sql);
						$site->dataObj->db->query($sql);
						$res=$site->dataObj->db->fetch();
						$str = "<ul>";
						foreach($res as $row) {
							$str.= "<li><a href=\"/".$row->permalink."\">".$row->name."</a></li>";
						}
						$str.= "</ul>";
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
	$inc = "web/inc/explore.php";
} else {
	$inc = "web/inc/home.php";
}
//print_r($res);
//print_r($_GET);
?>
<!DOCTYPE html>

<!--[if lt IE 7 ]> <html class="ie ie6 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" dir="ltr" lang="en-US"><!--<![endif]-->
<!-- the "no-js" class is for Modernizr. -->
<head>
	<meta charset="UTF-8">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta name = "viewport" content = "initial-scale = 1.0, maximum-scale=1, minimum-scale = 1.0"> 
	<title>PagineBaby | iPhone, iPad and Android App to quickly find information about your surroundings!</title>
	<meta name="description" content="iPhone, iPad and Android App to quickly find information about your surroundings!">
	<meta name="author" content="Rana Elettrica Soc. Coop.">
	<!--  Mobile Viewport meta tag
	j.mp/mobileviewport & davidbcalhoun.com/2010/viewport-metatag 
	device-width : Occupy full width of the screen in its current orientation
	initial-scale = 1.0 retains dimensions instead of zooming out if page height > device height
	maximum-scale = 1.0 retains dimensions instead of zooming in if page width < device width -->
	<!-- Uncomment to use; use thoughtfully!
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	-->
	<link rel="canonical" href="/" />
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/themes/ico/114x114.png">
	<link rel="stylesheet" href="/web/style.css">
	<link rel="stylesheet" href="/web/flexslider.css">	

	<!-- all our JS is at the bottom of the page, except for Modernizr. -->
	<script src="/web/js/modernizr-1.7.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js?ver=3.3.1"></script>
	<script type="text/javascript" src="/web/js/jquery.easing.js?ver=1.3"></script>
	<script src="/web/js/jquery.preloadr.js"></script>
	<script src="/web/js/jquery.flexslider-min.js"></script>
	<script src="/web/js/responsive-slider.js"></script>
	<script>
	  var template_path = "/web";
	</script>
	<script src="/web/js/home.js?v=5"></script>
	
	<!-- SOLO EXPLORE -->
	
	
	<style type="text/css" media="screen">@import "/_admin/_script/jquery/css/south-street/jquery-ui-1.8.24.custom.css";</style>
	<script type="text/javascript"            src="/_admin/_script/jquery/js/jquery-ui-1.8.24.custom.min.js"></script>
	<!-- GOOGLEAPI -->
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
	<!-- GOOGLEAPI -->
	<script type="text/javascript" src="/js/script_site.js" charset="utf-8"></script>
	<script type="text/javascript" src="/js/dictionary.js"></script>
	<script type="text/javascript" src="/js/cat.js"></script>
	<script type="text/javascript">
	selectCat = "<select class=\"cat\" name=\"cat[]\" onchange=\"addCat(this);\">";
	selectCat+= "<option value=\"\">"+getLabel("Categoria")+"</option>";
	
	searchCat = "<ul class=\"edgetoedge\">";
	searchCat+= "<li>";
	searchCat+= "<ul class=\"individual\">";
	searchCat+= "<li><a class=\"greenButton\" onclick=\"catSearch('hl');\" href=\"#\">"+getLabel("PrimoPiano")+"</a></li>";
	searchCat+= "<li><a class=\"greenButton\" onclick=\"catSearch('pbcard');\" href=\"#\">"+getLabel("PBcard")+"</a></li>";
	searchCat+= "</ul>";
	searchCat+= "</li>";
	//for(var a;a<catMain.length;cat++) {
	for(var item in catMain) {
		selectCat+= "<optgroup label=\""+catMain[item].title+"\">";
		
		searchCat+= "<li>";
		searchCat+= "<select onchange=\"catSearch(this.value);\" id=\"cat"+item+"\" name=\"cat"+item+"\">";
		searchCat+= "<option>"+catMain[item].title+"</option>";
		searchCat+= "<option value=\""+item+"\">"+catMain[item].title+" ("+getLabel("tutto")+")</option>";
		for(var item2 in catMain[item].items) {
			selectCat+= "<option value=\"|"+item+"|"+catMain[item].items[item2].id+"|\">"+catMain[item].items[item2].name+"</option>";
			searchCat+= "<option value=\""+catMain[item].items[item2].id+"\">"+catMain[item].items[item2].name+"</option>";
		}
		selectCat+= "</optgroup>";

		searchCat+= "</select>";
		searchCat+= "</li>";
	}
	selectCat+= "</select>";
	
	searchCat+= "</ul>";

	var markers = [
		['Bondi Beach', -33.890542, 151.274856],
		['Coogee Beach', -33.923036, 151.259052],
		['Cronulla Beach', -34.028249, 151.157507],
		['Manly Beach', -33.80010128657071, 151.28747820854187],
		['Maroubra Beach', -33.950198, 151.259302]
	];
	
	function initializeMaps() {
		var myOptions = {
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			mapTypeControl: false
		};
		console.log(document.getElementById("map_canvas"));
		var map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
		var infowindow = new google.maps.InfoWindow(); 
		var marker, i;
		var bounds = new google.maps.LatLngBounds();
	
		for (i = 0; i < markers.length; i++) { 
			var pos = new google.maps.LatLng(markers[i][1], markers[i][2]);
			bounds.extend(pos);
			marker = new google.maps.Marker({
				position: pos,
				map: map
			});
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					infowindow.setContent(markers[i][0]);
					infowindow.open(map, marker);
				}
			})(marker, i));
		}
		map.fitBounds(bounds);
	}
	$(document).ready(function() {
		//initializeMaps();
	});

</script>
</head>

<body>
<div id="fb-root"></div>
<!-- ONLY WEB -->
<script type="text/javascript">
(function() {
	var e = document.createElement('script'); e.async = true;
	e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
	document.getElementById('fb-root').appendChild(e);
}());
window.fbAsyncInit = function() {
	FB.init({ appId: '366045943462852', 
	status: true, 
	cookie: true,
	xfbml: true,
	oauth: true});
	$(".login").show();
	$(".logout").hide();
	FB.Event.subscribe('auth.statusChange', function(response) {
		me(response);
		console.log('auth.login event');
	});			
};
function login(){
	document.location.href = 'https://www.facebook.com/dialog/oauth?client_id=366045943462852&redirect_uri=http://ipb.flyer.it/explore/&scope=email';
}
</script>
<div id="world"></div>
<? include_once($inc); ?>
<footer id="footer" class="">
    <div class="wrapper">
  		<ul>
			<li class="attorno">
				<h4>Rana Elettrica Soc. Coop.</h4>
				<ul>
					<li><a href='mailt&#111;&#58;info@paginebaby.it'><? echo( $site->dataObj->getLabel("Contact Us")); ?></a></li>
					<li class="press-link"><a href="/press-release"><? echo( $site->dataObj->getLabel("Press Release")); ?></a></li>
				</ul>
			</li>
			<li class="social">
				<h4>Follow us</h4>
				<ul class="social">
					<li><a class="fb ir" target ="_blank" href="https://www.facebook.com/PagineBabyApp">Facebook</a></li>
					<li><a class="twt ir" target ="_blank" href="http://twitter.com/#!/PagineBabyapp">Twitter</a></li>
				</ul>
				<div class="clearfix"></div>
			</li>
			<li class="last">
				<h4>Menu</h4>      
				<ul class="nav">
					<li><a href="/">Home</a></li>
					<li><a href="/support"><? echo( $site->dataObj->getLabel("Support")); ?></a></li>
					<li><a href="/about-us"><? echo( $site->dataObj->getLabel("About Us")); ?></a></li>
					<li><a href="/blog"><? echo( $site->dataObj->getLabel("Blog")); ?></a></li>
				</ul>
			</li>
      	</ul>
		<br class="myClear" />
		<p class='copy'>Copyright © 2012 Rana Elettrica Soc. Coop. - Via C. Landino, 5 - 00137 Roma - P. Iva 09655591007 - All rights reserved. Powered by <a href="http://www.flyer.it" target="_blank">Flyer communication</a>.</p>
	</div>
</footer>
<div id="toolbar">
	<ul>
		<li class="mainButtons">
			<a href="#" onClick="$('#user').toggle();$('#settings').hide();$('#help').hide();"><? echo( $site->dataObj->getLabel("Utente")); ?></a>
			<ul class="rounded" id="user">
				<li class="login"><a href="#" onClick="login()"><? echo( $site->dataObj->getLabel("Login")); ?></a></li>
			</ul>
		</li>
		<li class="mainButtons">
			<a href="#" onClick="$('#settings').toggle();$('#help').hide();$('#user').hide();"><? echo( $site->dataObj->getLabel("Impostazioni")); ?></a>
			<ul class="rounded" id="settings">
				<li><? echo( $site->dataObj->getLabel("Raggio d'azione")); ?> <span class="toggle"><input type="text" disabled="disabled" name="radius-field" id="radius-field" value="" /></span></li>
				<li><div id="slider"></div></li>
				<li><? echo( $site->dataObj->getLabel("Posizione dispositivo")); ?> <span class="toggle"><input type="checkbox" name="autopos" id="autopos" value="1" onchange="setMode();" /></span></li>
				<li id="position"><? echo( $site->dataObj->getLabel("Nessuna posizione impostata")); ?> <a href="#editmap" onclick="editPosMap()"><? echo( $site->dataObj->getLabel("imposta manualmente")); ?></a></li>
			</ul>
		</li>
		<li class="mainButtons">
			<a href="#" onClick="$('#help').toggle();$('#settings').hide();$('#user').hide();"><? echo( $site->dataObj->getLabel("Altro")); ?></a>
			<ul class="rounded" id="help">
				<li><a href="#comeusare"><? echo( $site->dataObj->getLabel("Come usare PagineBaby")); ?></a></li>
				<li><a href="#credits"><? echo( $site->dataObj->getLabel("Credits")); ?></a></li>
			</ul>
		</li>
	</ul>
	<br class="myClear" />
</div>
	<div id="editmap">
		<div class="toolbar">
			<h1><script type="text/javascript">document.write(getLabel("Modifica mappa"))</script></h1>
			<a href="#" class="back"><script type="text/javascript">document.write(getLabel("indietro"))</script></a>
		</div>
		<form id="search-map-form" action="#">
			<ul class="edgetoedge">
		  		<li><input type="text" name="search-text" placeholder="Citta" id="search-map" /></li>
				<script type="text/javascript">document.getElementById("search-map").placeholder = getLabel("Citta");</script>
			</ul>
		</form>
		<div id="map_canvas">map div</div>
	</div>
</body>
</html>
