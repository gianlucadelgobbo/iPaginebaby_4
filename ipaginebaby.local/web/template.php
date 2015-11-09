<!DOCTYPE html>

<!--[if lt IE 7 ]> <html class="ie ie6 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" dir="ltr" lang="en-US"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" dir="ltr" lang="en-US"><!--<![endif]-->
<!-- the "no-js" class is for Modernizr. -->
<head>
	<title><? echo($title ? $title." | " : ""); ?>PagineBaby | iPhone, iPad and Android App to quickly find information about your surroundings!</title>
	<meta charset="UTF-8">
	<meta name = "viewport" content = "initial-scale = 1.0, maximum-scale=1, minimum-scale = 1.0"> 
	<meta name="description" content="iPhone, iPad and Android App to quickly find information about your surroundings!">
	<meta name="author" content="Rana Elettrica Soc. Coop.">
	<link rel="canonical" href="<? echo($_SERVER['PHP_SELF']); ?>" />
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/mobile/ico/114x114.png">

	<link rel="stylesheet" href="/web/style.css">
	<link rel="stylesheet" href="/web/flexslider.css">	
	<link rel="stylesheet" href="/_admin/_script/jquery/css/south-street/jquery-ui-1.8.24.custom.css">	

	<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="/_admin/_script/jquery/js/jquery-ui-1.8.24.custom.min.js"></script>
	<!-- GOOGLEAPI -->
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script> 
	<!-- GOOGLEAPI -->
	<script type="text/javascript" charset="utf-8">
		var jQT = false;
	</script>
	<script type="text/javascript" src="/js/script.js" charset="utf-8"></script>
	<script type="text/javascript" src="/js/dictionary.js"></script>
	<script type="text/javascript" src="/js/cat.js"></script>
	<script type="text/javascript" charset="utf-8">
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
	</script>
	<!-- all our JS is at the bottom of the page, except for Modernizr. -->
	<script type="text/javascript" src="/web/js/modernizr-1.7.min.js"></script>
	<script type="text/javascript" src="/web/js/jquery.easing.js?ver=1.3"></script>
	<script type="text/javascript" src="/web/js/jquery.preloadr.js"></script>
	<script type="text/javascript" src="/web/js/jquery.flexslider-min.js"></script>
	<script type="text/javascript" src="/web/js/responsive-slider.js"></script>
	<script>
	  var template_path = "/web";
	</script>
	<script src="/web/js/home.js?v=5"></script>
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
				<li><div id="slider"></div><hr /></li>
				<li><? echo( $site->dataObj->getLabel("Posizione dispositivo")); ?> <span class="toggle"><input type="checkbox" name="autopos" id="autopos" value="1" onchange="setMode();" /></span></li>
				<li id="position"><? echo( $site->dataObj->getLabel("Nessuna posizione impostata")); ?> <a href="#editmap" onclick="editPosMap()"><? echo( $site->dataObj->getLabel("imposta manualmente")); ?></a></li>
			</ul>
		</li>
	</ul>
	<br class="myClear" />
</div>
<div id="editmap" title="Basic dialog" style="display:none;">
	<div class="toolbar">
		<form id="search-map-form" action="#">
			<ul class="edgetoedge">
		  		<li><input type="text" name="search-text" placeholder="<? echo( $site->dataObj->getLabel("Citta")); ?>" id="search-map" /></li>
			</ul>
		</form>
		<div id="map_canvas">map div</div>
	</div>
</div>
</body>
</html>
