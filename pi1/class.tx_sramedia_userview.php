<?php

class tx_sramedia_userview implements tx_sramedia_view {
	var $parent;

	function setParentObj(&$obj) {
		$this->parent = $obj;
	}

	function getOutput() {

		if ($this->parent->piVars['export'] == 1) {
			$this->exportBibtex();
			return;
		}

		// unser Subpart
		$subpart=$this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_LIST###');
		//eine einzelne Reihe 
		$singlerow=$this->parent->cObj->getSubpart($subpart,'###CONTENT###');  
		$liste='';
		$notempty = false; // Bisher ist die Liste leer
		$count = 0;
			
		foreach ($this->parent->books as $key => $value) {

			// --- Filter Funktionen ---

			// Vorhanden/Verliehen Filter
			if ($GLOBALS['TSFE']->fe_user->user['uid'] != $value['conferredto']) {
				continue;
			}

			$count++;
			$markerArray = $this->getItemMarkerArray($value);

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
		$subpartArray['###PIVARS_INPUT_VALUE###']="";
		$subpartArray['###BOOKSCOUNT###'] = $count." ".$this->parent->pi_getLL("pi_list_bookscount");

		if ($notempty) {
			return $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,$subpartArray,array());     
		} else {
			return $this->parent->pi_getLL('empty_user_list');
		}

	}

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

			// --- Filter Funktionen ---

			// Vorhanden/Verliehen Filter
			if ($GLOBALS['TSFE']->fe_user->user['uid'] != $value['conferredto']) {
				continue;
			}

			$count++;
			$globalMarkerArray['###ENTRY_TYPE###'] = $value['bibtex_type'];
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

		$subpartArray['###CONTENT###']=$books;
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_userview.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_userview.php']);
}


?>
