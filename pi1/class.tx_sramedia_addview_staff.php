<?php
/**
	Implementation des Eingabe Formular
  */
class tx_sramedia_addview_staff implements tx_sramedia_view {
	var $parent;
	var $nextinv;
	var $htmlresult;

	function setParentObj(&$obj) {
		$this->parent = $obj;
	}

	function getOutput() {
		$this->parent->piVars = $this->process_data();
		$markerArray = $this->getItemMarkerArray($this->parent->piVars);
		$this->getHeaders($markerArray);
		$t=array();
		$t['total'] = $this->parent->cObj->getSubpart($this->parent->template,'###TEMPLATE_STAFF_ADDMEDIUM###');
		$t['item'][] = $GLOBALS['TSFE']->cObj->getSubpart($t['total'], '###DOCCALLNUMBER###');
		$subpartArray['###CALLNUMBEROPTIONS###'] = $this->setoptions($this->parent->callnumber, $t, "callnumber");
		
		$t['item'][] = $GLOBALS['TSFE']->cObj->getSubpart($t['total'], '###DOCTYPE###');
		$subpartArray['###TYPEOPTIONS###'] = $this->setoptions($this->parent->type, $t, "type");
			
		$t['item'][] = $GLOBALS['TSFE']->cObj->getSubpart($t['total'], '###DOCAREA###');							
		$subpartArray['###AREAOPTIONS###'] = $this->setoptions($this->parent->area, $t, "area");

		$t['item'][] = $GLOBALS['TSFE']->cObj->getSubpart($t['total'], '###DOCPERSON###');
		$subpartArray['###PERSONOPTIONS###'] = $this->setoptions($this->parent->person, $t, "person");
		$markerArray['###OPTION_NEXTINV_SHORT###'] = $this->getNextInv();

		$t['item'][] = $GLOBALS['TSFE']->cObj->getSubpart($t['total'], '###DOCBIBTEX###');
		$subpartArray['###BIBTEXOPTIONS###'] = $this->setoptions($this->parent->bibtex_entry_types, $t, "bibtex");



		if (strlen($markerArray['###BOOK_INVNR###']) < 1) {
			$markerArray['###BOOK_INVNR###'] = $markerArray['###OPTION_NEXTINV_SHORT###'];
		}

		//echo var_dump($t);

		//echo var_dump($this->area); << Alle Areas drinnen
		//echo var_dump($subpartArray['###AREAOPTIONS###']); << !Nicht mehr alle Areas drinnen!
		// Hier handelt es sich um einen Bug in der TAB Media Extension, Template Prozessing
		//echo var_dump($subpartArray);

		$hiddenrow = $this->parent->cObj->getSubpart($t['total'], '###HIDDEN_BOOKS_LIST###');
		$liste = '';

		foreach ($this->parent->area as $areaid => $value) {
			$hiddenMarkerArray = array();
			$hiddenMarkerArray['###AREAID###'] = $areaid;
			$hiddenMarkerArray['###CLASS_AREA###'] = "hidden";
			
			if ($this->parent->piVars['area'] == $areaid) {
				$hiddenMarkerArray['###CLASS_AREA###'] = "visible";					
			}
			
			$hiddenbooksitems = $this->parent->cObj->getSubpart($hiddenrow, '###HIDDEN_BOOKS_ITEMS');
			$bookslist = '';

			$roweven = false;
			foreach ($this->parent->books as $bookid => $bookvalue) {
				if ($bookvalue['area'] == $areaid) {
					$hiddenbooksitemsArray = array();
					$hiddenbooksitemsArray['###CRDATE###'] = date("d.m.Y", $bookvalue['crdate']);
					$hiddenbooksitemsArray['###SIGNATURE###'] = $bookvalue['signature'];
					$hiddenbooksitemsArray['###AREA###'] = $value['name'];
					$hiddenbooksitemsArray['###TITLE###'] = $bookvalue['title'];
					$roweven ? $hiddenbooksitemsArray['###ROWCLASS###'] = "even" : $hiddenbooksitemsArray['###ROWCLASS###'] = "odd";
					$bookslist .= $this->parent->cObj->substituteMarkerArrayCached($hiddenbooksitems,$hiddenbooksitemsArray,array(), $wrappedSubpartArray); 
					$roweven = !$roweven;
				}
			}

			$hiddenbookssubpart = array();
			$hiddenbookssubpart['###HIDDEN_BOOKS_ITEMS###'] = $bookslist;

			$liste .= $this->parent->cObj->substituteMarkerArrayCached($hiddenrow,$hiddenMarkerArray,$hiddenbookssubpart, $wrappedSubpartArray); 
		}

		$subpartArray['###HIDDEN_BOOKS_LIST###'] = $liste;
		$content.= $this->parent->cObj->substituteMarkerArrayCached($t['total'],$markerArray,$subpartArray,$wrappedSubpartArray);

		$content=$this->parent->cObj->substituteMarker($content, '###SEARCH_BUTTON###', $this->parent->pi_getLL('pi_search_button','[Search]'));
		$content=$this->parent->cObj->substituteMarker($content, '###SAVE_BUTTON###', $this->parent->pi_getLL('pi_save_button','[Send]'));
		return $content;
	}

	/**
	 * Diese Funktion zählt die Einträge aus dem aktuellen Jahr. Dabei wird geschaut,
	 * ob das aktuelle Jahr mit dem Jahr des crdate übereinstimmt.
	 * crdate bietet sich zur Inventarisierung an, da
	 * crdate das Datum ist, zu welchem das Buch in die DB eingetragen wurde.
	 */
	function getNextInv() {
		$count = 0;
		$now = date("Y");
		foreach ($this->parent->books as $id => $value) {
			$date = $value['crdate'];
			$year = date("Y", $date);
			if ($year == $now) {
				$count++;
			}
		}
		return ($count+1)."-".$now;
	}

	// set the values id and name of options of the form element select
	function setoptions($array, $t, $code)
	{						
		$itemsOut='';				

		// Da für die Personenwahl ein onChange-Listener verwendet wird, muss
		// ein leerer Platzhalter eingefügt werden
		if ($code == "person") {
			$markArray['###PERSON_UID###']="-1";
			$markArray['###PERSON_NAME###']="- Bitte ausw&auml;hlen: -";
			$itemsOut.= $this->parent->cObj->substituteMarkerArrayCached($t['item'][(count($t['item'])-1)],$markArray,array(),array());
		}

		foreach($array as $key => $value)
		{
			$wrappedSubpartArray=array();
			$markArray['###OPTION_ID###'] = $key;
			if ($code == "area") {
				$markArray['###OPTION_NAME###'] = $value["name"];
				$markArray['###OPTION_SHORTNAME###'] = $value["signature"];
				$markArray['###OPTION_SELECTED###']="";

				if ($this->parent->piVars['area'] == $key) {
					$markArray['###OPTION_SELECTED###']="selected='selected'";
				}

			} else if ($code=="callnumber") {
				$markArray['###OPTION_NAME###'] = $value['name'];
				$markArray['###OPTION_SELECTED###'] = "";
				
				if ($key == $this->parent->piVars['callnumber']) {
					$markArray['###OPTION_SELECTED###'] = "selected='selected'";
				}

			} else if ($code=="type") {
				$markArray['###OPTION_NAME###'] = $value;
				$markArray['###OPTION_SELECTED###'] = "";
				
				if ($key == $this->parent->piVars['sra_type']) {
					$markArray['###OPTION_SELECTED###'] = "selected='selected'";
				}
			} else if ($code=="person") {
				$markArray['###PERSON_UID###'] = $value['uid'];
				$markArray['###PERSON_NAME###'] = $value['name']." ".$value['vorname'];
				$markArray['###OPTION_SELECTED###'] = "";

				if ($key == $this->parent->piVars['sra_person']) {
					$markArray['###OPTION_SELECTED###'] = "selected='selected'";
				}
			} else if ($code=="bibtex") {
				$markArray['###OPTION_VALUE###'] = $key;
				$markArray['###OPTION_TITLE###'] = $key;

				if ($key == $this->parent->piVars['bibtex_type']) {
					$markArray['###OPTION_SELECTED###'] = "selected='selected'";
				} else {
					$markArray['###OPTION_SELECTED###'] = "";
				}

			} else {
				$markArray['###OPTION_NAME###'] = $value;
			}

			$itemsOut.= $this->parent->cObj->substituteMarkerArrayCached($t['item'][(count($t['item'])-1)],$markArray,array(),$wrappedSubpartArray);
			}
		return $itemsOut;	
	}	


	/**
	 * Fills in the markerArray with data for a product
	 */
	function getItemMarkerArray ($row)
	{
		$markerArray=array();
		
		$this->getHeaders($markerArray);

		// Add Formular
		// -------------------------------------
		// SRA Publikation?
		// Versteckte HTML Elemente per Stylesheet sichtbar machen
		$markerArray['###PERSON_ROW_CLASS###'] = "visible";
		$markerArray['###PERSON_RESULTLIST_CLASS###'] = "visible";
		$markerArray['###PERSON_CONTAINER_CLASS###'] = "visible";
		$markerArray['###PERSON_INFO_CLASS###'] = "visible";
		$markerArray['###PERSON_ITEMS###'] = "";

		foreach ($row['person'] as $key => $value) {
			$markerArray['###PERSON_ITEMS###'] .= "<li id=\"person$key\">{$this->parent->person[$key]["name"]}{$this->parent->person[$key]["vorname"]}($key)<input type=\"hidden\" value=\"$key\" name=\"tx_sramedia_pi1[person][$key]\"> <a href=\"javascript:removePerson($key)\">(entfernen)</a></li>\n";
		}
			
		$markerArray['###BACK_LINK###'] = $this->parent->pi_getLL('back_to_list');
		$markerArray['###EMPTY###'] = $this->parent->pi_getLL('empty_list');

		$markerArray['###BOOK_CONFIRMACTION###'] = $this->book_confirmaction;

		// Title
		$markerArray['###BOOK_UID###'] = htmlentities($row['uid'], ENT_QUOTES);
		$markerArray['###BOOK_NO###'] = $this->parent->cObj->stdWrap($this->callnumber[$row['callnumber']]["name"],'');
		$markerArray['###BOOK_TITLE###'] = htmlentities($this->parent->cObj->stdWrap($row['title'],$lConf['title_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_ISBN###'] = htmlentities($this->parent->cObj->stdWrap($row['isbn'],$lConf['isbn_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_SUBTITLE###'] = htmlentities($this->parent->cObj->stdWrap($row['subtitle'],$lConf['subtitle_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_INVNR###'] = htmlentities($this->parent->cObj->stdWrap($row['invnr'],$lConf['invnr_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_AUTHOR###'] = htmlentities($this->parent->cObj->stdWrap($row['author'],$lConf['author_stdWrap.']), ENT_QUOTES);

		$markerArray['###BOOK_LANGUAGE###'] = htmlentities($this->parent->cObj->stdWrap($row['language'],$lConf['language_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_PUBLISHER###'] = htmlentities($this->parent->cObj->stdWrap($row['publisher'],$lConf['publisher_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_EDITION###'] = htmlentities($this->parent->cObj->stdWrap($row['edition'],$lConf['edition_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_PUBYEAR###'] = htmlentities($this->parent->cObj->stdWrap($row['pubyear'],$lConf['pubyear_stdWrap.']), ENT_QUOTES);
		$markerArray['###BOOK_PUBDATE###'] = $this->parent->cObj->stdWrap($row['pubdate'],$lConf['pubdate_stdWrap.']);
		$markerArray['###BOOK_ABO###'] = $this->parent->cObj->stdWrap($row['abo'],$lConf['abo_stdWrap.']);
		$markerArray['###BOOK_COMMENT###'] = $this->parent->cObj->stdWrap($row['comment']);
		$markerArray['###BOOK_SIGNATURE###'] = $this->parent->cObj->stdWrap($row['signature'],$lConf['signature_stdWrap.']);
		$markerArray['###BOOK_LINK###'] = htmlentities($this->parent->cObj->stdWrap($row['link'],$lConf['link_stdWrap.']), ENT_QUOTES);
	
		// Category fields:
		$markerArray['###BOOK_TYPE###'] = $this->parent->cObj->stdWrap($this->parent->type[$row['type']],$lConf['type_stdWrap.']);
		$markerArray['###BOOK_AREA###'] = $this->parent->cObj->stdWrap($this->parent->area[$row['area']]["name"],$lConf['area_stdWrap.']);
		
		$markerArray['###BOOK_SAVEMESSAGE###'] = $this->parent->cObj->stdWrap($row['book_savemessage'], $lConf['book_savemessage_stdWrap.']);
		
		$markerArray['###BOOK_STATUS###'] = '';
		
		return $markerArray;
	}

	function getHeaders(&$markerArr) 
	{
		$markerArr['###HEADER_BORROW_TO###'] = $this->getFieldHeader('borrow_to');
		$markerArr['###HEADER_BIBTEX_TYPE###'] = $this->getFieldHeader('bibtex_type');
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
				return $this->parent->pi_getLL('listFieldHeader_'.$fN,'['.$fN.']');
			break;
		}
	}



	
	//	process_data prepares the load of amazones data and save them
	function process_data()
	{
		$base_url = 'http://www.amazon.de/exec/obidos/ASIN/';
		$description_url = 'http://www.amazon.de/gp/product/product-description/';

			
		if(!empty($this->parent->piVars['search']) AND (empty($this->parent->piVars['isbn'])))
		{	
			$this->parent->piVars['book_savemessage'] = $this->parent->pi_getLL('missing_isbn', '');
		}
		
		if(!empty($this->parent->piVars['isbn']))
		{			
			$this->get_from_www($base_url, $this->parent->piVars['isbn']);
			
			$this->parse_htmlresult();	
			
			foreach($this->parent->piVars as $key => $value)
			{
				$this->parent->piVars["$key"] = utf8_decode($value);
			}		
			
			$this->get_from_www($description_url, $this->parent->piVars['isbn']);
			
			// Die Beschreibungs Extraktion muss nochmal nachbearbeitet werden.
			// Es kommt vor, dass HTML-Code in das Beschreibungsfeld mit
			// reingeschrieben wird, und dass existierende Beschreibungen 
			// mit dieser Methode nicht gefunden werden.
			//$this->extract_description();
			//
			//kim, 13.5.2009
				
			$lConf = $this->conf['displaySingle'.'.'];
		}
		elseif(!empty($this->parent->piVars['title']) OR !empty($this->parent->piVars['author']) OR !empty($this->parent->piVars['signature']) OR !empty($this->parent->piVars['area']))
		{
			$save = $this->parent->piVars;
			$this->parent->piVars['book_savemessage'] .= empty($save['title']) ? "<div class='book-errormessage'>".$this->parent->pi_getLL('missing_title', '') . "</div>" : '';
			$this->parent->piVars['book_savemessage'] .= empty($save['author']) ? "<div class='book-errormessage'>".$this->parent->pi_getLL('missing_author', '') . "</div>"  : '';

			// WENN SRA-Publikationen ausgewählt wurden
				//DANN muss mindestens eine Referenz zu einer SRA-Person existieren
				if (count($this->parent->piVars['person']) == 0) {
					// SONST Fehlermeldung
					$this->parent->piVars['book_savemessage'] .= "<div class='book-errormessage'>".$this->parent->pi_getLL('missing_sraperson', '')."</div>";
				}				
			
			if(empty($this->parent->piVars['book_savemessage']))
			{
				foreach($save as $key => $value)
				{
					$save[$key] = html_entity_decode($value);
				}

				// FileUpload
				$save_filename = "";
				if (strlen($_FILES['tx_sramedia_pi1']['name']['download']) > 2) {
					$str_ziel = PATH_site."uploads/tx_sramedia/";
					$save_filename = $this->getSaveFileName($_FILES['tx_sramedia_pi1']['name']['download']);
			        $str_ziel .= $save_filename;
					move_uploaded_file($_FILES['tx_sramedia_pi1']['tmp_name']['download'], $str_ziel) or die("Error");
					//Um einen Dateizugriff per Web zu ermoeglichen:
					chmod($str_ziel, 0644);
				}
					
				$time = time();

				/* DB */
				$storage_pid;
				if (stristr(",", $this->parent->lConf['pages'])) {
					$pidlist = explode(",", $this->parent->lConf['pages']);
					$storage_pid = $pidlist[0];
				} else {
					$storage_pid = $this->parent->lConf['pages'];
				}

				$fields_values = array(
					'pid' => $storage_pid,
					'tstamp' => $time,					
					'crdate' => $time,
					'cruser_id' => $GLOBALS['TSFE']->fe_user->user['uid'],
					'type' => 3,
					'title' => $save['title'],					
					'subtitle' => $save['subtitle'],				
					'author' => $save['author'],				
					'publisher' => $save['publisher'],				
					'callnumber' => $save['callnumber'],				
					'pubyear' => $save['pubyear'],				
					'edition' => $save['edition'],				
					'bibtex_type' => $save['bibtex_type'],
					'comment' => $save['comment'],								
					'download'=> mysql_escape_string($save_filename),
					'link' => $save['link']);	
										
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_sramedia_books',$fields_values,$no_quote_fields=FALSE);

				if($error = $GLOBALS['TYPO3_DB']->sql_error()) {
					$this->show_sql_error();
				} else {
					$this->parent->piVars['book_savemessage'] = "<div class='book-savemessage'>";
					$this->parent->piVars['book_savemessage'] .= "<h2 class='book-savemessage'>".$this->parent->pi_getLL('book_savesucceded', '')."</h2>";
					$this->parent->piVars['book_savemessage'] .= "</div>";

					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							"uid",
							"tx_sramedia_books",
							"tstamp=$time");
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

					// FALLS SRA-Publikation gewählt wurde
					$count = 0;
					foreach ($this->parent->piVars['person'] as $key => $value) {
						$count++;
						$insertArray = array(
							"uid_local" => $row['uid'],
							"uid_foreign" => $key
							);
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_sramedia_books_person_mm', $insertArray);
					}

					// Der Header sorgt dafür, man zu der Detailseite weitergeleitet wird.
					// Der neu eingefügte Bucheintrag wird angezeigt.
					$singlePid = $this->parent->lConf['single_pid'];
					
					if (!intval($singlePid) > 0) {
						header("Location: /index.php?id=".$this->parent->uid);
					} 

					header("Location: /index.php?id=".$singlePid."&tx_sramedia_pi1[showUid]=".$row['uid']);
				}
					
			} 
		}	
		return $this->parent->piVars;		
	}

	/** this function prevents overwriting existing Files */
	function getSaveFileName($str_ziel)
	{
		if (file_exists(PATH_site."uploads/tx_sramedia/".$str_ziel)) {
			return $this->getSaveFileName("0".$str_ziel);
		} else {
			return $str_ziel;
		}	
	}

	//load content/book-informations from amazon
	function get_from_www($base_url, $isbn)	
	{
		$htmlfile = '';
		$isbn=str_replace('-','',$isbn);
		
		// ISBN13 nach ISBN10 umwandeln,
		// da Amazon's ASIN Webschnittstelle nur ISBN10 akzeptiert
		if(preg_match('/[\d\w]{13}/',$isbn) && strlen($isbn)==13) {
			$isbn = $this->convert_isbn13_to_isbn10($isbn);
		}

		// ISBN gültig?
		if(preg_match('/[\d\w]{10}/',$isbn) && strlen($isbn)==10)
		{
			$base_url=$base_url.$isbn.'/';
			$this->htmlresult = @file_get_contents($base_url);
			$this->parent->piVars['isbn'] = $isbn;
			$this->parent->piVars['url'] = $base_url;
		}
		else 
		{	
			$this->parent->piVars['isbn'] = $isbn;
			$this->parent->piVars['book_savemessage'] =$this->parent->pi_getLL('wrong_isbn', '');
			return;
		}
	}	 

	function convert_isbn13_to_isbn10($isbn13) {
	   $isbn10 = '';
	   $isbn13 = str_replace("-","",$isbn13) ;
	   if((substr($isbn13,0,3)=='978')&&(strlen($isbn13)==13)) { 
	      $isbn10 = substr($isbn13,3,9);
	      settype($isbn10,"string");
	      $q = 0;
	      $m = 1;
	      for($i=0; $i<9; $i++) {
		 $q = $q + ($isbn10[$i] * $m);
		 $m++;
	      }
	      $q = $q % 11 ;
	      if($q==10) { $q = 'X'; }
	      $isbn10 = $isbn10 . $q;
	   }
	   return $isbn10;
	}

	
	// executes the methods to extract the important book informations
	function parse_htmlresult()
	{
		if(!empty($this->htmlresult))
		{
			$this->extract_title_author();
			$this->extract_language();
			$this->extract_publishinginformations();		
		}
		else	
		{
			$this->parent->piVars['book_savemessage'] =$this->parent->pi_getLL('nofund', '');
			return;
		}
	}
	
	//	extract the title and author from htmlresult
	function extract_title_author()
	{
		//$tag = '/<span id=\"btAsinTitle\">.*<\/span>/';
		$tag = '/<span.*btAsinTitle.*>(.*)<\/span>/';
		if(preg_match($tag,$this->htmlresult,$matches))
		{
				$this->parent->piVars['title'] = trim(utf8_encode($matches[1]));
		}

		$tag = '/von.*<a.*>(.*)<\/a>.*\(Autor\)/';
		if(preg_match($tag,$this->htmlresult,$matches))
		{
				$this->parent->piVars['author'] = trim(utf8_encode($matches[1]));
		}

		$this->parent->piVars['title'] = isset($this->parent->piVars['title']) ? $this->parent->piVars['title'] : '';
		$this->parent->piVars['author'] = isset($this->parent->piVars['author']) ? $this->parent->piVars['author'] : '';
	}
	
	//	extract the language from htmlresult
	function extract_language()
	{
		$tag = '/<li><b>Sprache:<\/b>([[:print:]]*)<\/li>/';
		if(preg_match($tag,$this->htmlresult,$matches))
		{
			$this->parent->piVars['language']=trim($matches[1]);
		}
		else
		{
			$this->parent->piVars['language'] = '';
		}	
	}
	
	//	extract the publisher, edition und publishingyear from htmlresult
	function extract_publishinginformations()
	{
		$tag = '/<li><b>Verlag:<\/b>([[:print:]][^;\(<]*)(?:; Auflage: ([0-9]*)[[:print:]][^\(]*)?(?:\(([[:print:]]*)\))?<\/li>/';
		if(preg_match($tag,$this->htmlresult,$matches))
		{	
			$this->parent->piVars['publisher'] = trim($matches[1]);
			if (count($matches))
			{
				$this->parent->piVars['edition'] = trim($matches[2]);
				$year = trim($matches[3]);
			}
			else
				$year = trim($matches[2]);
				
			preg_match('/([0-9]{4})/', $year, $matchyear);
			$this->parent->piVars['pubyear'] = $matchyear[0];
		}
		else
		{
			$this->parent->piVars['publisher'] = '';
			$this->parent->piVars['edition'] = '';
			$this->parent->piVars['pubyear'] = '';
		}	
	}
	
	//	extract the description from htmlresult and save it in comment
	function extract_description()
	{
		if(strlen($this->htmlresult) > 10)
		{
			//$tag = '/id=\"productDescription\"*<div class=\"content\">*<\/div>\n<\/div>/';
			$tag = '/[[:print:][:space:]]*productDescription[[:print:][:space:]]*div class=\"content\">[[:print:][:space:]]*<\/div>/';
//			$tag = '/[[:print:][:space:]]productDescription[[:print:][:space:]]*<div class=\"content\">[[:print:][:space:]]*Kurzbeschreibung[[:print:][:space:]]*<\/div>[[:print:][:space:]]*<\/div>/';
			if(preg_match($tag,$this->htmlresult,$matches))
			{
				die();
				$descriptions = explode('<br><br>',$matches[0]);
				echo var_dump($descriptions);die();
				$this->parent->piVars['comment'] = $descriptions[0];
				$this->parent->piVars['comment'] = empty($this->parent->piVars['comment']) ? $this->parent->pi_getLL('book_nodescription') : $this->parent->piVars['comment'];
			}
			else
			{
				$this->parent->piVars['comment'] = $this->parent->pi_getLL('book_nodescription');
			}
		}
		else	
		{
			$this->error.=$this->parent->pi_getLL('not_found',$this->LLkey);
		}		
	}



}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_addview_staff.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_addview_staff.php']);
}

?>
