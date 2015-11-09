var places;
var currentIndex;
var lat;
var lon;
var cat;
var fava;
var radius = 5;
var lastList;


$(function(){
	$('#radius-field').val(radius+" Km");
	$("#add").hide();
	nearMe();

	window.onresize = function(){
		myResize();
	}
	$('body').bind('turn', function(event, info){
		myResize()
		//console.log(info.orientation); // landszipe or profile
	});

	$('#slider').slider({
		value: radius*10,
		min: 1,
		max: 1000,
		slide: function(event, ui) {
			radius = (ui.value/10);
			$('#radius-field').val(radius+" Km");
		}
	});

	if ($("#facebook iframe").attr("style")) {
		//$("#facebook iframe").attr("style",$("#facebook iframe").attr("style").replace(320,$(document).width()));
		//$("#facebook iframe").attr("style",$("#facebook iframe").attr("style").replace(320,$("#facebook .s-scrollwrapper").height()));
	}
	$("#search").submit(function(event, info) {
		var text = $("input[id=search-text]", this);
		text.blur();
		var results = $("#search-results", this).empty();
		results.append($("<li>", {
			"class": "sep",
			text: 'Cerco "' + text.val() + '"'
		}));
	  	searchPlaces();
		return false;   	
	});
});

function myResize(){
	//$("#map_canvas").attr("style","width:"+$(document).width()+"px; height:"+$(jQT.hist[0].hash+" .s-scrollwrapper").height()+"px;");
	//alert("width:"+$(document).width()+"px; height:"+$(jQT.hist[0].hash+" .s-scrollwrapper").height()+"px;")
	if(jQT.hist.length){
		if($(jQT.hist[0].hash+" .s-scrollwrapper").height()){
			//	$("#fb-like-box-cnt").html('<div id="fb-like-box" class="fb-like-box" data-href="https://www.facebook.com/PagineBabyItaly" data-width="'+$(document).width()+'" data-height="'+$(jQT.hist[0].hash+" .s-scrollwrapper").height()+'" data-colorscheme="white" data-show-faces="false" data-border-color="#333333" data-stream="true" data-header="false"></div>');
			$("#fb-like-box-cnt").html('<fb:like-box href="http://www.facebook.com/PagineBabyItaly" width="'+$(document).width()+'" height="'+$(jQT.hist[0].hash+" .s-scrollwrapper").height()+'" show_faces="false" stream="true" header="false"></fb:like-box>');
			//console.log('<div class="fb-like-box" data-href="https://www.facebook.com/PagineBabyItaly" data-width="'+$(document).width()+'" data-height="'+$(jQT.hist[0].hash+" .s-scrollwrapper").height()+'" data-colorscheme="white" data-show-faces="false" data-border-color="#333333" data-stream="true" data-header="false"></div>');
			FB.XFBML.parse(document.getElementById('fb-like-box-cnt'));
		}
	}
}
	
function iPadTouchHandler(elem){
	//console.log('iPadTouchHandler');
	//console.log(elem);
}
	
function addCat(elem){
	if($(elem).parent().parent().find('.cat').length<3) {
		$(elem).parent().after($(elem).parent().clone());
	}
}
	
function advSearch(cc){
	cat = cc;
	$("input[id=search-text]").val();
	var text = $("input[id=search-text]", this);
	//jQT.goBack();
	jQT.goTo('#searchplaces', 'slidedown');
	text.blur();
	var results = $("#search-results", this).empty();
	results.append($("<li>", {
		"class": "sep",
		text: 'Cerco "' + text.val() + '"'
	}));
	$("#search2 option").removeAttr("selected");
	searchPlaces();
	return false; 
}
	
function setRadius() {
	radius = $('#radius-field').val();
	if (radius == 0) {
		radius = 0.5;
		$('#radius-field').val(radius);
	}
}
	
function nearMe() {
	if (lastList) $(lastList).empty();
	lastList = '#near-results';
	$(lastList).html('<li class="sep">Cerco vicino a te</li>');
	navigator.geolocation.getCurrentPosition(drawNearMe);
	$("#loading").show();
}

function drawNearMe(pos) {
	if (pos.coords){
		lat = pos.coords.latitude;
		lon = pos.coords.longitude;
		$("#searchButton").show();
		url = 'http://ipb.flyer.it/api/explore?ll=' + lat + ',' + lon + '&radius='+radius;
		if (lastList) $(lastList).empty();
		lastList = '#near-results';
		$.getJSON(url, {}, function(data){
			$("#loading").hide();
			places = data;
			$(lastList).html("<li class=\"sep\">Risultati</li>"+placesSrc(places));
		});
	} else {
		alert("bella");	
	}
}
function searchPlaces() {
	str = $("input[id=search-text]").val();
	if (str.length>1 || cat){
		$("#loading").show();
		url = 'http://ipb.flyer.it/api/explore?ll=' + lat + ',' + lon + '&radius='+radius+(cat ? '&cat='+cat : '&q='+str);
		if (lastList) $(lastList).empty();
		lastList = '#search-results';
		$.getJSON(url, {}, function(data){
			places = data;
			//$(lastList).html("<li class=\"sep\">Risultati"+(cat ? "" : " per '"+str+"'")+"</li>"+placesSrc(places));
			$(lastList).html("<li class=\"sep\">Risultati</li>"+placesSrc(places));
			cat = undefined;
			$("#add").show();
			$("#loading").hide();
		});
		cat = 0;

	} else {
		alert("Inserisci almeno 2 caratteri");
	}
}
function placesSrc(data) {
	str = "";
	if (data.length) {
		for (var i = 0; i < data.length; i++) {
			str+= '<li class="arrow">';
			str+= '<a href="#ipbdett" onclick="fillDett('+i+'); return false;">';
			if (data[i].img!="" && data[i].hl==1) {
				str+= '<span class="imgListCnt"><img src="'+data[i].img+'" align="left" height="40" class="imgList" /></span>';
			}
			str+= '<span class="txtListCnt"><span class="titList">'+data[i].name+' <br /></span>';
			if (data[i].pbcard==1) {
				str+= '<span class="pbcListCnt">PB-CARD</span> ';
			}
			if (data[i].distance) {
				str+= '<span class="stitTitList">Distanza: </span> <span class="stitList">'+(Math.round(data[i].distance*100)/100)+' Km</span> ';
			}
			if (data[i].cat_str) str+= '<span class="stitTitList">Categorie: </span> <span class="stitList">'+data[i].cat_str+'</span> ';
			str+= '</span> ';
			str+= '</a>';
			/*str+= '<small class="counter">'+data[i].checkin+'</small>';*/
			str+= '</li>';
		}
	} else {
		str+= '<li>Nessun risultato</li>';
	}
	return str;
}
function fillDett(i) {
	currentIndex = i;
	$('pid').val(places[i]['id']);
	str = "<div id=\"dettCntMain\">";
	if (places[i]['img']) str+= "<p class=\"imgCnt\"><img src=\""+places[i]['img']+"\" /></p>";
	str+= "<h3>"+places[i]['name']+"</h3>";
	str+= "<p>"+places[i]['address']+", "+places[i]['zip']+" - "+places[i]['city']+"</p>";
	if (places[i]['pbcard']==1) str+= "<p><span class=\"pbcListCnt\">PB-CARD</span></p>";
	if (places[i].distance) str+= "<p><span class=\"stitTit\">Distanza: </span> "+Math.floor(places[i]['distance']*100)/100+" km</p>";
	str+= "<div><div class=\"stitTit left\">Voto: </div> <div id=\"r1\" class=\"rate_widget\"><div class=\"star_1 ratings_stars\"></div><div class=\"star_2 ratings_stars\"></div><div class=\"star_3 ratings_stars\"></div><div class=\"star_4 ratings_stars\"></div><div class=\"star_5 ratings_stars\"></div></div><br class=\"myClear\" /></div>";
	str+= "<p><span class=\"stitTit\">Checkins: </span> "+places[i]['checkin']+"</p>";
	if (places[i]['tel']) str+= "<p><span class=\"stitTit\">Tel: </span> <a href=\"tel:"+places[i]['tel']+"\">"+places[i]['tel']+"</a></p>";
	if (places[i]['fax']) str+= "<p><span class=\"stitTit\">Fax: </span> "+places[i]['fax']+"</p>";
	if (places[i]['email']) str+= "<p><span class=\"stitTit\">Email: </span> <a href=\"mailto:"+places[i]['email']+"\">"+places[i]['email']+"</a></p>";
	if (places[i]['website']) str+= "<p><span class=\"stitTit\">Sito web: </span> <a href=\""+places[i]['website']+"\" target=\"_blank\">"+places[i]['website']+"</a></p>";
	if (places[i]['cat']) str+= "<p><span class=\"stitTit\">Categorie: </span> "+places[i]['cat_str']+"</p>";
	/*str+= "<p>categories:</p>";
	for (var a = 0; a < places[i].categories.length; a++) {
		str+= "<p>"+places[i].categories[a].name+"</p>";
	}*/
	str+= "<hr />";
	str+= "<div class=\"txt\">"+places[i]['txtHtml']+"<br class=\"myClear\" /></div>";
	//str+= "<p>tipCount: "+places[i]['stats']['tipCount']+"</p>";
	//str+= "<p>usersCount: "+places[i]['stats']['usersCount']+"</p>";
	str+= "<hr />";
	str+= "<div id=\"mappaDett\"><img alt=\"Mappa di: "+places[i]['name']+"\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x200&maptype=roadmap&markers=color:red|color:red|label:S|"+places[i]['lat']+","+places[i]['lng']+"&sensor=false\" /></div>";
	str+= "<hr />";
	str+= "<ul class=\"individual\">";
	//console.log(fava);
	if (fava && $.inArray(places[i]['id'],fava)!=-1) {
		str+= "<li><a class=\"greenButton\" onclick=\"favorities("+places[i]['id']+");\" href=\"#\">RIMUOVI DAI PREFERITI</a></li>";
	} else {
		str+= "<li><a class=\"greenButton\" onclick=\"favorities("+places[i]['id']+");\" href=\"#\">AGGIUNGI AI PREFERITI</a></li>";
	}
	str+= "<li><a class=\"greenButton\" onclick=\"checkin("+places[i]['id']+");\" href=\"#\">CHECK-IN</a></li>";
	str+= "</ul>";
	str+= "<ul class=\"individual\">";
	str+= "<li><a class=\"greenButton\" onclick=\"rate("+places[i]['id']+");\" href=\"#rate\">VOTA</a></li>";
	if (places[i]['author']==$('#uid').val()) {
		str+= "<li><a class=\"greenButton\" onclick=\"onEdit("+i+");\" href=\"#aggiungi\">MODIFICA</a></li>";
	} else {
		str+= "<li><a class=\"greenButton\" onclick=\"$('#pid2').val("+places[i]['id']+");\" href=\"#propietario\">SEI IL TITOLARE?</a></li>";
	}
	str+= "</ul>";
	str+= "</div>";
	$('#dett').html("<div id=\"dettCnt\">"+str+"</div>");
	$('.rate_widget').each(function(ii) {
		$(this).find('.star_' + parseInt(places[i]['rate'])).prevAll().andSelf().addClass('ratings_vote');
		$(this).find('.star_' + parseInt(places[i]['rate'])).nextAll().removeClass('ratings_vote'); 
	});
}

function onEdit(i) {
	$('#pid3').val(places[i]['id']);
	catA = places[i]['cat'].split(",");
	vis = [];
	hid = [];
	catEnabled = ["309","315","314","313","311","310"];
	for(var a=0;a<catA.length;a++) {
		tmp = catA[a].split("|");
		//console.log("bella");
		//console.log(tmp[1]);
		//console.log(catEnabled.indexOf(tmp[1]));
		//console.log("bella");
		if (catEnabled.indexOf(tmp[1])!=-1) {
			vis.push(catA[a]);
		} else {
			hid.push(catA[a]);
		}
	}
	for(var a=0;a<vis.length;a++) {
		if (a>0) {
			addCat($('.cat')[a-1]);
		}
		$($('.cat')[a]).val(vis[a]);
	}
	for(var a=0;a<hid.length;a++) {
		$($('.cat')[0]).parent().after("<input type=\"hidden\" name=\"cat[]\" class=\"cat\" value=\""+hid[a]+"\" />");
	}
	$('#name').val(places[i]['name']);
	$('#txt').val(places[i]['txt']);
	$('#website').val(places[i]['website']);
	$('#email').val(places[i]['email']);
	$('#address').val(places[i]['address']);
	$('#street_number').val(places[i]['street_number']);
	$('#zip').val(places[i]['zip']);
	$('#city').val(places[i]['city']);
	$('#country').val(places[i]['country']);
	$("#mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+places[i]['lat']+","+places[i]['lng']+")\"><img alt=\"Mappa nuova location\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+places[i]['lat']+","+places[i]['lng']+"&sensor=false\" height=\"100\" /></a>");
	$("#ll").val(places[i]['lat']+","+places[i]['lng']);
	$('#tel').val(places[i]['tel']);
	/*
	$('#mtel').val(places[i]['mtel']);
	$('#fax').val(places[i]['fax']);
	*/
}
function onAdd() {
	if ($('#author').val()) {
		getGeoLocationData(lat,lon);
		$('#name').val($("input[id=search-text]").val());
	} else {
		if(confirm("Per aggiungere una location devi essere loggato.\nTi vuoi loggare ora?")) {
			document.location.href = 'https://m.facebook.com/dialog/oauth?client_id=366045943462852&redirect_uri=http://ipb.flyer.it/&scope=email';
		}
	}
}
function getGeoLocationData(llat,llon) {
   	//console.log(llat + ',' + llon);
   	$("#ll").val(llat + ',' + llon);
	url = "http://ipb.flyer.it/api/getGeoLocationData.php?latlng="+llat+","+llon;
	$("#mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+llat+","+llon+")\"><img alt=\"Mappa nuova location\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+llat+","+llon+"&sensor=false\" height=\"100\" /></a>");
	$.getJSON(url, {}, function (data,textStatus) {
		if(data.results.length) {
			for(var i=0;i<data.results[0].address_components.length;i++){
				switch(data.results[0].address_components[i]['types'][0]) {
					case "route" :
					$('#address').val(data.results[0].address_components[i]['long_name']);
					break
					case "street_number" :
					$('#street_number').val(data.results[0].address_components[i]['long_name']);
					break
					case "locality" :
					$('#city').val(data.results[0].address_components[i]['long_name']);
					break
					case "country" :
					$('#country').val(data.results[0].address_components[i]['long_name']);
					break
					case "postal_code" :
					$('#zip').val(data.results[0].address_components[i]['long_name']);
					break
				}
			
			}
		}
	});
}
function placeNew() {
	var chk = true;
	if (!$('#name').val()) {
		chk = false;
		alert("Inserisci il nome");
	}
	if (!$('#ll').val()) {
		chk = false;
		alert("Controlla la posizione sulla mappa");
	}
	if ($('#website').val() && !checkWebsite($('#website').val())) {
		chk = false;
		alert("Controlla il sito web, ricordati di inserire http://");
	}
	if ($('#email').val() && !checkEmail($('#email').val())) {
		chk = false;
		alert("Controlla la mail");
	}
	if (chk) {
		//str = "";
		obj = {
			"name":$('#name').val(),
			"address":$('#address').val(),
			"street_number":$('#street_number').val(),
			"author":$('#author').val(),
			"city":$('#city').val(),
			"country":$('#country').val(),
			"zip":$('#zip').val(),
			"txt":$('#txt').val(),
			"cat":[],
			"website":$('#website').val(),
			"email":$('#email').val(),
			"tel":$('#tel').val(),
			"pid":$('#pid3').val(),
			/*
			"mtel":$('#mtel').val(),
			"fax":$('#fax').val(),
			*/
			"ll":$('#ll').val()
		}
		$('.cat').each(function(index) {
			obj.cat.push($(this).val());
		});
		url = "http://ipb.flyer.it/api/newplace";
		$("#loading").show();
		$.post(url, obj, function(data, textStatus) {
			places[currentIndex] = data[0];
			fillDett(currentIndex);
			$(lastList).html("<li class=\"sep\">Risultati</li>"+placesSrc(places));
			$("#loading").hide();
			$('#pid').val(data);
			jQT.goBack();
		}, "json");
		return true;
	} else {
		return false;
	}
}
function checkin(pid) {
	if ($('#uid').val()) {
		$("#loading").show();
		url = 'http://ipb.flyer.it/api/checkin?pid=' + pid + '&uid=' + $('#uid').val();
		$.getJSON(url, {}, function(data){
			$("#loading").hide();
			if (data.err){
				alert("Oggi hai già fatto il checkin quì!!!");
			} else {
				alert("Grazie per aver fatto il checkin!!!");
			}
		});
		return false;
	} else {
		if(confirm("Per fare il checkin devi essere loggato.\nTi vuoi loggare ora?")) {
			document.location.href = 'https://m.facebook.com/dialog/oauth?client_id=366045943462852&redirect_uri=http://ipb.flyer.it/&scope=email';
		} else {
			return false;
		}
	}
}
function loadFav(uid) {
	fava = [];
	url = 'http://ipb.flyer.it/api/explore?fav=' + uid;
	$.getJSON(url, {}, function(data){
		if (data.length) {
			for (var i = 0; i < data.length; i++) {
				//console.log(data[i].id);
				fava.push(data[i].id);
			}
		}
		$('#fav-results').html(placesSrc(data));
	});
}
function favorities(pid) {
	if ($('#uid').val()) {
		$("#loading").show();
		url = 'http://ipb.flyer.it/api/favorities?pid=' + pid + '&uid=' + $('#uid').val();
		$.getJSON(url, {}, function(data){
			$("#loading").hide();
			if (data.err){
				alert("Rimosso dai preferiti!!!");
			} else {
				alert("Grazie di aver inserito tra i preferiti!!!");
			}
			loadFav($('#uid').val());
		});
		return false;
	} else {
		if(confirm("Per inserire tra i preferiti devi essere loggato.\nTi vuoi loggare ora?")) {
			document.location.href = 'https://m.facebook.com/dialog/oauth?client_id=366045943462852&redirect_uri=http://ipb.flyer.it/&scope=email';
		} else {
			return false;
		}
	}
}
function rate(pid) {
	if ($('#uid').val()) {
		$('#pid').val(pid);
		$('.ratings_stars_active').bind('click', function() {
			var star = $(this).attr('class').split(" ")[0].replace("star_","");
			$('#rateval').val(star);
			//console.log(star);
			ratePlace()
		});
		return true;
		//jQT.goTo("#rate","slide");
	} else {
		if(confirm("Per votare devi essere loggato.\nTi vuoi loggare ora?")) {
			document.location.href = 'https://m.facebook.com/dialog/oauth?client_id=366045943462852&redirect_uri=http://ipb.flyer.it/&scope=email';
			return false;
		} else {
			return false;
		}
	}
}
function ratePlace() {
	var chk = true;
	if (!$('#pid').val()) {
		chk = false;
		alert("Ho perso la località");
	}
	if (chk) {
		str = "";
		str+= "?pid="+$('#pid').val();
		str+= "&uid="+$('#uid').val();
		//str+= "&rate="+$('input:radio[name=rate]:checked').val();
		str+= "&rate="+$('#rateval').val();
		url = "http://ipb.flyer.it/api/rate"+str;
		//alert(url);
		$("#loading").show();
		//$.getJSON(url, {}, rateOk);
		$('#r2').each(function(ii) {
			$(this).find('.star_' + parseInt($('#rateval').val())).prevAll().andSelf().addClass('ratings_vote_big');
			$(this).find('.star_' + parseInt($('#rateval').val())).nextAll().removeClass('ratings_vote_big'); 
		});
		$.getJSON(url, {}, function (data) {
			$("#loading").hide();
			jQT.goBack();
			places[currentIndex]['rate'] = data.val;
			$('.rate_widget').each(function(ii) {
				$(this).find('.star_' + parseInt(data.val)).prevAll().andSelf().addClass('ratings_vote');
				$(this).find('.star_' + parseInt(data.val)).nextAll().removeClass('ratings_vote'); 
			});
		});
	} else {
		return false;
	}
}
function rateOk (data) {
	/*
	url = 'http://ipb.flyer.it/api/explore?ll=' + lat + ',' + lon + '&radius='+radius;
	$.getJSON(url, {}, function(data){
		$("#loading").hide();
		places = data;
		$('#near-results').html("<li class=\"sep\">Risultati vicino a te</li>"+placesSrc(data));
		jQT.goTo('#searchplaces', 'slidedown');
	});
	*/
	jQT.goBack();
}
function loginLog(response) {
	url = 'http://ipb.flyer.it/api/login';
	$.post(url, response, function(data, textStatus) {
		//console.log(data);
	}, "json");
}
function trim(str) {
	var res="";
	if(str){
		if(str.length>0){
			res=ltrim(rtrim(str, "\\s"), "\\s");
		}
	}
	return res;
}
function ltrim(str, chars) {
//	chars = chars || "\\s";
	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");
}
 
function rtrim(str, chars) {
	chars = chars || "\\s";
	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");
}

function checkEmail(email){
	var res=0;
	email=trim(email);
	if(window.RegExp){
		var rexp=new RegExp("^[_a-zA-Z0-9-]+(\\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\\.[a-zA-Z0-9-]+)*(\\.([a-zA-Z]){2,4})$");
		if(rexp.test(email))
			res=1;
	}else{
		if((email.indexOf("@") > 0) && (email.indexOf(".") > 0))
			res=1;
	}
	return res;
}

	function checkWebsite(val){
		var res=0;
		if(val!="" && val!="http://"){
			var str=trim(val);
			if(str.length>10){
				if(str.substring(0,4)=="http"){
					res=1;
				}
			}
		}else{
			res=1;
		}
		return res;
	}

var marker;
var map;
function initialize(lat,lng) {
	var position = new google.maps.LatLng(lat,lng);
	var mapOptions = {
		zoom: 13,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		center: position
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	marker = new google.maps.Marker({
		map:map,
		draggable:true,
		animation: google.maps.Animation.DROP,
		position: position
	});
	google.maps.event.addListener(marker, 'click', toggleBounce);
	google.maps.event.addListener(marker, "dragend", function(obj) {
		//console.log(obj);
		//console.log(marker.getPosition());
		getGeoLocationData(obj.latLng.$a,obj.latLng.ab);
	});
}
function toggleBounce() {
	if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}
