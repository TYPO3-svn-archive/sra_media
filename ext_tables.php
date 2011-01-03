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
 * $Id: ext_tables.php,v 1.6 2006/11/08 11:47:11 m1017 Exp $
 *
 * @author 2003-2006	Norman Seibert			 <seibert@eumedia.de>
 * @author 2006 		TAB GmbH (Markus Krause) <typo3@tab-gmbh.de>
 */


if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');

t3lib_extMgm::allowTableOnStandardPages('tx_sramedia_books');
t3lib_extMgm::addToInsertRecords('tx_sramedia_books');

$TCA['tx_sramedia_books'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'type' => 'bibtex_type',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate DESC',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sramedia_books.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, bibtex_type, type, isbn, invnr, signature, title, subtitle, author, publisher, callnumber, pubdate, edition, versionno, versiondate, context, area, keywords, comment, link, conferredto, status',
	)
);


$TCA['tx_sramedia_context'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_context',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sramedia_context.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	)
);

$TCA['tx_sramedia_callnumber'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_callnumber',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sramedia_callnumber.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	)
);

$TCA['tx_sramedia_status'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_status',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sramedia_status.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	)
);

$TCA['tx_sramedia_areas'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_areas',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => Array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_sramedia_areas.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name, signature, sigautoinc, invautoinc',
	)
);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';                 // new!
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:sra_media/flexform_ds_pi1.xml');            // new!
//include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_sramedia_addFieldsToFlexForm.php'); // new!
include_once(t3lib_extMgm::extPath($_EXTKEY).'eval/class.tx_sramedia_bibtex_type_condrequired.php'); // new!

// initialize static extension templates
t3lib_extMgm::addStaticFile( $_EXTKEY, 'static/', 'SRA Bibliothek CSS und JS' );

t3lib_extMgm::addPlugin(Array('LLL:EXT:sra_media/locallang_db.xml:tt_content.list_type', $_EXTKEY.'_pi1'),'list_type');
?>
