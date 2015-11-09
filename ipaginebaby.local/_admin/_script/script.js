function onEdit(id) {
	$( "#edit" ).dialog( "open" );
	url = "http://ipb.flyer.it/api/explore";
    //$("#form_loading").show();
	$.post(url, {id:id}, function(data, textStatus) {
		fillDett(data[0]);
	}, "json");
}
function onDelete(id,div) {
	if(confirm("Sicuro di voler cancellare questo record?")) {
		url = "http://ipb.flyer.it/api/delete";
	    //$("#form_loading").show();
		$.post(url, {id:id}, function(data, textStatus) {
			if(data.msg==1) $(div).parent().parent().remove();
		}, "json");
	}
}
function fillDett(place) {
	console.log(place);
	$('#form_pid3').val(place['id']);
	catA = place['cat'].split(",");
	vis = [];
	hid = [];
	catEnabled = ["309","315","314","313","311","310"];
	for(var a=0;a<catA.length;a++) {
		tmp = catA[a].split("|");
		console.log("bella");
		console.log(tmp[1]);
		console.log(catEnabled.indexOf(tmp[1]));
		console.log("bella");
		if (catEnabled.indexOf(tmp[1])!=-1) {
			vis.push(catA[a]);
		} else {
			hid.push(catA[a]);
		}
	}
	for(var a=0;a<vis.length;a++) {
		if (a>0) {
			addCat($('form .cat')[a-1]);
		}
		$($('form .cat')[a]).val(vis[a]);
	}
	for(var a=0;a<hid.length;a++) {
		$($('form .cat')[0]).parent().after("<input type=\"hidden\" name=\"cat[]\" class=\"cat\" value=\""+hid[a]+"\" />");
	}
	$('#form_name').val(place['name']);
	$('#form_txt').val(place['txt']);
	$('#form_website').val(place['website']);
	$('#form_email').val(place['email']);
	$('#form_address').val(place['address']);
	$('#form_street_number').val(place['street_number']);
	$('#form_zip').val(place['zip']);
	$('#form_city').val(place['city']);
	$('#form_country').val(place['country']);
	$("#form_mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+place['lat']+","+place['lng']+")\"><img alt=\"Mappa nuova location\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+place['lat']+","+place['lng']+"&sensor=false\" height=\"100\" /></a>");
	$("#form_ll").val(place['lat']+","+place['lng']);
	$('#form_tel').val(place['tel']);
	$('#form_mtel').val(place['mtel']);
	$('#form_fax').val(place['fax']);
	$('#form_img').val(place['img'].replace('http://ipb.flyer.it',''));
	$("input:[name='form_hl']").each(function(a) {
	    if($(this).val() == place['hl']){
	        $(this).attr('checked','checked');
	    }
	});
	$("input:[name='form_pbcard']").each(function(a) {
	    if($(this).val() == place['pbcard']){
	        $(this).attr('checked','checked');
	    }
	});
	$("input:[name='form_new']").each(function(a) {
	    if($(this).val() == place['new']){
	        $(this).attr('checked','checked');
	    }
	});
	$("input:[name='form_public']").each(function(a) {
	    if($(this).val() == place['public']){
	        $(this).attr('checked','checked');
	    }
	});
	return false;
}
function addCat(elem){
	if($(elem).parent().parent().find('form .cat').length<3) {
		$(elem).parent().after($(elem).parent().clone());
	}
}
function placeSave() {
	var chk = true;
	if (!$('#form_name').val()) {
		chk = false;
		alert("Inserisci il nome");
	}
	if (!$('#form_ll').val()) {
		chk = false;
		alert("Controlla la posizione sulla mappa");
	}
	if ($('#form_website').val() && !checkWebsite($('#form_website').val())) {
		chk = false;
		alert("Controlla il sito web, ricordati di inserire http://");
	}
	if ($('#form_email').val() && !checkEmail($('#form_email').val())) {
		chk = false;
		alert("Controlla la mail");
	}
	if (chk) {
		//str = "";
		obj = {
			"name":$('#form_name').val(),
			"address":$('#form_address').val(),
			"street_number":$('#form_street_number').val(),
			"author":$('#form_author').val(),
			"city":$('#form_city').val(),
			"country":$('#form_country').val(),
			"zip":$('#form_zip').val(),
			"txt":$('#form_txt').val(),
			"cat":[],
			"website":$('#form_website').val(),
			"email":$('#form_email').val(),
			"tel":$('#form_tel').val(),
			"mtel":$('#form_mtel').val(),
			"fax":$('#form_fax').val(),
			"ll":$('#form_ll').val(),
			"pid":$('#form_pid3').val(),
			"img":$('#form_img').val(),
			"pbcard":$("input[name=form_pbcard]:checked").val(),
			"hl":$("input[name=form_hl]:checked").val(),
			"public":$("input[name=form_public]:checked").val(),
			"new":$("input[name=form_new]:checked").val()
		}
		$('form .cat').each(function(index) {
			if ($(this).val()) obj.cat.push($(this).val());
		});
		url = "http://ipb.flyer.it/api/newplace";
		console.log(obj);
	    $("#loading").show();
		$.post(url, obj, function(data, textStatus) {
			console.log(data);
			$( "#edit" ).dialog( "close" );
		}, "json");
		return true;
	} else {
		return false;
	}
}
/* GOOGLE */
var marker;
var map;
function initialize(lat,lng) {
	$( "#map" ).dialog( "open" );
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
		var m = marker.getPosition();
		var lat = m.lat();
		var lng = m.lng();
		console.log(lng);
		console.log(lat);
		getGeoLocationData(lat,lng);
	});
}
function toggleBounce() {
	if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}
function getGeoLocationData(llat,llon) {
   	console.log(llat + ',' + llon);
   	$("#form_ll").val(llat + ',' + llon);
	url = "http://ipb.flyer.it/api/getGeoLocationData.php?latlng="+llat+","+llon;
	$("#form_mappaNew").html("<a href=\"#editmap\" onclick=\"initialize("+llat+","+llon+")\"><img alt=\"Mappa nuova location\" src=\"http://maps.googleapis.com/maps/api/staticmap?zoom=14&size=280x100&maptype=roadmap&markers=color:red|color:red|label:S|"+llat+","+llon+"&sensor=false\" height=\"100\" /></a>");
	$.getJSON(url, {}, function (data,textStatus) {
		if(data.results.length) {
			for(var i=0;i<data.results[0].address_components.length;i++){
			    switch(data.results[0].address_components[i]['types'][0]) {
			        case "route" :
			        $('#form_address').val(data.results[0].address_components[i]['long_name']);
			        break
			        case "street_number" :
			        $('#form_street_number').val(data.results[0].address_components[i]['long_name']);
			        break
			        case "locality" :
			        $('#form_city').val(data.results[0].address_components[i]['long_name']);
			        break
			        case "country" :
			        $('#form_country').val(data.results[0].address_components[i]['long_name']);
			        break
			        case "postal_code" :
			        $('#form_zip').val(data.results[0].address_components[i]['long_name']);
			        break
			    }
			
			}
		}
	});
}
/* GOOGLE END */

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

