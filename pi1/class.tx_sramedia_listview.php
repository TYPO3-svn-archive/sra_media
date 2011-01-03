<?php
global $TYPO3_CONF_VARS;
/**
 * Diese Klasse erzeugt eine Liste von Büchern / Medien aus der SRA Buchdatenbank
 *
 * Für die Erzeugung wird auf die DB Tabellen in Form von Arrays zugegriffen. Die 
 * Arrays werden in der Oberklasse einmalig durch auslesen der Tabellen der DB erzeugt.
 * Für die Erzeugung der Buchliste wird in einer Schleife über alle Bücher iteriert.
 * Die Daten werden innerhalb der Schleife gefiltert, und nur dann hinzugefügt, wenn 
 * entsprechende Filter erfolgreich sind.
 *
 * Download als Bibtex Liste
 * =========================
 * ...
 *
 * Neu: Sicherer Download der PDF Dateien
 * ======================================
 * bei gesetzten Parametern
 * - tx_sramedia_pi1[download]=1
 * - tx_sramedia_pi1[showUid]= (uid der Publiaktion)
 * wird ein Download gestartet. Es wird geschaut, ob die Publikation
 * in der Datenbank exisitert, und ob man im FE eingeloggt ist. Daraufhin
 * wird geschaut, ob zu der Publikation eine Datei existiert (und diese 
 * auf der Festplatte vorhanden ist.) Diese Datei wird mit file_get_contents
 * ausgelesen und als application/pdf Stream gesendet.
 * Das Upload Verzeichnis sollte mit einer .htaccess Datei gesperrt werden.
 *
 * Listenansicht
 * =============
 * Für die Ausgabe wird das HTML-Template ###TEMPLATE_LIST### verwendet. 
 * (siehe auch doc_template.tmpl)
 *
 */
class tx_sramedia_listview implements tx_sramedia_view {
	// tx_sramedia_pi1 - Object Referenz
	var $parent;	

	/**
	 * @see interface.tx_sramedia_view.php
	 */
	function setParentObj(&$obj) {
		$this->parent = $obj;
	}

	/**
	 * HTML Output erzeugen
	 * @see interface.tx_sramedia_view.php
	 */
	function getOutput() {
		
		// Bibtex Liste herunterladen?
		if ($this->parent->piVars['export'] == 1) {
			$this->exportBibtex();
			return;
		}

		// PDF Datei Download für FE User
		// ==========================================================================================
		$download = $this->parent->piVars['download'];
		$uid = $this->parent->piVars['showUid'];
		// - tx_sramedia_pi1[download] = 1? (Soll ein Download stattfinden?)
		// - tx_sramedia_pi1[showUid] > 0 (Welches Buch / Publikation angezeigt wird, hängt
		// von diesem Parameter ab)
		// - FE-User eingeloggt?
		if ($download == 1 && $uid > 0 && $GLOBALS['TSFE']->fe_user->user['uid']) {

			// Es wird geschaut, ob das Buch tatsächlich in der DB existiert
			if (array_key_exists($uid, $this->parent->books)) {			
				// Existiert eine PDF Datei, so steht diese in der DB im Feld "download"
				$filename = strval($this->parent->books[$uid]['download']);

				// Falls Dateiname existiert, und Datei physikalisch auf der Festplatte vorhanden ist
				if (strlen($filename) > 1 && file_exists("uploads/tx_sramedia/".$filename)) {
					// sicheren Download starten
					header("Content-Type: application/pdf");
					header("Content-Disposition: attachment; filename=\"".$filename."\"");
					header("Cache-Control: no-cache, must-revalidate");
					// das Upload Verzeichnis sollte mit einer htaccess Datei gesperrt werden.
					// Der Webserver sollte dennoch Zugriff auf die Datei haben, da der Inhalt
					// mit file_get_contents ausgelesen wird, und somit für FE Benutzer
					// heruntergeladen werden kann.
					echo file_get_contents("uploads/tx_sramedia/".$filename);
					die();
				}
			}
		}


		// Listenansicht
		// ========================================
		// erst wenn search-button geklickt wurde,
		// zeige Liste
		if (!isset($this->parent->piVars['submit'])) {
			return;
		}

		// unser Subpart
		$subpart=$this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_LIST###');
		//eine einzelne Reihe 
		$singlerow=$this->parent->cObj->getSubpart($subpart,'###CONTENT###');  
		$liste='';
		$notempty = false; // Bisher ist die Liste leer
		$bookscount = 0;	
		foreach ($this->parent->books as $key => $value) {

			// --- Filter Funktionen ---

			// Medientyp Filter (Buch, CD, DVD...
			if (isset($this->parent->piVars['sra_type']) && $this->parent->piVars['sra_type'] >= 0) {
				if ($value['type'] != $this->parent->piVars['sra_type']) {
					continue;
				}
			}

			// Themenbereich Filter
			if (isset($this->parent->piVars['sra_area']) && $this->parent->piVars['sra_area'] >= 0) {
				if ($value['area'] != $this->parent->piVars['sra_area']) {
					continue;
				}
			}

			// Standort Filter
			if (isset($this->parent->piVars['sra_callnumber']) && $this->parent->piVars['sra_callnumber'] >= 0) {
				if ($value['callnumber'] != $this->parent->piVars['sra_callnumber']) {
					continue;
				}
			}

			// Vorhanden/Verliehen Filter
			if (isset($this->parent->piVars['sra_status'])) {
				// Vorhanden?
				if ($this->parent->piVars['sra_status'] == 0) {
					if ($value['conferredto'] != 0) {
						continue;
					}
				} elseif ($this->parent->piVars['sra_status'] == 1) { // Verliehen?
					if ($value['conferredto'] == 0 || $value['conferredto'] == NULL) {
						continue;
					}
				}
			}

			// Suchwort Filter
			$match = false;
			if (strlen($this->parent->piVars['swords']) > 2) {
				$searchFieldList = explode(",", $this->parent->searchFieldList);

				// Suchwort im Feld "field" suchen
				foreach($searchFieldList as $k => $field) {
					// Falls Suchwort nicht gefunden wurde, 
					// dann wird das nächste Feld durchsucht 
					if (!stristr($value[$field], $this->parent->piVars['swords'])) {
						continue;
					} else {
						// Ansonsten wird die Schleife abgebrochen
						// Suchwort gefunden.
						$match = true;
						break;
					}
				}

				// 
				if (!$match) {
					continue;
				}

			}

			// Personen Filter
			if (
				isset($this->parent->piVars['sra_type']) && 	// SRA-Typ gesetzt
				$this->parent->piVars['sra_type'] == 3 &&		// SRA-Typ == Publikation
				isset($this->parent->piVars['sra_person']) &&	// SRA-Person ausgewählt
				$this->parent->piVars['sra_person'] != "-1" &&	// SRA-Person != keine Person
				// Medium ist nicht mit dieser Person verknüpft
				array_search($this->parent->piVars['sra_person'],$this->parent->book_person_mm[$value['uid']]) === FALSE
			) {
				continue;
			}

			// Ab diesem Punkt kann man Zaehlen
			$markerArray = $this->getItemMarkerArray($value);
			$bookscount++;

			// Link zur Single-Ansicht
			$wrappedSubpartArray = array();
			$temp_conf = array();
			$temp_conf['parameter'] = $this->parent->lConf['single_pid'];
			$temp_conf['additionalParams'] = '&tx_sramedia_pi1[showUid]='.$value['uid'];
			$wrappedSubpartArray['###LINK_ITEM###']=$this->parent->cObj->typolinkWrap($temp_conf);
			$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), $wrappedSubpartArray);
			$notempty = true;
		}

		$subpartArray['###CONTENT###']=$liste;      

		// Export Bibtex
		$subpartArray['###PIVARS_INPUT_VALUE###']="";
		foreach ($this->parent->piVars as $key => $value) {
			$subpartArray['###PIVARS_INPUT_VALUE###'].='<input type="hidden" name="tx_sramedia_pi1['.$key.']" value="'.$value.'" />';
		}

		$subpartArray['###BOOKSCOUNT###']=$bookscount." ".$this->parent->pi_getLL('pi_list_bookscount');

		if ($notempty) {
			return $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,$subpartArray,array());     
		} else {
			return $this->parent->pi_getLL('empty_list');
		}
	}

	/**
	 *	Bibtex Export Funktion
	 */
	function exportBibtex() {
		// unser Subpart
		$subpart=$this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_BIBTEXEXPORT###');
		//eine einzelne Reihe 
		$subpartentry=$this->parent->cObj->getSubpart($subpart,'###CONTENT###');  
		$singlerow=$this->parent->cObj->getSubpart($subpartentry,'###FIELDS_TO_DISPLAY###');  
		$globalMarkerArray = array();
		$books = "";
		$count = 0;

		foreach ($this->parent->books as $key => $value) {
			$subpartArray = array();

			// --- Filter Funktionen ---

			// Medientyp Filter (Buch, CD, DVD...
			if (isset($this->parent->piVars['sra_type']) && $this->parent->piVars['sra_type'] >= 0) {
				if ($value['type'] != $this->parent->piVars['sra_type']) {
					continue;
				}
			}

			// Themenbereich Filter
			if (isset($this->parent->piVars['sra_area']) && $this->parent->piVars['sra_area'] >= 0) {
				if ($value['area'] != $this->parent->piVars['sra_area']) {
					continue;
				}
			}

			// Standort Filter
			if (isset($this->parent->piVars['sra_callnumber']) && $this->parent->piVars['sra_callnumber'] >= 0) {
				if ($value['callnumber'] != $this->parent->piVars['sra_callnumber']) {
					continue;
				}
			}

			// Vorhanden/Verliehen Filter
			if (isset($this->parent->piVars['sra_status'])) {
				// Vorhanden?
				if ($this->parent->piVars['sra_status'] == 0) {
					if ($value['conferredto'] != 0) {
						continue;
					}
				} elseif ($this->parent->piVars['sra_status'] == 1) { // Verliehen?
					if ($value['conferredto'] == 0 || $value['conferredto'] == NULL) {
						continue;
					}
				}
			}

			// Suchwort Filter
			$match = false;
			if (strlen($this->parent->piVars['swords']) > 2) {
				$searchFieldList = explode(",", $this->parent->searchFieldList);

				// Suchwort im Feld "field" suchen
				foreach($searchFieldList as $k => $field) {
					// Falls Suchwort nicht gefunden wurde, 
					// dann wird das nächste Feld durchsucht 
					if (!stristr($value[$field], $this->parent->piVars['swords'])) {
						continue;
					} else {
						// Ansonsten wird die Schleife abgebrochen
						// Suchwort gefunden.
						$match = true;
						break;
					}
				}

				// 
				if (!$match) {
					continue;
				}

			}

			// Personen Filter
			if (
				isset($this->parent->piVars['sra_type']) && 	// SRA-Typ gesetzt
				$this->parent->piVars['sra_type'] == 3 &&		// SRA-Typ == Publikation
				isset($this->parent->piVars['sra_person']) &&	// SRA-Person ausgewählt
				$this->parent->piVars['sra_person'] != "-1" &&	// SRA-Person != keine Person
				// Medium ist nicht mit dieser Person verknüpft
				array_search($this->parent->piVars['sra_person'],$this->parent->book_person_mm[$value['uid']]) === FALSE
			) {
				continue;
			}

			$globalMarkerArray['###ENTRY_TYPE###'] = $value['bibtex_type'];
			$count++;
			
			//  BibtexKey 
			if (strlen($value['bibtex_key'])>1) {
				$globalMarkerArray['###BIBTEX_KEY###'] = $value['bibtex_key'];
			} else {
				$globalMarkerArray['###BIBTEX_KEY###'] = $value['signature'];
			}

			$liste = "";
			$liste .= $this->getRequiredFields($value,$singlerow);
			$liste .= $this->getOptionalFields($value,$singlerow);
			$subpartArray['###FIELDS_TO_DISPLAY###'] = $liste;
			$books .= $this->parent->cObj->substituteMarkerArrayCached($subpartentry,$globalMarkerArray,$subpartArray, array()); 
		}

		$subpartArray['###CONTENT###'] = $books;
		$content = $this->parent->cObj->substituteMarkerArrayCached($subpart,array(),$subpartArray,array());


		// Download
		header("Content-Type: text/plain");
		header("Content-Disposition: attachment; filename=\"SRA-Bibliothek.bib\"");
		header("Cache-Control: no-cache, must-revalidate");
		echo "# This BibTeX File has been generated by\r\n";
		echo "# the Typo3 extension 'SRA Media'\r\n";
		echo "#\r\n";
		echo "# Date: ".date("d/m/Y")."\r\n";
		echo "# ".$count." Items.";
		echo $content;
		die();
		return;
	}

	/**
	 * Pflichtfelder
	 *
	 * Diese Methode fügt zur Ausgabe entsprechend dem in der DB gespeicherten Literaturtyp 
	 * des Buches zugehörigen Feldwerte ein.
	 *
	 * @see tx_sramedia_pi1
	 */
	function getRequiredFields(&$row,$singlerow) {
		$liste = "";
		$markerArray = array();
		$next = false;
		foreach ($this->parent->bibtex_entry_types[$row['bibtex_type']]["required"] as $key => $value) {

			if ($next) {
				$markerArray['###ISNEXT###'] = ",\r\n";
			} else {
				$markerArray['###ISNEXT###'] = "";
			}
			
			if (stristr($this->parent->getDBFieldName($value), " or ")
  				||(stristr($this->parent->getDBFieldName($value), " and/or "))) {
				$fields = explode(" ", $this->parent->getDBFieldName($value));
				$field0 = $fields[0];
				$field1 = $fields[2];
				$markerArray['###FIELD_HEADER###'] = $field0;
				$markerArray['###FIELD_VALUE###'] = $row[$field0];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
				$markerArray['###ISNEXT###'] = ",\r\n";
				$markerArray['###FIELD_HEADER###'] = $field1;
				$markerArray['###FIELD_VALUE###'] = $row[$field1];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			} else {
				$markerArray['###FIELD_HEADER###'] = $value;
				$markerArray['###FIELD_VALUE###'] = $row[$this->parent->getDBFieldName($value)];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			}

			$next = true;
		}

		return $liste;
	}

	/**
	 * Optionale Felder
	 *
	 * Diese Methode fügt zur Ausgabe entsprechend dem in der DB gespeicherten Literaturtyp 
	 * des Buches zugehörigen Feldwerte ein.
	 */
	function getOptionalFields(&$row,$singlerow) {
		$liste = "";
		$markerArray = array();
		$next = false;
		foreach ($this->parent->bibtex_entry_types[$row['bibtex_type']]["optional"] as $key => $value) {

			if ($row['bibtex_type'] != "misc" || $next) {
				$markerArray['###ISNEXT###'] = ",\r\n";
			} else {
				$markerArray['###ISNEXT###'] = "";
			}

			if (stristr($this->parent->getDBFieldName($value), " or ")
  				||(stristr($this->parent->getDBFieldName($value), " and/or "))) {
				$fields = explode(" ", $this->parent->getDBFieldName($value));
				$field0 = $fields[0];
				$field1 = $fields[2];
				$markerArray['###FIELD_HEADER###'] = $field0;
				$markerArray['###FIELD_VALUE###'] = $row[$field0];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
				$markerArray['###ISNEXT###'] = ",\r\n";
				$markerArray['###FIELD_HEADER###'] = $field1;						
				$markerArray['###FIELD_VALUE###'] = $row[$field1];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			} else {
				$markerArray['###FIELD_HEADER###'] = $value;
				$markerArray['###FIELD_VALUE###'] = $row[$this->parent->getDBFieldName($value)];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			}

			$next = true;
		}

		return $liste;
	}


	/**
	 * Füllt die Marker mit den entsprechenden Buchdaten
	 *
	 * @see class.tx_sramedia_pi1.php#getHeaders
	 */
	function getItemMarkerArray ($row) {
		$markerArray=array();
		$this->parent->getHeaders($markerArray);
		$markerArray['###SHOW_LISTLIMIT###'] = $this->parent->pi_getLL('pi_list_browseresults_show');
		$markerArray['###BOOK_SIGNATURE###'] = $row['signature']; 
		$markerArray['###BOOK_TITLE###'] = $row['title'];
		$markerArray['###BOOK_AUTHOR###'] = $row['author'];
		$markerArray['###BOOK_SRAPERSON###'] = "";

		// Verknüpfungen zu SRA-Mitarbeitern
		if ($row['type'] == 3) {
			$markerArray['###BOOK_SRAPERSON###'] .= "<ul>";

			foreach ($this->parent->book_person_mm[$row['uid']] as $k => $person_uid) {
				$markerArray['###BOOK_SRAPERSON###'] .= 
					"<li>"
					.$this->parent->person[$person_uid]['name'].$this->parent->person[$person_uid]['vorname'].
					"</li>";
			}
			
			$markerArray['###BOOK_SRAPERSON###'] .= "</ul>";
		}
		
		$markerArray['###BOOK_PUBYEAR###'] = $row['pubyear'];
		$markerArray['###BOOK_TYPE###'] = $this->parent->type[$row['type']];
		$markerArray['###BOOK_AREA###'] = $this->parent->area[$row['area']]['name'];
		$markerArray['###BOOK_NO###'] = $this->parent->callnumber[$row['callnumber']]['name'];
		
		if($row['conferredto'] == 0) {
			$markerArray['###CSS-TR###'] = 'sra-media-pi1-listtable-status0';
		} else {
			$markerArray['###CSS-TR###'] = 'sra-media-pi1-listtable-status1';
		}

		return $markerArray;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_listview.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_listview.php']);
}

?>
