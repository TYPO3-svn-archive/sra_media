<?php
/**
 * Client Seitige Evaluation der Bibtex-Literaturtypen Auswahlbox
 *
 * Je nach Auswahl werden bestimmte Felder als Pflichtfelder markiert,
 * und andere Felder als optionale Felder.
 */
class tx_sramedia_bibtex_type_condrequired {

	/**
	 * Javascript Code
	 */
	function returnFieldJS() {
		global $SOBE;
		$id= $SOBE->elementsData[0]['uid'];
		return "//ONFOCUSE";
	}

	/**
	 * PHP Evaluation
	 */
	function evaluateFieldValue() {
	}

}

?>
