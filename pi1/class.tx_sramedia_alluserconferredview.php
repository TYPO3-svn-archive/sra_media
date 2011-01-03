<?php
/**
 * Übersicht aller entliehenen Medien
 */
class tx_sramedia_alluserconferredview implements tx_sramedia_view {
	var $parent;

	function setParentObj(&$obj) {
		$this->parent = $obj;
	}

	function getOutput() {
		require_once(t3lib_extMgm::extPath('sra_media').'pi1/class.tx_sramedia_listview.php');
		$listview = t3lib_div::makeInstance('tx_sramedia_listview');
		$listview->setParentObj($this->parent);
		$this->parent->piVars['sra_status'] = 1;
		$this->parent->piVars['submit'] = 1;
		return $listview->getOutput();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_alluserconferredview.php']) {
	include_once($TYPO3_CONF_VARS['TYPO3_MODE']['XCLASS']['ext/sra_media/pi1/class.tx_sramedia_alluserconferredview.php']);
}

?>
