<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2006	Norman Seibert 			(seibert@eumedia.de)
*  (c) 2006 		TAB GmbH (Markus Krause)(typo3@tab-gmbh.de)
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
 * $Id: ext_localconf.php,v 1.5 2006/11/08 11:47:11 m1017 Exp $
 *
 * @author 2003-2006	Norman Seibert			 <seibert@eumedia.de>
 * @author 2006 		TAB GmbH (Markus Krause) <typo3@tab-gmbh.de>
 * @author 2009 	Ki-Hyoun Kim, <kihyoun@googlemail.com>
 */

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_sramedia_books=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','tt_content.CSS_editor.ch.tx_sramedia_pi1 = < plugin.tx_sramedia_pi1.CSS_editor',43);

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_sramedia_pi1.php','_pi1','list_type',1);

t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_sramedia_books = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi1
	tt_content.shortcut.20.0.conf.tx_sramedia_books.CMD = singleView',43);

// eigene Evaluations Klasse für Plichtfelderstellung
//$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_sramedia_bibtex_type_condrequired'] = 'EXT:sramedia/eval/class.tx_sramedia_bibtex_type_condrequired.php'
?>
