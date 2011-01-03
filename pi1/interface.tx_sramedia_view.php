<?php
/**
 * Interface für Grundlegende Methoden zur FE Anzeige
 */
interface tx_sramedia_view {
	
	// Diese Funktion liefert HTML-Code zur Anzeige auf der Webseite
	function getOutput();

	// Diese Methode stellt eine Verbindung zum tx_sramedia_pi1 Objekt her
	function setParentObj(&$obj);
}

?>
