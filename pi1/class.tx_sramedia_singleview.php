<?php
/**
 * Detailansicht
 */
class tx_sramedia_singleview implements tx_sramedia_view {
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
		// URL Variable showUid gesetzt?
		if (!isset($this->parent->piVars['showUid'])) {
			return $this->parent->pi_getLL('no_uid');
		} else {
			// Entsprechenden Eintrag aus DB holen
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				"*",
				"tx_sramedia_books",
				"deleted=0 AND hidden=0 AND uid='".intval($this->parent->piVars['showUid'])."'"
			);
			// DB Error?
			if (!$GLOBALS['TYPO3_DB']->sql_error()) {
				$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);				
				// Eintrag vorhanden? 
				if ($count == 1) {
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);				
					$subpart=$this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_DETAILVIEW###');
					//eine einzelne Reihe 
					$singlerow=$this->parent->cObj->getSubpart($subpart,'###FIELDROW###');  
					$liste='';
					// Ausgabe der zusätzlichen Felder, wie z.B. Verlinkungen zu Personen
					$liste.= $this->getAdditionalFields($row,$singlerow);
					// Ausgabe der Pflichtfelder
					$liste.= $this->getRequiredFields($row,$singlerow);
					// Ausgabe der optionalen Felder
					$liste.= $this->getOptionalFields($row,$singlerow);
					// Ausgabe Action Message
					if ($this->parent->piVars['action'] == 0 && isset($this->parent->piVars['submit']) && $GLOBALS['TSFE']->fe_user->user['uid']>0) {
						$updateArray = array('conferredto'=>$GLOBALS['TSFE']->fe_user->user['uid']);
						$query = $GLOBALS['TYPO3_DB']->UPDATEquery('tx_sramedia_books', 'uid='.intval($this->parent->piVars['showUid']),
						$updateArray);
						$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
						$row['conferredto']=$GLOBALS['TSFE']->fe_user->user['uid'];
					} 

					if ($this->parent->piVars['action'] == 1 && isset($this->parent->piVars['submit']) && $GLOBALS['TSFE']->fe_user->user['uid']>0) {
						$updateArray = array('conferredto'=>0);
						$query = $GLOBALS['TYPO3_DB']->UPDATEquery('tx_sramedia_books', 'uid='.intval($this->parent->piVars['showUid']),
						$updateArray);
						$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
						$row['conferredto']=0;
					} 

					// Ausgabe des Ausleihbutton
					if ($row['conferredto'] == 0) {
						$form=$this->parent->cObj->getSubpart($subpart,'###FORM###');
						$markerArray['###URL###'] = $this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"top",array("tx_sramedia_pi1[showUid]"=>$row['uid']));
						$markerArray['###BUTTON###'] = "Ausleihen";
						$markerArray['###ACTION###'] = "0"; // ausleihen
						$subpartArray['###FORM###'] = $this->parent->cObj->substituteMarkerArrayCached($form,$markerArray,array(), array());
					} else {
						if ($GLOBALS['TSFE']->fe_user->user['uid'] == $row['conferredto']) {
							$form=$this->parent->cObj->getSubpart($subpart,'###FORM###');
							$markerArray['###URL###'] = $this->parent->pi_getPageLink($GLOBALS['TSFE']->id,"top",array("tx_sramedia_pi1[showUid]"=>$row['uid']));
							$markerArray['###BUTTON###'] = "Zuruecklegen";
							$markerArray['###ACTION###'] = "1"; // zurueckgeben
							$subpartArray['###FORM###'] = $this->parent->cObj->substituteMarkerArrayCached($form,$markerArray,array(), array());
						} else {
							$subpartArray['###FORM###'] = "<p><i>Verliehen an: ".$this->parent->feuser[$row['conferredto']]['name']."</i></p><br />";
						}
					}
					$subpartArray['###FIELDROW###']=$liste;      
					return $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,$subpartArray,array());     
				} else {
					// Eintrag mit der entsprechenden ID ist nicht vorhanden
					return $this->parent->pi_getLL('no_uid');
				}
			} else {
				// DB Anfrage Fehlerhaft
				return $this->parent->pi_getLL('db_error');
			}
		}
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

		foreach ($this->parent->bibtex_entry_types[$row['bibtex_type']]["required"] as $key => $value) {
			if (stristr($this->parent->getDBFieldName($value), " or ")
  				||(stristr($this->parent->getDBFieldName($value), " and/or "))) {
				$fields = explode(" ", $this->parent->getDBFieldName($value));
				$field0 = $fields[0];
				$field1 = $fields[2];
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$field0);
				$markerArray['###FIELD_VALUE###'] = $row[$field0];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$field1);						
				$markerArray['###FIELD_VALUE###'] = $row[$field1];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			} else {
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$this->parent->getDBFieldName($value));
				$markerArray['###FIELD_VALUE###'] = $row[$this->parent->getDBFieldName($value)];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			}
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

		foreach ($this->parent->bibtex_entry_types[$row['bibtex_type']]["optional"] as $key => $value) {
			if (stristr($this->parent->getDBFieldName($value), " or ")
  				||(stristr($this->parent->getDBFieldName($value), " and/or "))) {
				$fields = explode(" ", $this->parent->getDBFieldName($value));
				$field0 = $fields[0];
				$field1 = $fields[2];
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$field0);
				$markerArray['###FIELD_VALUE###'] = $row[$field0];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$field1);						
				$markerArray['###FIELD_VALUE###'] = $row[$field1];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			} else {
				$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_'.$this->parent->getDBFieldName($value));
				$markerArray['###FIELD_VALUE###'] = $row[$this->parent->getDBFieldName($value)];
				$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
			}
		}

		return $liste;
	}

	/**
	 * zusätzliche Felder
	 *
	 * Zu den zusätzlichen Feldern gehören alle Felder, wie z.B. Themenbereich, 
	 * Verlinkte Personen, Standort und alle übrigen Felder, welche nicht zu den
	 * Bibtex Typen hinzugezählt werden.
	 */
	function getAdditionalFields(&$row,$singlerow) {
		$liste = "";
		$markerArray = array();
		// Signatur
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_signature');
		$markerArray['###FIELD_VALUE###'] = $row["signature"];
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		// INV Nummer
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_invnr');
		$markerArray['###FIELD_VALUE###'] = $row["invnr"];
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		// Literaturtyp (Bibtex)
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_bibtex_type');
		$markerArray['###FIELD_VALUE###'] = $row["bibtex_type"];
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		// Themenbereich
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_area');
		$markerArray['###FIELD_VALUE###'] = $this->parent->area[$row["area"]]["name"];
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		// Standort
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_sralocation');
		$markerArray['###FIELD_VALUE###'] = $this->parent->callnumber[$row["callnumber"]]["name"];
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		// Medium
		$markerArray['###FIELD_HEADER###'] = $this->parent->pi_getLL('listFieldHeader_type');
		switch ($row['type']) {
			case 0:
				$markerArray['###FIELD_VALUE###'] = "Buch";
				break;
			case 1:
				$markerArray['###FIELD_VALUE###'] = "CD";
				break;
			case 2:
				$markerArray['###FIELD_VALUE###'] = "DVD";
				break;
			case 3:
				$markerArray['###FIELD_VALUE###'] = "SRA Publikation";
				break;
			default:
				$markerArray['###FIELD_VALUE###'] = "Buch";
				break;
		}
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($singlerow,$markerArray,array(), array());
		return $liste;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_singleview.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_singleview.php']);
}


?>
