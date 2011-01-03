<?php

/**
 * Das Suchformular
 */
class tx_sramedia_searchform implements tx_sramedia_view {
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
		// unser Subpart
		$subpart=$this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_SEARCH###');
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###FORM_URL###', $this->parent->pi_getPageLink($GLOBALS['TSFE']->id));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SWORDS###', htmlspecialchars($this->parent->piVars['swords']));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_PHRASE###', $this->parent->pi_getLL('pi_search_phrase','[Text]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_BUTTON###', $this->parent->pi_getLL('pi_search_button','[Search]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_TYPE###', $this->parent->pi_getLL('pi_search_type','[Type]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_AREA###', $this->parent->pi_getLL('pi_search_area','[Area]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_STATUS###', $this->parent->pi_getLL('pi_search_status','[Status]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_PERSON###', $this->parent->pi_getLL('pi_search_person','[Person]'));
		$subpart=$this->parent->cObj->substituteMarker($subpart, '###SEARCH_CALLNUMBER###', $this->parent->pi_getLL('pi_search_callnumber','[Callnumber]'));

		// Falls SRA-Publikation gewählt wurde, soll eine Personen-Auswahlbox angezeigt
		if (isset($this->parent->piVars['sra_type']) && $this->parent->piVars['sra_type'] == 3) {
			$subpart=$this->parent->cObj->substituteMarker($subpart, '###PERSON_ROW_CLASS###', "visible");
		} else {
			$subpart=$this->parent->cObj->substituteMarker($subpart, '###PERSON_ROW_CLASS###', "hidden");
		}

		$subpartArray = array();
		$subpartArray['###TYPE_OPTIONS###']=$this->makeTypeSelect($this->parent->cObj->getSubpart($subpart,'###TYPE_OPTIONS###'));
		$subpartArray['###PERSON_OPTIONS###']=$this->makePersonSelect($this->parent->cObj->getSubpart($subpart,'###PERSON_OPTIONS###'));
		$subpartArray['###AREA_OPTIONS###']=$this->makeAreaSelect($this->parent->cObj->getSubpart($subpart,'###AREA_OPTIONS###'));
		$subpartArray['###STATUS_OPTIONS###']=$this->makeStatusSelect($this->parent->cObj->getSubpart($subpart,'###STATUS_OPTIONS###'));
		$subpartArray['###CALLNUMBER_OPTIONS###']=$this->makeCallnumberSelect($this->parent->cObj->getSubpart($subpart,'###CALLNUMBER_OPTIONS###'));

		return $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,$subpartArray,array());     
	}

	/**
	 * Füllt die Buchtypen Selectorbox
	 */
	function makeTypeSelect($optionrow) {
		$markerArray = array();
		$liste='';
			
		foreach ($this->parent->type as $typevalue => $typename) {
			
			if (isset($this->parent->piVars['sra_type']) && $this->parent->piVars['sra_type'] == $typevalue) {
				$markerArray['###SELECTED###'] = "selected=\"selected\"";				
			} else {
				$markerArray['###SELECTED###'] = "";
			}

			$markerArray['###TYPE_VALUE###'] = $typevalue;
			$markerArray['###TYPE_NAME###'] = $typename;
			$liste .= $this->parent->cObj->substituteMarkerArrayCached($optionrow,$markerArray,array(),array()); 
		}

		return $liste;
	}

	/**
	 * Füllt die Themengebiete Auswahlbox
	 */
	function makeAreaSelect($subpart) {
		$markerArray = array();
		$liste='';
			
		foreach ($this->parent->area as $areaid => $row) {

			if ($this->parent->piVars['sra_area'] == $areaid) {
				$markerArray['###SELECTED###'] = "selected=\"selected\"";				
			} else {
				$markerArray['###SELECTED###'] = "";
			}

			$markerArray['###AREA_ID###'] = $areaid;
			$markerArray['###AREA_NAME###'] = $row['name'];
			$liste .= $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,array(),array()); 
		}

		return $liste;
	}

	/**
	 * Füllt die Personen Auswahlbox
	 */
	function makePersonSelect($subpart) {
		$markerArray = array();
		$liste='';
			
		foreach ($this->parent->person as $personid => $row) {

			if ($this->parent->piVars['sra_person'] == $personid) {
				$markerArray['###SELECTED###'] = "selected=\"selected\"";				
			} else {
				$markerArray['###SELECTED###'] = "";
			}

			$markerArray['###PERSON_ID###'] = $personid;
			$markerArray['###PERSON_NAME###'] = $row['name'].$row['vorname'];
			$liste .= $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,array(),array()); 
		}

		return $liste;
	}

	/**
	 * Füllt die Standort Auswahlbox
	 */
	function makeCallnumberSelect($subpart) {
		$markerArray = array();
		$liste='';
			
		foreach ($this->parent->callnumber as $cnid => $row) {

			if ($this->parent->piVars['sra_callnumber'] == $cnid) {
				$markerArray['###SELECTED###'] = "selected=\"selected\"";				
			} else {
				$markerArray['###SELECTED###'] = "";
			}

			$markerArray['###CN_ID###'] = $cnid;
			$markerArray['###CN_NAME###'] = $row['name'];
			$liste .= $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,array(),array()); 
		}

		return $liste;
	}

	/**
	 * Füllt die Status Auswahlbox
	 */
	function makeStatusSelect($subpart) {
		$markerArray = array();
		$liste='';
				
		// Status "Vorhanden"
		if (isset($this->parent->piVars['sra_status']) && $this->parent->piVars['sra_status'] == 0) {
			$markerArray['###SELECTED###'] = "selected=\"selected\"";				
		} else {
			$markerArray['###SELECTED###'] = "";
		}

		$markerArray['###STATUS_VALUE###'] = 0;
		$markerArray['###STATUS_NAME###'] = "vorhanden";
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,array(),array()); 

		// Status "verliehen"
		if ($this->parent->piVars['sra_status'] == 1) {
			$markerArray['###SELECTED###'] = "selected=\"selected\"";				
		} else {
			$markerArray['###SELECTED###'] = "";
		}

		$markerArray['###STATUS_VALUE###'] = 1;
		$markerArray['###STATUS_NAME###'] = "verliehen";
		$liste .= $this->parent->cObj->substituteMarkerArrayCached($subpart,$markerArray,array(),array()); 

		return $liste;
	}



}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_searchform.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_searchform.php']);
}

?>
