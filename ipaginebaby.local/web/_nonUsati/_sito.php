<?
	include_once("_php/site.class.php");
	$site = new site();

//	include_once("config.php");
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
} else {
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

<head id="www-sitename-com" data-template-set="html5-reset-wordpress-theme" profile="http://gmpg.org/xfn/11">

	<meta charset="UTF-8">
	
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name = "viewport" content = "initial-scale = 1.0, maximum-scale=1, minimum-scale = 1.0"> 
	
	<title>PagineBaby | iPhone, iPad and Android App to quickly find information about your surroundings!</title>
	
	<meta name="title" content=" - PagineBaby">
	<meta name="description" content="iPhone, iPad and Android App to quickly find information about your surroundings!">
	
	<meta name="google-site-verification" content="">
	<!-- Speaking of Google, don't forget to set your site up: http://google.com/webmasters -->
	
	<meta name="author" content="Rana Elettrica Soc. Coop.">
	<meta name="Copyright" content="Copyright Rana Elettrica Soc. Coop. 2012. All Rights Reserved.">

	
	<!--  Mobile Viewport meta tag
	j.mp/mobileviewport & davidbcalhoun.com/2010/viewport-metatag 
	device-width : Occupy full width of the screen in its current orientation
	initial-scale = 1.0 retains dimensions instead of zooming out if page height > device height
	maximum-scale = 1.0 retains dimensions instead of zooming in if page width < device width -->
	<!-- Uncomment to use; use thoughtfully!
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	-->
	
	<link rel="shortcut icon" href="/pb_files/_/img/favicon.ico">
	<!-- This is the traditional favicon.
		 - size: 16x16 or 32x32
		 - transparency is OK
		 - see wikipedia for info on browser support: http://mky.be/favicon/ -->
		 
	<link rel="apple-touch-icon" href="/pb_files/_/img/apple-touch-icon.png">
	<!-- The is the icon for iOS's Web Clip.
		 - size: 57x57 for older iPhones, 72x72 for iPads, 114x114 for iPhone4's retina display (IMHO, just go ahead and use the biggest one)
		 - To prevent iOS from applying its styles to the icon name it thusly: apple-touch-icon-precomposed.png
		 - Transparency is not recommended (iOS will put a black BG behind the icon) -->
	
	<!-- CSS: screen, mobile & print are all in the same file -->
	<link rel="stylesheet" href="/pb_files/style.css">
	<link rel="stylesheet" href="/pb_files/style_data.css?v=3">
	<link rel="stylesheet" href="/pb_files/style_blog.css?v=2">
	<link rel="stylesheet" href="/pb_files/flexslider.css">	
	<!-- all our JS is at the bottom of the page, except for Modernizr. -->
	<script src="/pb_files/js/modernizr-1.7.min.js"></script>
	
	<link rel="pingback" href="/xmlrpc.php" />

	
	<link rel="alternate" type="application/rss+xml" title="PagineBaby &raquo; Feed" href="/feed/" />
	<link rel="alternate" type="application/rss+xml" title="PagineBaby &raquo; Comments Feed" href="/comments/feed/" />
	<link rel="alternate" type="application/rss+xml" title="PagineBaby &raquo; Homepage Comments Feed" href="/homepage/feed/" />
	<link rel="stylesheet" id="jquery.fancybox-css"  href="/pb_files/js/jquery.fancybox.css?ver=1.2.6" type="text/css" media="all" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js?ver=3.3.1"></script>
	<script type="text/javascript" src="/pb_files/js/jquery.fancybox.js?ver=1.2.6"></script>
	<script type="text/javascript" src="/pb_files/js/jquery.easing.js?ver=1.3"></script>
	<link rel="canonical" href="/" />
	<script type="text/javascript">
		jQuery(document).ready(function($){
			var select = $('a[href$=".bmp"],a[href$=".gif"],a[href$=".jpg"],a[href$=".jpeg"],a[href$=".png"],a[href$=".BMP"],a[href$=".GIF"],a[href$=".JPG"],a[href$=".JPEG"],a[href$=".PNG"]');
			select.attr('rel', 'fancybox');
			select.fancybox();
		});
	</script>
	<script>
	  var base_url = "";
	  var template_path = "/pb_files";
	</script>
	
	<script src="/pb_files/js/jquery-ui.js"></script>
	<script src="/pb_files/js/jquery.preloadr.js"></script>
	
	<script src="/pb_files/js/jquery.flexslider-min.js"></script>
	<script src="/pb_files/js/responsive-slider.js"></script>
	
	<!-- SOLO EXPLORE -->
	
	<style type="text/css" media="screen">@import "/js/css/south-street/jquery-ui-1.8.21.custom.css";</style>
	
	<script type="text/javascript" src="/js/jquery-1.4.2.min.js" charset="utf-8"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.21.custom.min.js"></script>
	<!-- GOOGLEAPI -->
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
	<!-- GOOGLEAPI -->
	<script type="text/javascript" src="/js/script_site.js" charset="utf-8"></script>
	<script type="text/javascript" src="/js/dictionary.js"></script>
	<script type="text/javascript" src="/js/cat.js"></script>
	<script type="text/javascript">
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

<body class="home page page-id-2 page-template page-template-homepage-php">
<div id="fb-root"></div>
<!-- ONLY WEB -->
<script type="text/javascript" charset="utf-8">
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
<div id="toolbar">
	<ul>
		<li class="mainButtons">
			<a href="#" onClick="$('#user').toggle();$('#settings').hide();$('#help').hide();"><script type="text/javascript">document.write(getLabel("Utente"))</script></a>
			<ul class="rounded" id="user">
				<li class="login"><a href="#" onClick="login()"><script type="text/javascript">document.write(getLabel("Login"))</script></a></li>
			</ul>
		</li>
		<li class="mainButtons">
			<a href="#" onClick="$('#settings').toggle();$('#help').hide();$('#user').hide();"><script type="text/javascript">document.write(getLabel("Impostazioni"))</script></a>
			<ul class="rounded" id="settings">
				<li><script type="text/javascript">document.write(getLabel("Raggio d'azione"))</script> <span class="toggle"><input type="text" disabled="disabled" name="radius-field" id="radius-field" value="" /></span></li>
				<li><div id="slider"></div></li>
				<li><script type="text/javascript">document.write(getLabel("Posizione dispositivo"))</script> <span class="toggle"><input type="checkbox" name="autopos" id="autopos" value="1" onchange="setMode();" /></span></li>
				<li id="position"><script type="text/javascript">document.write(getLabel("Nessuna posizione impostata"))</script> <a href="#editmap" onclick="editPosMap()"><script type="text/javascript">document.write(getLabel("imposta manualmente"))</script></a></li>
			</ul>
		</li>
		<li class="mainButtons">
			<a href="#" onClick="$('#help').toggle();$('#settings').hide();$('#user').hide();"><script type="text/javascript">document.write(getLabel("Altro"))</script></a>
			<ul class="rounded" id="help">
				<li><a href="#comeusare"><script type="text/javascript">document.write(getLabel("Come usare PagineBaby"))</script></a></li>
				<li><a href="#credits"><script type="text/javascript">document.write(getLabel("Credits"))</script></a></li>
			</ul>
		</li>
	</ul>
	<br class="myClear" />
</div>
<section id="intro">
	<div class='wrapper'>
		<div id='loading'></div>
		<nav id='main'>
			<ul>
				<li><a class="active" href="">home</a></li>
				<li><a  href="/support">support</a></li>
				<li><a  href="/about-us">about us</a></li>
				<li><a  href="/blog">blog</a></li>
			</ul>
		</nav>
		<div class='titles'>
			<h1>PagineBaby</h1>
			<p class='abstract'></p>
			<a href="#" onclick="nearMe(0);">Find Near Me</a>
		</div>
		<div class="iPhone" id="search-results">
			<? echo($str); ?>	
		</div>
		<div id="intro-icon"><img src="/pb_files/img/icon.png" /></div>
		<div id="intro-icon-mobile"><img src="/pb_files/img/icon.png" /></div>
	</div>
</section>
</body>
</html>