function removePerson(id) {
	// <ul> - Tag
	var personlist = document.getElementById("sra-media-person-list");
	var li_tags = personlist.getElementsByTagName("li");
	for (var i=0; i<li_tags.length; i++) {
		if (li_tags[i].getAttribute("id") == "person"+id) {
			personlist.removeChild(li_tags[i]);
			return;
		}
	}
	
}

function addPerson() {
	var select_person = document.getElementById("sra-media-person-select");
	var personlist_info = document.getElementById("sra-media-person-info");
	var personlist_container = document.getElementById("sra-media-person-list-container");
	var personlist_row = document.getElementById("sra-media-person-resultlist");
//	var sramedia_addform = document.getElementById("sramedia-addform");

	if (select_person.value != '-1') {
		personlist_container.className = "visible";
		personlist_row.className = "visible";
		var personlist = document.getElementById("sra-media-person-list");
		var item = document.createElement("li");
		var option_tags = select_person.getElementsByTagName("option");
		
		for (var i=0; i<option_tags.length; i++) {
			if(option_tags[i].value == select_person.value) {
				var hidden_input = document.createElement("input");
				hidden_input.setAttribute("type", "hidden");
				hidden_input.setAttribute("value", select_person.value);
				hidden_input.setAttribute("name", "tx_sramedia_pi1[person]["+select_person.value+"]");
				//sramedia_addform.appendChild(hidden_input);
				item.appendChild(hidden_input);
				var personname = document.createTextNode(option_tags[i].firstChild.data+"("+select_person.value + ")");
				item.appendChild(personname);
				item.setAttribute("id", "person"+select_person.value);
				var link_node = document.createElement("a");
				var link_text = document.createTextNode("(entfernen)");
				link_node.setAttribute("href", "javascript:removePerson("+select_person.value+")");
				link_node.appendChild(link_text);
				item.appendChild(link_node);
				personlist.appendChild(item);
				personlist_info.className = "visible";
			}
		}
	}

}

// Diese Funktion macht die Personen-Select Box und die darumliegende TABLE-Zeile
// sichtbar
function checkOptionId() {
	var select_type = document.getElementById("sra-media-type-select");
	var tr_person = document.getElementById("sra-media-person-row");
	var div_personlist = document.getElementById("sra-media-person-list");
	var personlist_row = document.getElementById("sra-media-person-resultlist");
	var person_publikation_info = document.getElementById("person-list-info");
	
	if (select_type.value == 3) {
		tr_person.className = "visible";
		personlist_row.className = "visible";
		person_publikation_info.className = "visible";
	} else {
		tr_person.className = "hidden";
		personlist_row.className = "hidden";
		person_publikation_info.className = "hidden";
	}

}

function updateInvNr (nextinv) {
	var invnr = document.getElementById("next_inv");
	invnr.style.backgroundColor = "#F5FF67";
	invnr.setAttribute("value" ,nextinv);
}

function showArea() {

	var areaselect = document.getElementById("sra-media-areaselect");
	var areaid = areaselect.value;
	var booklist = document.getElementById("tx-sramedia-area"+areaid);
	var container = document.getElementById("book-selected-area");
	var signature_lookuptable = document.getElementById("signature_lookuptable");
	signature_lookuptable.value = "";
	signature_lookuptable.value += booklist.firstChild.nodeValue;
}

// Zeigt die Personen-Selectbox, falls SRA-Publikation ausgewählt wurde
// Gilt nur für die Suche
function showPersonSelect() {
	var type_select = document.getElementById("sra-media-type-select");
	var person_select_row = document.getElementById("sra-media-person-select-row");
	
	// FALLS Typ==SRA-Publikation
	if (type_select.value == 3) {
		person_select_row.className="visible";
	} else {
		person_select_row.className="hidden";
	}
}
