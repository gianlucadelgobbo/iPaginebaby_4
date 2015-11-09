<div id="edit" title="Modifica attività">
		  		<form>
		  			<input type="hidden" name="form_ll" id="form_ll" value="" />
		  			<input type="hidden" name="form_author" id="form_author" value="" />
		  			<input type="hidden" name="form_pid" id="form_pid3" value="" />
					<ul class="edit rounded">
						<li><input type="text" class="textField" name="form_name" placeholder="Nome *" id="form_name" /></li>
						<li><input type="text" class="textField" name="form_address" placeholder="Indirizzo" id="form_address" /></li>
						<li>
							<select class="cat" name="form_cat[]" onchange="addCat(this);">
								<option value="">Categoria</option>
								<optgroup label="Sport">
									<option value="|309|322|">Sport individuali</option>
									<option value="|309|354|">Centri estivi</option>
									<option value="|309|355|">Sport di squadra</option>
									<option value="|309|394|">Federazioni</option>
								</optgroup>
								<optgroup label="Tempo libero">
									<option value="|310|316|">Parco</option>
									<option value="|310|317|">Gite fuori porta </option>
									<option value="|310|318|">Biblioteca</option>
									<option value="|310|319|">Organizzazione feste</option>
									<option value="|310|320|">Corsi e laboratori</option>
									<option value="|310|321|">Teatro</option>
									<option value="|310|340|">Museo</option>
									<option value="|310|341|">Centri estivi</option>
									<option value="|310|342|">Ludoteca</option>
									<option value="|310|344|">Cinema</option>
									<option value="|310|348|">Animali</option>
									<option value="|310|509|">Viaggi e vacanze</option>
									<option value="|310|600|">Bar e ristoranti</option>
									<option value="|310|402|">Visite guidate</option>
								</optgroup>
								<optgroup label="Salute">
									<option value="|311|324|">ASL</option>
									<option value="|311|364|">Assistenza prenatale</option>
									<option value="|311|365|">Casa di cura</option>
									<option value="|311|366|">Centro vaccinazioni</option>
									<option value="|311|367|">Consultorio</option>
									<option value="|311|370|">TSMREE/CPBA/USFMIA</option>
									<option value="|311|371|">Ospedale </option>
									<option value="|311|372|">Ambulatorio</option>
									<option value="|311|374|">Centro specialistico</option>
									<option value="|311|506|">Day-hospital</option>
									<option value="|311|601|">UTM</option>
								</optgroup>
								<optgroup label="Acquisti">
									<option value="|313|327|">Stock e Outlet</option>
									<option value="|313|328|">Centro commerciale</option>
									<option value="|313|329|">Abbigliamento</option>
									<option value="|313|330|">Calzature</option>
									<option value="|313|347|">Giocattoli</option>
									<option value="|313|373|">Alimentazione</option>
									<option value="|313|594|">Puericultura</option>
									<option value="|313|382|">Usato</option>
									<option value="|313|383|">Arredamento</option>
									<option value="|313|385|">Consumo critico</option>
									<option value="|313|386|">Libri</option>
									<option value="|313|388|">Animali</option>
									<option value="|313|389|">Parrucchieri</option>
									<option value="|313|401|">Articoli feste</option>
									<option value="|313|661|">Negozio online</option>
									<option value="|313|603|">Accessori</option>
									<option value="|313|621|">Gruppo di Acquisto Solidale (GAS)</option>
								</optgroup>
								<optgroup label="Servizi">
									<option value="|314|325|">Sostegno pratico</option>
									<option value="|314|326|">Patologie organiche</option>
									<option value="|314|331|">Assistenza legale</option>
									<option value="|314|332|">Assistenza sociale</option>
									<option value="|314|333|">Assistenza prenatale</option>
									<option value="|314|334|">Assistenza psicologica</option>
									<option value="|314|335|">Gruppo di Acquisto Solidale (GAS)</option>
									<option value="|314|338|">Corsi e laboratori</option>
									<option value="|314|350|">Assistenza neonatale</option>
									<option value="|314|379|">Disagio psichico</option>
									<option value="|314|380|">Terapie</option>
									<option value="|314|390|">Baby sitting</option>
									<option value="|314|391|">Assistenza scolastica</option>
									<option value="|314|392|">Banca del Tempo</option>
									<option value="|314|393|">Consumo critico</option>
									<option value="|314|415|">Sindrome di Down</option>
									<option value="|314|425|">Emergenze</option>
									<option value="|314|620|">Nascita Pretermine</option>
								</optgroup>
								<optgroup label="Scuola">
									<option value="|315|430|">Privata</option>
									<option value="|315|432|">Caf</option>
									<option value="|315|434|">Convenzionata</option>
									<option value="|315|435|">Centri estivi</option>
									<option value="|315|436|">Corsi e laboratori</option>
									<option value="|315|437|">Ufficio asili nido</option>
									<option value="|315|438|">Comunale</option>
									<option value="|315|439|">Statale</option>
									<option value="|315|441|">Privata religiosa</option>
									<option value="|315|448|">Animazione</option>
									<option value="|315|463|">baby Parking</option>
								</optgroup>
							</select>
						</li>
						<li><input type="text" class="textField" name="form_street_number" placeholder="Numero civico" id="form_street_number" /></li>
						<li><input type="text" class="textField" name="form_zip" placeholder="CAP" id="form_zip" /></li>
						<li><input type="text" class="textField" name="form_city" placeholder="Citta" id="form_city" /></li>
						<li><input type="text" class="textField" name="form_country" placeholder="Stato" id="form_country" /></li>
						<li id="form_mappaNew">mappa</li>
						<li><input type="text" class="textField" name="form_tel" placeholder="Telefono" id="form_tel" /></li>
						<li><input type="text" class="textField" name="form_mtel" placeholder="Cellulare" id="form_mtel" /></li>
						<li><input type="text" class="textField" name="form_fax" placeholder="Fax" id="form_fax" /></li>
					</ul>
					<ul class="edit rounded">
						<li><textarea name="form_txt" placeholder="Descrizione" id="form_txt"></textarea></li>
						<li><input type="text" class="textField" name="form_img" placeholder="Immagine" id="form_img" /></li>
						<li><input type="text" class="textField" name="form_email" placeholder="email" id="form_email" /></li>
						<li><input type="text" class="textField" name="form_website" placeholder="Sito web" id="form_website" /></li>
						<li>PB Card <input type="radio" name="form_pbcard" value="0" id="form_pbcard_0" /> <label for="form_pbcard_0">NO</label> <input type="radio" name="form_pbcard" value="1" id="form_pbcard_1" /> <label for="form_pbcard_1">SI</label></li>
						<li>Primopiano <input type="radio" name="form_hl" value="0" id="form_hl_0" /> <label for="form_hl_0">NO</label> <input type="radio" name="form_hl" value="1" id="form_hl_1" /> <label for="form_hl_1">SI</label></li>
						<li>Public <input type="radio" name="form_public" value="0" id="form_public_0" /> <label for="form_public_0">NO</label> <input type="radio" name="form_public" value="1" id="form_public_1" /> <label for="form_public_1">SI</label></li>
						<li>New <input type="radio" name="form_new" value="0" id="form_new_0" /> <label for="form_new_0">NO</label> <input type="radio" name="form_new" value="1" id="form_new_1" /> <label for="form_new_1">SI</label></li>
						<li>&nbsp;</li>
						<li><a class="greenButton" href="#" onclick="placeSave()">SALVA</a></li>
					</ul>
					<br class="myClear" />
		  		</form>
</div>
<div id="map" title="Modifica posizione">
	<div id="map_canvas"></div>
</div>
<script type="text/javascript">
$(function() {
	$( "#edit" ).dialog({
		width: 600,
		autoOpen: false,
		show: "blind",
		hide: "explode"
	});
	$( "#map" ).dialog({
		width: 700,
		autoOpen: false,
		show: "blind",
		hide: "explode"
	});

});
</script>
