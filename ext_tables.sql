/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2006	Norman Seibert 			(seibert@eumedia.de)
*  (c) 2006 		TAB GmbH (Markus Krause)(typo3@tab-gmbh.de)
*  (c) 2009			System- und Rechnerarchitektur, Leibniz Universität Hannover
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


#
# Table structure for table 'tx_sramedia_callnumber'
#
CREATE TABLE tx_sramedia_callnumber (
	uid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	pid INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tstamp INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	crdate INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	cruser_id INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	deleted TINYINT(1) DEFAULT 0 NOT NULL,
	hidden TINYINT(1) DEFAULT 0 NOT NULL,
	name CHAR(30) DEFAULT '' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_sramedia_books'
#
CREATE TABLE tx_sramedia_books (
	uid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	pid INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tstamp INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	crdate INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	cruser_id INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	deleted TINYINT(1) DEFAULT 0 NOT NULL,
	hidden TINYINT(1) DEFAULT 0 NOT NULL,
	type INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	title TEXT DEFAULT '' NOT NULL,
	subtitle TEXT DEFAULT '' NOT NULL,
	invnr TEXT DEFAULT '' NOT NULL,
	author TINYTEXT DEFAULT '' NOT NULL,
	publisher CHAR(40) DEFAULT '' NOT NULL,
	callnumber INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	pubdate INT(11) DEFAULT '0' NOT NULL,
	pubyear CHAR(4) DEFAULT '' NOT NULL,
	edition INT(11) NULL,
	area INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	keywords CHAR(150) NOT NULL,
	comment TEXT DEFAULT '' NOT NULL,
	link CHAR(150) DEFAULT '' NOT NULL,
	conferredto INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	reservedby INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	conferredbegin INT(11) DEFAULT NULL,
	conferredend INT(11) DEFAULT NULL,
	signature CHAR(50) DEFAULT '' NOT NULL, 
	distributionlist TEXT NOT NULL,
	haslist TINYINT(1) DEFAULT 0 NOT NULL,
	reminded TINYINT(1) DEFAULT 0 NOT NULL,
	abo CHAR(50) NOT NULL,
	circulating_since INT(11) DEFAULT '0' NOT NULL,
	download BLOB NOT NULL,

	address TINYTEXT NULL,
	booktitle TINYTEXT NULL,
	chapter CHAR(10) NULL,
	editor TINYTEXT NULL,
	howpublished TINYTEXT NULL,
	institution TINYTEXT NULL,
	journal TINYTEXT NULL,
	bibtex_key CHAR(20) NULL,
	month CHAR(15) NULL,
	note TINYTEXT NULL,
	number CHAR(10) NULL,
	organization TINYTEXT NULL,
	pages CHAR(10) NULL,
	school TINYTEXT NULL,
	series CHAR(10) NULL,
	bibtex_type ENUM('article','book','booklet','conference','inbook','incollection','inproceedings','manual','mastersthesis','misc','phdthesis','proceedings','techreport','unpublished') NOT NULL,
	volume CHAR(10) NULL,

	sraperson int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid)
);


# Updated by kim <kihyoun@gmail.com>: 
# signature CHAR(30) DEFAULT '' NOT NULL
#
# Table structure for table 'tx_sramedia_areas'
#
CREATE TABLE tx_sramedia_areas (
	uid INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	pid INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	tstamp INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	crdate INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	cruser_id INT(11) UNSIGNED DEFAULT '0' NOT NULL,
	deleted TINYINT(1) DEFAULT 0 NOT NULL,
	hidden TINYINT(1) DEFAULT 0 NOT NULL,
	name CHAR(30) DEFAULT '' NOT NULL,
	signature CHAR(30) DEFAULT '' NOT NULL, 
	sigautoinc INT(11) UNSIGNED DEFAULT '0' NOT NULL, 
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_sramedia_books_person_mm'
#
CREATE TABLE tx_sramedia_books_person_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);
