var places;
var currentIndex;
var lat;
var lng;
var cat;
var fava;
var mode;
var location_timeout;

var lang 			= (window.navigator.userLanguage || window.navigator.language).split("-")[0];
var defaultLang 	= "en";
var site_path 		= "http://paginebaby.it";
var facebookFP 		= "PagineBabyItaly";
var catEnabled 		= ["309","315","314","313","311","310"];
var startLat 		= 41.90101705184494;
var startLng		= 12.501811981201172;
var radius 			= 5;

/*
$(function(){
	window.onresize = function(){
		myResize();
	}
    $('body').bind('turn', function(event, info){
    	myResize()
        //console.log(info.orientation); // landszipe or profile
    });
});
function myResize(){
	//$(document).width() // $(jQT.hist[0].hash+" .s-scrollwrapper").height();
}
*/
$(function(){
	$('#radius-field').val(radius+" Km");
	$("#add").hide();
	$("#loading").hide();
	mode = 1;
	if (jQT) setTimeout("nearMe(0)",2000);

	$('#slider').slider({
		value: radius*10,
		min: 1,
		max: 1000,
		slide: function(event, ui) {
			radius = (ui.value/10);
			$('#radius-field').val(radius+" Km");
		}
	});

    $("#search").submit(function(event, info) {
        var text = $("input[id=search-text]", this);
        text.blur();
        var results = $("#search-results", this).empty();
        results.append($("<li>", {
            "class": "sep",
            text: getLabel("Cerco")+' "' + text.val() + '"'
        }));
	  	searchPlaces(0,"");
        return false;       
    });
    $("#search-map-form").submit(function(event, info) {
        var text = $("input[id=search-map]", this);
        text.blur();
        //console.log(text.val());
        geocoder.geocode( { 'address': text.val()}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				//console.log();
				marker.setPosition(results[0].geometry.location);
				var m = marker.getPosition();
				map.setCenter(m);
				getLocationData(m.lat(),m.lng());
			} else {
				alert(getLabel("Località non trovata"));
			}
		});
        return false;       
    });
});

function catSearch(cc){
	$("input[id=search-text]").val("");
	if (jQT) jQT.goTo('#ipb', 'slidedown');
	$("#search2 option").removeAttr("selected");
	searchPlaces(0,cc);
	return false; 
}
	
function nearMe(skip) {
	$('#search').hide('slidedown');
	//'#search-results' = '#search-results';
	if (skip==0) {
		$('#search-results').find('.arrow').remove();
		$('#search-results').html('<li class="sep">'+getLabel("Cerco vicino alla posizione impostata")+'</li>');
	}
	if (lat && lng) {
		$("#loading").show();
		searchPlaces(skip,undefined);
	} else if (mode) {
		getPosition();
	} else {
		$('#search-results').html('<li class="sep">'+getLabel("Risultati")+"</li><li>"+getLabel("Per visualizzare i risultati vicino a te devi autorizzare l\'applicazione a conoscere la tua posizione oppure imposta manualmente la località in cui cercare")+"</li>");
	}
}

/*
function drawNearMe(skip) {
    url = site_path+"/api/explore?lang="+lang+"&ll=" + lat + "," + lng + "&radius="+radius + "&skip="+skip;
	//if ('#search-results') $('#search-results').empty();
	'#search-results' = '#near-results';
	$.getJSON(url, {}, function(data){
		$("#loading").hide();
		if (skip==0) {
			places = data.data;
		} else {
			for(item in data.data) places.push(data.data[item]);
		}
		$($('#search-results').find('.sep')[0]).html(data.msg);
		$($('#search-results').find('.more')[0]).remove();
		$('#search-results').append(placesSrc(data.data)+(data.skip ? "<li class=\"more\"><a href=\"#\" onclick=\"nearMe("+data.skip+"); return false;\">"+getLabel("Visualizza altri risultati")+"</a></li>" : ""));
		if (jQT) jQT.setPageHeight();
	});
}
*/
function searchPlaces(skip,cc) {
	cat = cc;
	str = $("input[id=search-text]").val();
    //if (str.length>1 || cat){
		$("#loading").show();
	    url = site_path+"/api/explore?lang="+lang+"&radius="+radius+(lat && lng ? "&ll=" + lat + "," + lng : "")+(cat && cat!='undefined' ? "&cat="+cat : (str ? "&q="+str : ""))+"&skip="+skip;
	    //console.log(url);
		if (skip==0) $('#search-results').empty();
		$.getJSON(url, {}, function(data){
			if (skip==0) {
				places = data.data;
			} else {
				for(item in data.data) places.push(data.data[item]);
			}
			//console.log(places);
			if (skip==0) {
				$('#search-results').html('<li class="sep"></li>');
			}
			$($('#search-results').find('.sep')[0]).html(data.msg);
			$($('#search-results').find('.more')[0]).remove();
			$('#search-results').append(placesSrc(data.data)+(data.skip ? "<li class=\"more\"><a href=\"#\" onclick=\"searchPlaces("+data.skip+",'"+cat+"'); return false;\">"+getLabel("Visualizza altri risultati")+"</a></li>" : ""));
	    	$("#add").show();
			$("#loading").hide();
			if (jQT) jQT.setPageHeight();
		});
    /*
    } else {
    	alert(getLabel("Inserisci almeno 2 caratteri"));
    }
    */
}

function loadFav(uid) {
	fava = [];
    url = site_path+"/api/explore?lang="+lang+"&fav=" + uid;
	$.getJSON(url, {}, function(data){
		if (data.length) {
			for (var i = 0; i < data.length; i++) {
				//console.log(data[i].id);
				fava.push(data[i].id);
			}
			$('#fav-results').html(placesSrc(data));
		} else {
			$('#fav-results').html("<li>"+getLabel("Nessun preferito")+"</li>");
		}
		if (jQT) jQT.setPageHeight();
	});
}

function placesSrc(data) {
	str = "";
	if (data && data.length) {
		for (var i = 0; i < data.length; i++) {
			str+= '<li class="arrow'+(data[i]['hl']==1 ? ' hl' : '')+'">';
			str+= '<a href="#ipbdett" onclick="fillDett('+i+'); return false;">';
			if (data[i].img) {
				str+= '<span class="imgListCnt"><img src="'+data[i].img+'" align="left" height="40" class="imgList" /></span>';
			}
			str+= '<span class="txtListCnt"><span class="titList">'+data[i].name+' <br /></span>';
			if (data[i].pbcard==1) {
				str+= '<span class="pbcListCnt">PB-CARD</span> ';
			}
			if (data[i].distance) {
				str+= "<span class=\"stitTitList\">"+getLabel("Distanza")+": </span> <span class=\"stitList\">"+(Math.round(data[i].distance*100)/100)+" Km</span> ";
			}
			if (data[i].cat_str) str+= "<span class=\"stitTitList\">"+getLabel("Categorie")+": </span> <span class=\"stitList\">"+data[i].cat_str+"</span> ";
			str+= '</span> ';
			str+= '</a>';
			/*str+= '<small class="counter">'+data[i].checkin+'</small>';*/
			str+= '</li>';
		}
	} else {
		str+= "<li>"+getLabel("Nessun risultato")+"<br />"+getLabel("Se non trovi risultati prova ad aumentare il raggio della ricerca dalle Impostazioni. Oppure fai di meglio: utilizza la funzione Aggiungi per inserire le strutture che conosci")+"</li>";
	}
	return str;
}

function generateGallery(i) {
	$('#tabbar').hide();
	$('#photo').remove();
	console.log(places[i]['gallery']);
	if (jQT) var bella = jQT.generateGallery('photo', places[i]['gallery'],{defaultIndex:0});
	console.log($('#photo'));
	if (jQT) jQT.goTo('#photo', 'slide down');
}

function fillDett(i) {
	currentIndex = i;
	updateCurrentPosition();
	$('pid').val(places[i]['id']);
	str = "<div class=\"dettCntMain\">";
	if (places[i]['img']) str+= "<p class=\"imgCnt\"><img src=\""+places[i]['img']+"\" /></p>";
	if (places[i]['gallery']) str+= "<p><a class=\"galleryButton\" href=\"#photo\" onclick=\"generateGallery("+i+");return false;\">"+getLabel("Gallery")+"</a></p>";
	if (places[i]['img'] && places[i]['gallery']) str+= "<hr />";
	/*
	places[i]['gallery'] = [
		  {src:"http://www.nasa.gov/images/content/450090main_image_1653_946-710.jpg",width: 946,height: 710},
		  {src:"http://www.thinkgeek.com/images/products/front/b597_rock_paper_scissors_lizard_spock.jpg"}
	];
	*/
	str+= "<h3>"+places[i]['name']+"</h3>";
	str+= "<input type=\"hidden\" id=\"permalink\" value=\""+site_path+"/"+places[i]['permalink']+"\" />";
	str+= "<input type=\"hidden\" id=\"author_loc\" value=\""+places[i]['author']+"\" />";
	str+= "<p>"+places[i]['address']+(places[i]['street_number'] ? " "+places[i]['street_number'] : "")+", "+places[i]['zip']+" - "+places[i]['city']+"</p>";
	if (places[i]['pbcard']==1) str+= "<p><span class=\"pbcListCnt\">PB-CARD</span></p>";
	if (places[i].distance) str+= "<p><span class=\"stitTit\">"+getLabel("Distanza")+": </span> "+Math.floor(places[i]['distance']*100)/100+" km</p>";
	str+= "<div><div class=\"stitTit left\">"+getLabel("Voto")+": </div> <div id=\"r1\" class=\"rate_widget\"><div class=\"star_1 ratings_stars\"></div><div class=\"star_2 ratings_stars\"></div><div class=\"star_3 ratings_stars\"></div><div class=\"star_4 ratings_stars\"></div><div class=\"star_5 ratings_stars\"></div></div><br class=\"myClear\" /></div>";
	str+= "<p><span class=\"stitTit\">"+getLabel("Checkins")+": </span> "+places[i]['checkin']+"</p>";
	if (places[i]['tel']) str+= "<p><span class=\"stitTit\">"+getLabel("Tel")+": </span> <a href=\"tel:"+places[i]['tel']+"\">"+places[i]['tel']+"</a></p>";
	if (places[i]['fax']) str+= "<p><span class=\"stitTit\">"+getLabel("Fax")+": </span> "+places[i]['fax']+"</p>";
	if (places[i]['email']) str+= "<p><span class=\"stitTit\">"+getLabel("Email")+": </span> <a href=\"mailto:"+places[i]['email']+"\">"+places[i]['email']+"</a></p>";
	if (places[i]['website']) str+= "<p class=\"website\"><span class=\"stitTit\">"+getLabel("Sito web")+": </span> <a href=\""+places[i]['website']+"\" target=\"_blank\">"+places[i]['website']+"</a></p>";
	if (places[i]['cat']) str+= "<p class=\"catcat\"><span class=\"stitTit\">"+getLabel("Categorie")+": </span> "+places[i]['cat_str']+"</p>";
	/*str+= "<p>categories:</p>";
	for (var a = 0; a < places[i].categories.length; a++) {
		str+= "<p>"+places[i].categories[a].name+"</p>";
	}*/
	str+= "<hr />";
	str+= "<div class=\"txt\">"+places[i]['txtHtml']+"<br class=\"myClear\" /></div>";
	//str+= "<p>tipCount: "+places[i]['stats']['tipCount']+"</p>";
	//str+= "<p>usersCount: "+places[i]['stats']['usersCount']+"</p>";
	str+= "<hr />";
	str+= "<div id=\"mappaDett\"><img alt=\""+getLabel("Mappa di")+": "+places[i]['name']+"\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x200&maptype=roadmap&markers=color:red|color:red|label:S|"+places[i]['lat']+","+places[i]['lng']+"&sensor=false\" /></div>";
	str+= "<hr />";
	/*
	places[i]['gallery'] = [
		  {src:"http://www.nasa.gov/images/content/450090main_image_1653_946-710.jpg",width: 946,height: 710},
		  {src:"http://www.thinkgeek.com/images/products/front/b597_rock_paper_scissors_lizard_spock.jpg"}
	];
	if (jQT) jQT.generateGallery("photo", places[i]['gallery'],{defaultIndex:0});
	str+= "<hr />";
	*/
	str+= "<ul class=\"individual\">";
	//console.log(fava);
		str+= "<li><a class=\"greenButton\" rel=\"external\" target=\"_blank\" href=\"#\" id=\"openmaps\">"+getLabel("ARRIVA QUI")+"</a></li>";
		str+= "<li><a class=\"greenButton\" onclick=\"$('#pid2').val("+places[i]['id']+");\" href=\"#propietario\">"+getLabel("SEGNALA")+"</a></li>";

	if (fava && $.inArray(places[i]['id'],fava)!=-1) {
		str+= "<li><a class=\"greenButton\" onclick=\"favorities("+places[i]['id']+");\" href=\"#\">"+getLabel("RIMUOVI DAI PREFERITI")+"</a></li>";
	} else {
		str+= "<li><a class=\"greenButton\" onclick=\"favorities("+places[i]['id']+");\" href=\"#\">"+getLabel("AGGIUNGI AI PREFERITI")+"</a></li>";
	}
	str+= "<li><a class=\"greenButton\" onclick=\"checkin("+places[i]['id']+");\" href=\"#\">"+getLabel("CHECK-IN")+"</a></li>";
	//str+= "</ul>";
	//str+= "<ul class=\"individual\">";
	str+= "<li><a class=\"greenButton\" onclick=\"rate("+places[i]['id']+");\" href=\"#rate\">"+getLabel("VOTA")+"</a></li>";
	//console.log($('#uid').val());
	if (places[i]['author']==$('#uid').val()) {
		str+= "<li><a class=\"greenButton\" onclick=\"onEdit("+i+");\" href=\"#aggiungi\">"+getLabel("MODIFICA")+"</a></li>";
	} else {
		str+= "<li><a class=\"greenButton\" onclick=\"$('#pid2').val("+places[i]['id']+");\" href=\"#propietario\">"+getLabel("SEI IL TITOLARE")+"?</a></li>";
	}
	str+= "</ul>";
	str+= "</div>";
	if (jQT) {
		$('#dett').html("<div id=\"dettCnt\">"+str+"</div>");
		$('#dett').css('-webkit-transform', 'translate3d(0px, 0, 0px)');
		jQT.setPageHeight();
	} else {
		$('#search-results').html("<div id=\"dettCnt\">"+str+"</div>");
		$('#search-results').css('-webkit-transform', 'translate3d(0px, 0, 0px)');
	}
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
	//console.log(vis);
	//console.log(hid);
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
	$("#mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+places[i]['lat']+","+places[i]['lng']+",13,'getLocationData')\"><img alt=\""+getLabel("Mappa nuova location")+"\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+places[i]['lat']+","+places[i]['lng']+"&sensor=false\" height=\"100\" /></a>");
	$("#ll").val(places[i]['lat']+","+places[i]['lng']);
	$('#tel').val(places[i]['tel']);
	/*
	$('#mtel').val(places[i]['mtel']);
	$('#fax').val(places[i]['fax']);
	*/
}

function addCat(elem){
	//console.log($($(elem).parent().find("select")[0]));
	if($(elem).parent().parent().find('.cat').length<3) {
		//console.log($(elem).parent());
		$(elem).parent().after($(document.createElement('li')).html($($(elem).parent().find("select")[0]).clone()));
	}
	if (jQT) jQT.setPageHeight();
}
	
function setRadius() {
	radius = $('#radius-field').val();
	if (radius == 0) {
		radius = 0.5;
		$('#radius-field').val(radius);
	}
}
	
function onAdd() {
	if ($('#author').val()) {
		if (mode) {
			updateCurrentPosition();
		}
		$('#name').val($("input[id=search-text]").val());
	} else {
		if(confirm(getLabel("Per aggiungere una location devi eseguire il login")+".\n"+getLabel("Ti vuoi loggare ora")+"?")) {
			login();
		}
	}
}
function updateCurrentPosition() {
	navigator.geolocation.getCurrentPosition(
		function(pos){
			//console.log(pos);
		    if (pos.coords){
				getLocationData(pos.coords.latitude,pos.coords.longitude);
			}
		}
	);
}
function setMode() {
	mode = $('input:checkbox[id=autopos]:checked').val();
	//console.log(mode);
	if (mode) {
		getPosition();
	} else {
		$("#position").html(getLabel("Nessuna posizione impostata")+" <a href=\"#editmap\" onclick=\"editPosMap()\">"+getLabel("imposta manualmente")+"</a>");
	}
}
function getPositionError() {
	$("#loading").hide();
	$('#search-results').html("<li class=\"sep\">"+getLabel("Risultati")+"</li><li>"+getLabel("Per visualizzare i risultati vicino a te devi autorizzare l\'applicazione a conoscere la tua posizione oppure imposta manualmente la località in cui cercare")+"</li>");
	if($("#autopos").attr('checked')) {
		//if(confirm("Per impostare la tua posizione devi abilitare l'applicazione a leggere la tua posizione.\nVuoi farlo ora?")) {
		if(confirm(getLabel("Per impostare la tua posizione devi abilitare l'applicazione a leggere la tua posizione")+".")) {
			//console.log(getLabel("OK"));
		} else {
			//console.log(getLabel("NO"));
		}
	}
	$("#autopos").removeAttr('checked');
}
function getPosition() {
	location_timeout = setTimeout("getPositionError()", 5000);
	$("#loading").show();
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(pos){
			//console.log(pos);
		    if (pos.coords){
				clearTimeout(location_timeout);
				$("#autopos").attr('checked','checked');
			    setPositionData(pos.coords.latitude,pos.coords.longitude)
			    searchPlaces(0,undefined);
			}
		});
	}
}

function setPositionData(llat,llng) {
	lat = llat;
	lng = llng;
	$("#position").html("<a href=\"#editmap\" onclick=\"editPosMap()\"><img alt=\""+getLabel("Mappa nuova location")+"\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+lat+","+lng+"&sensor=false\" height=\"100\" /></a><br /><a href=\"#editmap\" onclick=\"editPosMap()\">"+getLabel("Modifica la posizione")+"</a>");
	
}

function editPosMap() {
	$("#autopos").removeAttr('checked');
	mode = $('input:checkbox[id=autopos]:checked').val();
	if (lat && lng) {
		initialize(lat,lng,13,'setPositionData');
	} else {
		initialize(startLat,startLng,6,'setPositionData');
	}
	if (!jQT) $('#editmap').dialog({ width: 840,title:getLabel("Modifica mappa") }); 
}

function getLocationData(llat,llng) {
   	$("#ll").val(llat + ',' + llng);
	$("#mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+llat+","+llng+",13,'getLocationData')\"><img alt=\""+getLabel("Mappa nuova location")+"\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+llat+","+llng+"&sensor=false\" height=\"100\" /></a>");
	url = site_path+"/api/getGeoLocationData.php?latlng="+llat+","+llng;
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
			if (currentIndex) {
				saddr = "http://maps.google.com/maps?saddr="+escape($('#address').val()+($('#street_number').val() ? ", "+$('#street_number').val() : "")+", "+$('#zip').val()+", "+$('#city').val()+", "+$('#country').val())+"&daddr="+escape(places[currentIndex]['address']+(places[currentIndex]['street_number'] ? " "+places[currentIndex]['street_number'] : "")+", "+places[currentIndex]['zip']+", "+places[currentIndex]['city']+", "+places[currentIndex]['country']);
				$("#openmaps").attr('href',saddr);
			}
		}
	});
}
function placeSave() {
	var chk = true;
	if (!$('#name').val()) {
		chk = false;
		alert("Inserisci il nome");
	}
	if (!$('#ll').val()) {
		chk = false;
		alert(getLabel("Controlla la posizione sulla mappa"));
	}
	if ($('#website').val() && !checkWebsite($('#website').val())) {
		chk = false;
		alert(getLabel("Controlla il sito web, ricordati di inserire http://"));
	}
	if ($('#email').val() && !checkEmail($('#email').val())) {
		chk = false;
		alert(getLabel("Controlla la mail"));
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
		url = site_path+"/api/newplace";
	    $("#loading").show();
		$.post(url, obj, function(data, textStatus) {
			places[currentIndex] = data[0];
			fillDett(currentIndex);
			$('#search-results').html("<li class=\"sep\">"+getLabel("Risultati")+"</li>"+placesSrc(places));
			if (jQT) jQT.setPageHeight();
			$('#pid').val(data);
			if (jQT) jQT.goBack();
			var params = {
				method: 'feed',
				access_token: at,
				name: $("#dettCnt h3").text(),
				link: $("#permalink").val(),
				picture: $($("#dettCnt img")[0]).attr("src"),
				caption: $("#dettCnt .catcat").text(),
				description: $("#dettCnt .txt").text(),
				message: getLabel("Ho inserito questo nuovo posto")+"!!!"
			};
			console.log(params);
			if(confirm(getLabel("Grazie per il nuovo inserimento")+"!!!\n\n"+getLabel("Vuoi condividere su Facebook")+"?")) {
				FB.api('/'+facebookFP+'/feed', 'post', params, function(response) {
					console.log(response);
					FB.api('/me/feed', 'post', params, function(response) {
						console.log(response);
						FB.api('/'+$("#author_loc").val()+'/feed', 'post', params, function(response) {
							console.log(response);
							alert(getLabel("Grazie per la condivisione")+"!!!");
							$("#loading").hide();
						});
					});
				});
			} else {
				$("#loading").hide();
				return false;
			}
		}, "json");
		return true;
	} else {
		return false;
	}
}

function sendMail(form) {
	var mailObj = $(form).serialize();
	var n = $("#segnalazioniMsg input:[name='name']").val();
	var e = $("#segnalazioniMsg input:[name='email']").val();
	var m = $("#segnalazioniMsg textarea:[name='message']").val();
	var p = $("#segnalazioniMsg input:[name='pid']").val();
	//console.log(mailObj);
	var chk = true;
	if (!n) {
		chk = false;
		alert(getLabel("Inserisci il nome"));
	}
	if (!m) {
		chk = false;
		alert(getLabel("Inserisci il messaggio"));
	}
	if (!e || !checkEmail(e)) {
		chk = false;
		alert(getLabel("Controlla la mail"));
	}
	if (chk) {
		//str = "";
		obj = {
			"name":n,
			"email":e,
			"pid":p,
			"message":m
		}
		url = site_path+"/api/message";
	    $("#loading").show();
		$.post(url, obj, function(data, textStatus) {
			alert(getLabel("Grazie per aver inviato il messaggio")+"!!!");
	    	$("#loading").hide();
			$('#pid').val(data);
			if (jQT) jQT.goBack();
		}, "json");
		return true;
	} else {
		return false;
	}
}

function checkin(pid) {
	if ($('#uid').val()) {
		$("#loading").show();
	    url = site_path+"/api/checkin?pid=" + pid + "&uid=" + $("#uid").val();
		$.getJSON(url, {}, function(data){
			if (data.err){
				alert(getLabel("Oggi hai già fatto il checkin quì")+"!!!");
				$("#loading").hide();
			} else {
				var params = {
					method: 'feed',
					access_token: at,
//					to: '49687544642',
					name: $("#dettCnt h3").text(),
//					link: $($("#dettCnt .website a")[0]).attr("href") ? $($("#dettCnt .website a")[0]).attr("href") : window.location.href,
					link: $("#permalink").val(),
					picture: $($("#dettCnt img")[0]).attr("src"),
					caption: $("#dettCnt .catcat").text(),
					description: $("#dettCnt .txt").text(),
					message: getLabel("Sto qui")+"!!!"
				};
//				FB.ui(params, function(obj) {
					//console.log(obj);
	//			});
				//alert(getLabel("Grazie per aver fatto il checkin")+"!!!");
				if(confirm(getLabel("Grazie per aver fatto il checkin")+"!!!\n\n"+getLabel("Vuoi condividere su Facebook")+"?")) {
					FB.api('/'+facebookFP+'/feed', 'post', params, function(response) {
						console.log(response);
						FB.api('/me/feed', 'post', params, function(response) {
							console.log(response);
							FB.api('/'+$("#author_loc").val()+'/feed', 'post', params, function(response) {
								console.log(response);
								alert(getLabel("Grazie per la condivisione")+"!!!");
								$("#loading").hide();
								/*
								if (!response || response.error) {
								} else {
									alert(getLabel("Grazie per aver fatto il checkin")+"!!!");
								}
								*/
							});
						});
					});
				} else {
					$("#loading").hide();
					return false;
				}
			}
		});
	} else {
		if(confirm(getLabel("Per fare il checkin devi eseguire il login")+".\n"+getLabel("Ti vuoi loggare ora")+"?")) {
			login();
		} else {
			return false;
		}
	}
}

function favorities(pid) {
	if ($('#uid').val()) {
		$("#loading").show();
	    url = site_path+"/api/favorities?pid=" + pid + "&uid=" + $("#uid").val();
		$.getJSON(url, {}, function(data){
			if (data.err){
				alert(getLabel("Rimosso dai preferiti")+"!!!");
				$("#loading").hide();
			} else {
				var params = {
					method: 'feed',
					access_token: at,
					name: $("#dettCnt h3").text(),
					link: $("#permalink").val(),
					picture: $($("#dettCnt img")[0]).attr("src"),
					caption: $("#dettCnt .catcat").text(),
					description: $("#dettCnt .txt").text(),
					message: getLabel("Ho inserito tra i preferiti questo posto")+"!!!"
				};
				if(confirm(getLabel("Grazie di aver inserito tra i preferiti")+"!!!\n\n"+getLabel("Vuoi condividere su Facebook")+"?")) {
					FB.api('/'+facebookFP+'/feed', 'post', params, function(response) {
						console.log(response);
						FB.api('/me/feed', 'post', params, function(response) {
							console.log(response);
							FB.api('/'+$("#author_loc").val()+'/feed', 'post', params, function(response) {
								console.log(response);
								alert(getLabel("Grazie per la condivisione")+"!!!");
								$("#loading").hide();
							});
						});
					});
				} else {
					$("#loading").hide();
					return false;
				}
			}
			loadFav($('#uid').val());
		});
		return false;
	} else {
		if(confirm(getLabel("Per inserire tra i preferiti devi eseguire il login")+".\n"+getLabel("Ti vuoi loggare ora")+"?")) {
			login();
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
		//if (jQT) jQT.goTo("#rate","slide");
	} else {
		if(confirm(getLabel("Per votare devi eseguire il login")+".\n"+getLabel("Ti vuoi loggare ora")+"?")) {
			login();
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
		url = site_path+"/api/rate"+str;
		//alert(url);
	    $("#loading").show();
		//$.getJSON(url, {}, rateOk);
		$('#r2').each(function(ii) {
		    $(this).find('.star_' + parseInt($('#rateval').val())).prevAll().andSelf().addClass('ratings_vote_big');
		    $(this).find('.star_' + parseInt($('#rateval').val())).nextAll().removeClass('ratings_vote_big'); 
		});
		$.getJSON(url, {}, function (data) {
			var params = {
				method: 'feed',
				access_token: at,
				name: $("#dettCnt h3").text(),
				link: $("#permalink").val(),
				picture: $($("#dettCnt img")[0]).attr("src"),
				caption: $("#dettCnt .catcat").text(),
				description: $("#dettCnt .txt").text(),
				message: getLabel("Ho votato questo posto")+"!!!"
			};
			if(confirm(getLabel("Grazie per aver votato")+"!!!\n\n"+getLabel("Vuoi condividere su Facebook")+"?")) {
				FB.api('/'+facebookFP+'/feed', 'post', params, function(response) {
					console.log(response);
					FB.api('/me/feed', 'post', params, function(response) {
						console.log(response);
						FB.api('/'+$("#author_loc").val()+'/feed', 'post', params, function(response) {
							console.log(response);
							alert(getLabel("Grazie per la condivisione")+"!!!");
							$("#loading").hide();
						});
					});
				});
			} else {
				$("#loading").hide();
				return false;
			}

			if (jQT) jQT.goBack();
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
	url = site_path+'/api/explore?lang="+lang+"&ll=' + lat + ',' + lng + '&radius='+radius;
	$.getJSON(url, {}, function(data){
		$("#loading").hide();
		places = data;
		$('#near-results').html("<li class=\"sep\">Risultati vicino a te</li>"+placesSrc(data));
		if (jQT) jQT.goTo('#searchplaces', 'slidedown');
	});
	*/
	if (jQT) jQT.goBack();
}

/* CHECK */
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
/* CHECK END */

/* FACEBOOK */

// function login() cfr index

function loginLog(response) {
	url = site_path+"/api/login";
	$.post(url, response, function(data, textStatus) {
		//console.log(data);
	}, "json");
}

function logout() {
	str = '<li class="login"><a href="#" onClick="login()">'+getLabel("Login")+'</a></li>';
	$('#user').html(str);
	//$('.logout').hide();
	$("#fb-like-box-cnt").html("<li>"+getLabel("Per accedere a facebook devi eseguire il login")+", <a href=\"#\" onclick=\"login()\">"+getLabel("accedi ora")+"</a></li>");
	$("#fav-results").html("<li>"+getLabel("Per visualizzare i preferiti devi eseguire il login")+", <a href=\"#\" onclick=\"login()\">"+getLabel("accedi ora")+"</a></li>");
	FB.logout(function(response) {
		//console.log('logged out');
	});
}
	
function me(response) {
	if (response.authResponse) {
		FB.api('/me', function(response) {
			/*str="\n";
			for(item in response){
			str+=item+"="+response[item]+"\n";
			   for(item2 in response[item]){
					str+=item+"."+item2+"="+response[item][item2]+"\n";
			   }
			}
			//console.log('auth.login event'+str);
			*/
			str = '<li class="logout" id="fbname"></li><li class="logout" id="fbemail"></li><li class="logout" id="fblocation"></li><li class="logout"><a href="#" onclick="logout();">'+getLabel("Logout")+'</a></li>';
			$('#user').html(str);
			//$(".logout").show();
			//$(".login").hide();
			$("#author").val(response.id);
			$("#uid").val(response.id);
			$("#fbname").html(response.name);
			$("#fbemail").html(response.email);
			if (response.location && response.location.name) {
				$("#fblocation").html(response.location.name);
			} else {
				$("#fblocation").hide();
			}
			loadFav(response.id);
			FB.api('/'+facebookFP+'/feed', {},  function(response) {
				str = "";
				for (var k = 0; k < response.data.length && k < 50; k++) {
					//if (k==1) //console.log(response.data[k].actions[0].link);
					if (!response.data[k].story) {

						if (response.data[k].name) {
							url = response.data[k].link;
						} else if (response.data[k].actions && response.data[k].actions.length && response.data[k].actions[0].link) {
							url = response.data[k].actions[0].link;
						} else {
							url = "";
							//console.log("-- NO URL --------------------");
							//console.log(response.data[k]);
							//console.log("--- NO URL END -------------------");
						}
						if (url) {
							str+= "<li>";
							str+= "<a href=\""+url+"\" target=\"_blank\">";
							if (response.data[k].picture) {
								str+= "<img class=\"fbImg\" src=\""+response.data[k].picture+"\" width=\"40\" align=\"left\" />";
							} else {
								//str+= "<img class=\"fbImg\" src=\"https://graph.facebook.com/100003963453780/picture\" width=\"40\" align=\"left\" />";
								url = "";
								//console.log("-- NO IMG --------------------");
								//console.log(response.data[k]);
								//console.log("--- NO IMG END -------------------");
							}
							if (response.data[k].name) {
								str+= "<span class=\"fbTit\">"+response.data[k].name+"<br /></span>";
								str+= "<span class=\"fbAut\">"+response.data[k].from.name+"<br /></span>";
							} else {
								str+= "<span class=\"fbTit\">"+response.data[k].from.name+"<br /></span>";
							}
							str+= "<span class=\"fbMess\">"+response.data[k].message+"<br /></span>";
							//				   //console.log(response[k].messagge);
							str+= "</a>";
							str+= "</li>";
						}
					}
				}
				$("#fb-like-box-cnt").html(str);
				if (jQT) jQT.setPageHeight();
			});
			   
			//console.log(response);
			loginLog(response);
		});
	}
}
/*
function facebookWallPost() {
	//console.log('Debug 1');
	var params = {
		method: 'feed',
		name: 'Facebook Dialogs',
		link: 'https://developers.facebook.com/docs/reference/dialogs/',
		picture: 'http://fbrell.com/f8.jpg',
		caption: 'Reference Documentation',
		description: 'Dialogs provide a simple, consistent interface for applications to interface with users.'
	};
	//console.log(params);
	FB.ui(params, function(obj) {
		//console.log(obj);
	});
}

function publishStoryFriend() {
	randNum = Math.floor ( Math.random() * friendIDs.length ); 

	var friendID = friendIDs[randNum];
	if (friendID == undefined){
		alert('please click the me button to get a list of friends first');
	}else{
		//console.log("friend id: " + friendID );
		//console.log('Opening a dialog for friendID: ', friendID);
		var params = {
			method: 'feed',
			to: friendID.toString(),
			name: 'Facebook Dialogs',
			link: 'https://developers.facebook.com/docs/reference/dialogs/',
			picture: 'http://fbrell.com/f8.jpg',
			caption: 'Reference Documentation',
			description: 'Dialogs provide a simple, consistent interface for applications to interface with users.'
		};
		FB.ui(params, function(obj) { //console.log(obj);});
	}
}

FB.Event.subscribe('auth.logout', function(response) {
	//console.log(response);
	//console.log('auth.logout event');
});
 
FB.Event.subscribe('auth.sessionChange', function(response) {
	//console.log(response);
	//console.log('auth.sessionChange event');
});

FB.Event.subscribe('auth.statusChange', function(response) {
	//console.log(response);
	//console.log('auth.statusChange event');
});

 function getSession() {
	alert("session: " + JSON.stringify(FB.getSession()));
}
function getLoginStatus() {
	FB.getLoginStatus(function(response) {
		if (response.status == 'connected') {
			//console.log('logged in');
		} else {
			//console.log('not logged in');
		}
	});
}
var friendIDs = [];
var fdata;

function friends() {
	FB.api('/me/friends', { fields: 'id, name, picture' },  function(response) {
		if (response.error) {
			alert(JSON.stringify(response.error));
		} else {
			var data = document.getElementById('data');
			fdata=response.data;
			//console.log("fdata: "+fdata);
			response.data.forEach(function(item) {
				var d = document.createElement('div');
				d.innerHTML = "<img src="+item.picture+"/>"+item.name;
				data.appendChild(d);
			});
		}
		var friends = response.data;
		//console.log(friends.length); 
		for (var k = 0; k < friends.length && k < 200; k++) {
			var friend = friends[k];
			var index = 1;

			friendIDs[k] = friend.id;
			//friendsInfo[k] = friend;
		}
		//console.log("friendId's: "+friendIDs);
	});
}
function facebookWallPost() {
	//console.log('Debug 1');
	var params = {
		method: 'feed',
		name: 'Facebook Dialogs',
		link: 'https://developers.facebook.com/docs/reference/dialogs/',
		picture: 'http://fbrell.com/f8.jpg',
		caption: 'Reference Documentation',
		description: 'Dialogs provide a simple, consistent interface for applications to interface with users.'
	};
	//console.log(params);
	FB.ui(params, function(obj) { //console.log(obj);});
}

function publishStoryFriend() {
	randNum = Math.floor ( Math.random() * friendIDs.length ); 

	var friendID = friendIDs[randNum];
	if (friendID == undefined){
		alert('please click the me button to get a list of friends first');
	}else{
		//console.log("friend id: " + friendID );
		//console.log('Opening a dialog for friendID: ', friendID);
		var params = {
			method: 'feed',
			to: friendID.toString(),
			name: 'Facebook Dialogs',
			link: 'https://developers.facebook.com/docs/reference/dialogs/',
			picture: 'http://fbrell.com/f8.jpg',
			caption: 'Reference Documentation',
			description: 'Dialogs provide a simple, consistent interface for applications to interface with users.'
		};
		FB.ui(params, function(obj) { //console.log(obj);});
	}
}

*/
/* FACEBOOK END*/

/* GOOGLE */
var marker;
var map;
var callback;
var geocoder;
function initialize(lat,lng,zoom,c) {
	callback = c;
	var position = new google.maps.LatLng(lat,lng);
	if (!map) {
		geocoded = new google.maps.Geocoder();
		var mapOptions = {
			zoom: zoom,
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
			//console.log("bella");
			//console.log(callback);
			var m = marker.getPosition();
			if (callback == 'setPositionData') {
				setPositionData(m.lat(),m.lng());
			} else {
				getLocationData(m.lat(),m.lng());
			}
		});
		google.maps.event.addListener(map, 'center_changed', function() {
			//console.log("bella");
			console.log(callback);
			var m = marker.getPosition();
			if (callback == 'setPositionData') {
				setPositionData(m.lat(),m.lng());
			} else {
				getLocationData(m.lat(),m.lng());
			}
			console.log('center_changed');
		});

	} else {
		marker.setPosition(position);
		map.setCenter(marker.getPosition());
	}
}
function toggleBounce() {
	if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}
/* GOOGLE END */

function getLabel(t){
	return dictionary[lang][t] ? dictionary[lang][t] : dictionary[defaultLang][t] ? dictionary[defaultLang][t] : "_"+t+"_";
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
