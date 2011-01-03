<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2006	Norman Seibert 			(seibert@eumedia.de)
*  (c) 2006 		TAB GmbH (Markus Krause)(typo3@tab-gmbh.de)
*  (c) 2009             Ki-Hyoun Kim,(kihyoun@googlemail.com)
* 			Department of System- and Computer Architecture, 
*			Leibniz Universit채t Hannover 
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/** 
 * $Id: class.tx_sramedia_pi1.php,v 1.8 2006/11/09 08:48:39 m1017 Exp $
 *
 * @author 2003-2006	Norman Seibert			 <seibert@eumedia.de>
 * @author 2006 		TAB GmbH (Markus Krause) <typo3@tab-gmbh.de>
 * @author 2009     Ki-Hyoun Kim <kihyoun@googlemail.com>
 */

require_once(PATH_tslib."class.tslib_pibase.php");
require_once(t3lib_extMgm::extPath('sra_media').'pi1/interface.tx_sramedia_view.php');

class tx_sramedia_pi1 extends tslib_pibase {
	var $cObj;		// The backReference to the mother cObj object set at call time
	
	var $prefixId = 'tx_sramedia_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_sramedia_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'sra_media';	// The extension key.
	
	var $conf;
	var $config;

	// Storage PID, falls mehrere angegeben wird nur 1. PID genommen
	var $pid;
	// HTML Template
	var $template;
	// Plugin Interface Code
	var $theCode='';

	// DB Tabellen
	var $area=array();			// Themenbereiche
	var $books=array();			// Medien
	var $person=array();		// Personen tx_tkmitarbeiter
	var $feuser=array();		// Personen Front-End Users 
	var $book_person_mm=array();// Medien->Personen Verkn체pfungen
	var $callnumber=array();	// Standorte

	// statische Medien-Typen:
	// 0 = Buch
	// 1 = CD
	// 2 = DVD
	// 3 = SRA Publikation
	var $type=array();

	// BibTex Entry Typen
	var $bibtex_entry_types = array();

	var $searchFieldList = 'title,author,invnr,signature,subtitle,keywords,comment';
	var $nextinv = 0;
	var $show_error = 'on';
	var $search_person = false;
	var $uid;
	
	function main($content,$conf) {		
		$this->conf = $conf;
		$this->uid = $GLOBALS['TSFE']->id;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();		// Loading the LOCAL_LANG values

		/*
		$year = date("Y");
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'lastinv',
				'tx_sramedia_invnr',
				'uid=0'
		);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

		// Inventar Nummer Increment setzen
		$this->nextinv = $row['lastinv']+1;
		 */

		// Storage Page?
		$pidlist = explode(",", trim($this->cObj->stdWrap($this->conf['pid_list'],$this->conf['pid_list.'])));
		// Nur 1 Eintrag von Speicherordnern, dies vereinfach das hinzuf체gen von B체chern
		$this->pid = $pidlist[0];
		
		// template is read.
		$this->template = $this->cObj->fileResource($this->conf['templateFile']);

		// Flexforms initialisieren
		$this->init();

		$myObj;
		// Ansichts Auswahl		
		switch($this->lConf['view_mode']) {

			case 'LISTVIEW': // Listenansicht
				// DB Abfragen
				$this->initBookPersonMM();
				$this->initBooks();
				$this->initType();
				$this->initCallnumber();
				$this->initArea();
				$this->initPerson();
				$this->initBibtex();

				// Erzeugung der Listenansicht-Klasse
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_listview.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_listview');
				$myObj->setParentObj($this);
				break;

			case 'ALLUSERCONFERRED': // Alle Entleihungen
				// DB Abfragen
				$this->initBookPersonMM();
				$this->initBooks();
				$this->initType();
				$this->initCallnumber();
				$this->initArea();
				$this->initPerson();
				$this->initBibtex();

				// Erzeugung der Listenansicht-Klasse
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_alluserconferredview.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_alluserconferredview');
				$myObj->setParentObj($this);
				break;

			case 'SEARCHFORM': // Suchformular
				// DB Abfragen
				$this->initType();
				$this->initPerson();
				$this->initArea();
				$this->initCallnumber();

				// Erzeugung der Suchformular-Klasse
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_searchform.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_searchform');
				$myObj->setParentObj($this);
				break;

			case 'USER_CONFERRED': // Meine ausgeliehenen Medien
				// DB Abfragen
				$this->initBookPersonMM();
				$this->initBooks();
				$this->initType();
				$this->initCallnumber();
				$this->initArea();
				$this->initPerson();
				$this->initBibtex();
				// Erzeugung der Suchformular-Klasse
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_userview.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_userview');
				$myObj->setParentObj($this);
				break;

			case 'ADDFORM': // Buch hinzuf체gen
				// DB Abfragen
				$this->initBooks();
				$this->initType();
				$this->initCallnumber();
				$this->initArea();
				$this->initPerson();

				// Erzeugung der Eingabemaske
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_addview.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_addview');
				$myObj->setParentObj($this);
				break;

			case 'ADDFORMSTAFF': // Publikation hinzuf체gen(f홸 Mitarbeiter)
				// DB Abfragen
				$this->initBooks();
				$this->initType();
				$this->initCallnumber();
				$this->initArea();
				$this->initPerson();
				$this->initBibtex();

				// Erzeugung der Eingabemaske
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_addview_staff.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_addview_staff');
				$myObj->setParentObj($this);
				break;


			case 'SINGLEVIEW': // Buch Detailansicht
				$this->initBibtex();
				$this->initFeUser();
				$this->initArea();
				$this->initCallnumber();

				// Erzeugung der Detailansichts-Klasse
				require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_singleview.php');
				$myObj = t3lib_div::makeInstance('tx_sramedia_singleview');
				$myObj->setParentObj($this);
				break;

			default: 
				break;
		}

		$content = $myObj->getOutput();
		return $this->pi_wrapInBaseClass($content);
	}

	/**
	 * Initialisiert die Bibtex Felder (Pflicht- und Optionale Felder)
	 */
	function initBibtex() {
		
		// @article
		$this->bibtex_entry_types["article"]["required"] = array (
				"author",
				"title",
				"journal",
				"year"
		);

		$this->bibtex_entry_types["article"]["optional"] = array (
				"volume",
				"number",
				"pages",
				"month",
				"note"
		);

		// @book
		$this->bibtex_entry_types["book"]["required"] = array (
				"author or editor",
				"title",
				"publisher",
				"year"
		);

		$this->bibtex_entry_types["book"]["optional"] = array (
				"volume or number",
				"series",
				"address",
				"edition",
				"month",
				"note"
		);

		// @booklet
		$this->bibtex_entry_types["booklet"]["required"] = array (
				"title"
		);

		$this->bibtex_entry_types["booklet"]["optional"] = array (
				"author",
				"howpublished",
				"address",
				"month",
				"year",
				"note"
		);

		// @conference
		$this->bibtex_entry_types["conference"]["required"] = array (
				"author",
				"title",
				"booktitle",
				"year"
		);

		$this->bibtex_entry_types["conference"]["optional"] = array (
				"editor",
				"volume or number",
				"series",
				"pages",
				"address",
				"month",
				"organization",
				"publisher",
				"note"
		);

		// @inbook
		$this->bibtex_entry_types["inbook"]["required"] = array (
				"author or editor",
				"title",
				"chapter and/or pages",
				"publisher",
				"year"
		);

		$this->bibtex_entry_types["inbook"]["optional"] = array (
				"volume or number",
				"series",
				"type",
				"address",
				"edition",
				"month",
				"note"
		);

		// @incollection
		$this->bibtex_entry_types["incollection"]["required"] = array (
				"author",
				"title",
				"booktitle",
				"publisher",
				"year"
		);

		$this->bibtex_entry_types["incollection"]["optional"] = array (
				"editor",
				"volume or number",
				"series",
				"type",
				"chapter",
				"pages",
				"address",
				"edition",
				"month",
				"note"
		);

		// @inproceedings
		$this->bibtex_entry_types["inproceedings"]["required"] = array (
				"author",
				"title",
				"booktitle",
				"year"
		);

		$this->bibtex_entry_types["inproceedings"]["optional"] = array (
				"editor",
				"volume or number",
				"series",
				"pages",
				"address",
				"month",
				"organization",
				"publisher",
				"note"
		);

		// @manual
		$this->bibtex_entry_types["manual"]["required"] = array (
				"title"
		);

		$this->bibtex_entry_types["manual"]["optional"] = array (
				"author",
				"organization",
				"address",
				"edition",
				"month",
				"year",
				"note"
		);

		// @masterthesis
		$this->bibtex_entry_types["mastersthesis"]["required"] = array (
				"author",
				"title",
				"school",
				"year"
		);

		$this->bibtex_entry_types["mastersthesis"]["optional"] = array (
				"type",
				"address",
				"month",
				"note"
		);

		// @misc
		$this->bibtex_entry_types["misc"]["required"] = array (
		);

		$this->bibtex_entry_types["misc"]["optional"] = array (
				"author",
				"title",
				"howpublished",
				"month",
				"year",
				"note"
		);

		// @phdthesis
		$this->bibtex_entry_types["phdthesis"]["required"] = array (
				"author",
				"title",
				"school",
				"year"
		);

		$this->bibtex_entry_types["phdthesis"]["optional"] = array (
				"type",
				"address",
				"month",
				"note"
		);

		// @proceedings
		$this->bibtex_entry_types["proceedings"]["required"] = array (
				"title",
				"year"
		);

		$this->bibtex_entry_types["proceedings"]["optional"] = array (
				"editor",
				"volume or number",
				"series",
				"address",
				"month",
				"organization",
				"publisher",
				"note"
		);

		// @techreport
		$this->bibtex_entry_types["techreport"]["required"] = array (
				"author",
				"title",
				"institution",
				"year"
		);

		$this->bibtex_entry_types["techreport"]["optional"] = array (
				"type",
				"number",
				"address",
				"month",
				"note"
		);

		// @unpublished 
		$this->bibtex_entry_types["unpublished"]["required"] = array (
				"author",
				"title",
				"note"
		);

		$this->bibtex_entry_types["unpublished"]["optional"] = array(
				"month",
				"year"
		);

	}

	/**
	 * Liefert den Feldnamen in der Datenbank zu dem entsprechenden Bibtex Feldnamen
	 *
	 * Der Bibtex Feldname "type" unterscheidet sich von dem in der Datenbank "bibtex_type".
	 * Die Funktion liefert entweder den Namen selbst, oder den umbenannten Namen.
	 */
	function getDBFieldName($value) {
		$fieldname = $value;

		switch ($value) {
		case "type":
			$fieldname = "bibtex_type";
			break;	
		case "year":
			$fieldname = "pubyear";
			break;
		default:
			$fieldname = $value;
			break;
		}

		return $fieldname;
	}

	/**
	 * Flexform Daten initialisieren
	 * initializes the flexform and all config options ;-)
	 */
	function init() {
	    $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
	    $this->lConf = array(); // Setup our storage array...
	    // Assign the flexform data to a local variable for easier access
	    $piFlexForm = $this->cObj->data['pi_flexform'];
	    $index = 0;
	    $sDef = current($piFlexForm['data']);
	    $lDef = array_keys($sDef);
	    
	    foreach ( $piFlexForm['data'] as $sheet => $data ) {
		    if (is_array($data[$lDef[$index]])) {
			    foreach ($data[$lDef[$index]] as $key => $val ) {
				    $this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet,$lDef[$index]);
			    } 
		    }
	    }
	}

	/**
	 * Getting all category into internal array
	 */
	function initType()	
	{
		// 0: Buch
		// 1: CD
		// 2: DVD
		// 3: SRA Veroeffentlichung

		// Fetching type from DB:
	 	$this->type = array();
		$this->type[0] = $this->pi_getLL('tx_sramedia_types.type.I.0');
		$this->type[1] = $this->pi_getLL('tx_sramedia_types.type.I.1');
		$this->type[2] = $this->pi_getLL('tx_sramedia_types.type.I.2');
		$this->type[3] = $this->pi_getLL('tx_sramedia_types.type.I.3');
	}

		
	function initCallnumber() {
		// Fetching callnumber from DB:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"*",
			"tx_sramedia_callnumber",
			"deleted=0 AND hidden=0",
			""
		);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->callnumber[$row['uid']] = $row;
		}	
	}
	
	function initArea()	
	{
		// Fetching area from DB:
		// Sortierung der Themenbereiche nach Alphabet
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"*",
			"tx_sramedia_areas",
			"deleted=0 AND hidden=0",
			"",
			"name ASC"
		);
		$this->area=array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	
		{
			$this->area[$row['uid']] = $row;
		}	
	}

	function initPerson()	
	{
		// Fetching area from DB:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"uid,name,vorname",
			"tx_tkmitarbeiter_personen",
			"deleted=0 AND hidden=0",
			"",
			"name ASC"
		) or die(mysql_error());
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$this->person[$row['uid']] = $row;
		}	
	}

	function initFeUser()	
	{
		// Fetching area from DB:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"uid,name",
			"fe_users",
			"",
			"",
			"name ASC"
		) or die(mysql_error());
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$this->feuser[$row['uid']] = $row;
		}	
	}

	function initBooks() {
		// Fetching books from DB:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"*",
			"tx_sramedia_books",
			"deleted=0 AND hidden=0",
			"",
			"crdate DESC"
		) or die(mysql_error());
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$this->books[$row['uid']] = $row;
		}	

	}
	
	function initBookPersonMM() {
		// Fetching books from DB:
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			"*",
			"tx_sramedia_books_person_mm",
			"") or die(mysql_error());
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
		{
			$this->book_person_mm[$row['uid_local']][] = $row['uid_foreign'];
		}	

	}

	function getHeaders(&$markerArr) 
	{
		$markerArr['###HEADER_BORROW_TO###'] = $this->getFieldHeader('borrow_to');
		$markerArr['###HEADER_NO###'] = $this->getFieldHeader('number');
		$markerArr['###HEADER_TITLE###'] = $this->getFieldHeader('title');
		$markerArr['###HEADER_ISBN###'] = $this->getFieldHeader('isbn');
		$markerArr['###HEADER_SUBTITLE###'] = $this->getFieldHeader('subtitle');
		$markerArr['###HEADER_INVNR###'] = $this->getFieldHeader('invnr');
		$markerArr['###HEADER_TYPE###'] = $this->getFieldHeader('type');
		$markerArr['###HEADER_AUTHOR###'] = $this->getFieldHeader('author');
		$markerArr['###HEADER_SRAPERSON###'] = $this->getFieldHeader('sraperson');
		$markerArr['###HEADER_LANGUAGE###'] = $this->getFieldHeader('language');
		$markerArr['###HEADER_PUBLISHER###'] = $this->getFieldHeader('publisher');
		$markerArr['###HEADER_EDITION###'] = $this->getFieldHeader('edition');
		$markerArr['###HEADER_PUBYEAR###'] = $this->getFieldHeader('pubyear');
		$markerArr['###HEADER_PUBDATE###'] = $this->getFieldHeader('pubdate');
		$markerArr['###HEADER_AREA###'] = $this->getFieldHeader('area');
		$markerArr['###HEADER_ABO###'] = $this->getFieldHeader('abo');
		$markerArr['###HEADER_COMMENT###'] = $this->getFieldHeader('comment');
		$markerArr['###HEADER_SIGNATURE###'] = $this->getFieldHeader('signature');
		$markerArr['###HEADER_DOWNLOAD###'] = $this->getFieldHeader('download');
		$markerArr['###HEADER_LINK###'] = $this->getFieldHeader('link');
		$markerArr['###HEADER_STATUS###'] = $this->getFieldHeader('status');
		$markerArr['###HEADER_LIST###'] = $this->getFieldHeader('list');
		$markerArr['###HEADER_BEGIN###'] = $this->getFieldHeader('begin');
		$markerArr['###HEADER_END###'] = $this->getFieldHeader('end');
		$markerArr['###HEADER_INFO###'] = $this->getFieldHeader('info');
		$markerArr['###HEADER_DOWNLOADPATH###'] = $this->getFieldHeader('downloadpath');
		$markerArr['###HEADER_PERSON###'] = $this->getFieldHeader('person');
		$markerArr['###HEADER_PERSONRESULT###'] = $this->getFieldHeader('personresult');
	}


     /**
      * Returns the header text of a field (from locallang)
      * @see getHeaders
      */
     function getFieldHeader($fN) {
         switch($fN) {
             default:
                 return $this->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
             break;
         }
     }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_pi1.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_pi1.php']);
}

?>
