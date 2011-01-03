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
 * $Id: tca.php,v 1.6 2006/11/08 11:47:11 m1017 Exp $
 *
 * @author 2003-2006	Norman Seibert			 <seibert@eumedia.de>
 * @author 2006 		TAB GmbH (Markus Krause) <typo3@tab-gmbh.de>
 */
 
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA['tx_sramedia_books'] = Array (
	'ctrl' => $TCA['tx_sramedia_books']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,bibtex_type,type,title,invnr,signature,subtitle,author,sraperson,publisher,callnumber,conferredto,conferredend'
	),
	'feInterface' => $TCA['tx_sramedia_books']['feInterface'],
	'columns' => Array (
		'hidden' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'bibtex_type' => Array (
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type',		
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.0',"article"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.1',"book"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.2',"booklet"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.3',"conference"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.4',"inbook"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.5',"incollection"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.6',"inproceedings"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.7',"manual"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.8',"masterthesis"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.9',"misc"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.10',"phdthesis"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.11',"proceedings"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.12',"techreport"),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_type.13',"unpublished"),
				),
				'default' => "book",
			)
		),
		'bibtex_key' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.bibtex_key',		
			'config' => Array (
				'type' => 'input',	
				'size' => '10',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'type' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.type',		
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_types.type.I.0', 0),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_types.type.I.1', 1),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_types.type.I.2', 2),
					Array('LLL:EXT:sra_media/locallang_db.php:tx_sramedia_types.type.I.3', 3),
				),
				'default' => 0
			)
		),	
		'signature' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.signature',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'title' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.title',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim'
			)
		),
		'subtitle' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.subtitle',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',
				'eval' => 'trim',
			)
		),
		'invnr' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.invnr',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '100',
				'eval' => 'trim'
			)
		),
		'author' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.author',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim'
			)
		),
		"sraperson" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.sraperson",		
			"config" => Array (
				"type" => "select",	
				"foreign_table" => "tx_tkmitarbeiter_personen",	
				"foreign_table_where" => "AND tx_tkmitarbeiter_personen.deleted=0 ORDER BY tx_tkmitarbeiter_personen.name",	
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 25,	
				"MM" => "tx_sramedia_books_person_mm",
			)
		),		
		'publisher' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.publisher',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'callnumber' => Array (	
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.callnumber',		
			'config' => Array (
				'type' => 'select',	
				'items' => Array (
                    Array('',0),
                ),
				'foreign_table' => 'tx_sramedia_callnumber',	
				'foreign_table_where' => 'AND tx_sramedia_callnumber.pid=###CURRENT_PID### ORDER BY tx_sramedia_callnumber.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'pubdate' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.pubdate',		
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
			)
		),
		'pubyear' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.pubyear',		
			'config' => Array (
				'type' => 'input',
				'size' => '4',
				'max' => '4',
				'eval' => 'int',
			)
		),
		'abo' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.abo',		
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'trim',
			)
		),
		'edition' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.edition',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'range' => Array ('lower'=>0,'upper'=>100),	
				'eval' => 'int',
			)
		),
		'area' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.area',		
			'config' => Array (
				'type' => 'select',	
				'items' => Array (
                    Array('',0),
                ),
				'foreign_table' => 'tx_sramedia_areas',	
				'foreign_table_where' => 'AND tx_sramedia_areas.pid=###CURRENT_PID### ORDER BY tx_sramedia_areas.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'keywords' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.keywords',		
			'config' => Array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'comment' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.comment',		
			'config' => Array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'link' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.link',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'conferredto' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.conferredto',		
			'config' => Array (
				'type' => 'select',	
				'items' => Array (
                    Array('',0),
                ),
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.name',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'conferredbegin' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.conferredbegin',		
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
			)
		),
		'conferredend' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.conferredend',		
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
			)
		),
		'reminded' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.reminded',		
			'config' => Array (
				'type' => 'check',
			)
		),
		'reservedby' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.reservedby',		
			'config' => Array (
				'type' => 'select',	
				'items' => Array (
                    Array('',0),
                ),
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.name',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'haslist' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.distributionlist',		
			'config' => Array (
				'type' => 'check',
			)
		),
		'distributionlist' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.distributionlist',		
			'config' => Array (
                'type' => 'group',    
                'internal_type' => 'db',    
                'allowed' => 'fe_users',    
                'size' => 10,    
                'minitems' => 0,
                'maxitems' => 100,
            )
		),
		'abo' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.abo',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'circulating_since' => Array (		
			'exclude' => 0,3,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.circulating_since',		
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
			)
		),
		'download' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.download',        
            'config' => Array (
                'type' => 'group',
                'internal_type' => 'file',
                'allowed' => 'pdf',    
                'disallowed' => 'php,php3',    
                'max_size' => 25000,    
                'uploadfolder' => 'uploads/tx_sramedia',
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
		),



		'address' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.address',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'booktitle' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.booktitle',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'chapter' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.chapter',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'editor' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.editor',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'howpublished' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.howpublished',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'institution' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.institution',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'journal' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.journal',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'month' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.month',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'note' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.note',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'number' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.number',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'organization' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.organization',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'pages' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.pages',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'school' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.school',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'series' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.series',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
		'volume' => array(
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_books.volume',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'max'  => '150',
				'eval' => 'trim'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type, bibtex_key,type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,journal,month,note,number,pages,volume'),
		'1' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,editor,month,note,number,series,volume'),
		'2' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,howpublished,month,note'),
		'3' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,booktitle,editor,month,note,number,organization,pages,series,volume'),
		'4' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type, type;;2-1, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,
		address,booktitle,chapter,editor,month,note,number,pages,series,volume'),
		'5' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,booktitle,chapter,bibtex_key,month,note,number,pages,series,volume'),
		'6' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,booktitle,editor,month,note,number,organization,pages,series,volume'),
		'7' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,month,note,organization'),
		'8' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,month,note,number,school'),
		'9' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,howpublished,month,note'),
		'10' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,month,note,organization,school'),
		'11' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,editor,month,note,number,organization,series,volume'),
		'12' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,address,institution,month,note,number'),
		'13' => Array('showitem' => 'hidden;;1;;1-1-1, bibtex_type,bibtex_key, type;;2, signature, title, invnr, subtitle, author, publisher, callnumber;;;;2-2-2, pubyear;;;;3-3-3, edition, area, keywords, comment, download;;;;4-4-4, link, conferredto, conferredbegin, conferredend, reminded, reservedby,month,note'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => ''),
		'2' => Array('showitem' => 'sraperson')
	)
);


$TCA['tx_sramedia_callnumber'] = Array (
	'ctrl' => $TCA['tx_sramedia_callnumber']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => $TCA['tx_sramedia_callnumber']['feInterface'],
	'columns' => Array (
		'hidden' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'name' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_callnumber.name',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);


$TCA['tx_sramedia_areas'] = Array (
	'ctrl' => $TCA['tx_sramedia_areas']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,name'
	),
	'feInterface' => $TCA['tx_sramedia_areas']['feInterface'],
	'columns' => Array (
		'hidden' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'name' => Array (
				'exclude' => 0,		
				'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_areas.name',		
				'config' => Array (
					'type' => 'input',	
					'size' => '30',	
					'eval' => 'required,trim',
				)
			),
		'signature' => Array (
				'exclude' => 0,		
				'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_areas.signature',		
				'config' => Array (
					'type' => 'input',	
					'size' => '30',	
					'eval' => 'required,trim',
				)
		),
		'sigautoinc' => Array (
				'exclude' => 0,		
				'label' => 'LLL:EXT:sra_media/locallang_db.php:tx_sramedia_areas.sigautoinc',		
				'config' => Array (
					'type' => 'input',	
					'size' => '30',	
					'eval' => 'int,required,trim',
				)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name, signature, sigautoinc')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
?>
