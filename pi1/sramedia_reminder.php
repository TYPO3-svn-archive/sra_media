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
 * $Id: sramedia_reminder.php,v 1.3 2006/11/08 11:47:11 m1017 Exp $
 *
 * @author 2003-2006	Norman Seibert			 <seibert@eumedia.de>
 * @author 2006 		TAB GmbH (Markus Krause) <typo3@tab-gmbh.de>
 */

function formatStr($input, $length) 
{
	$text = trim($input);
	$text = substr($text, 0, $length-1);
	while (strlen($text) < $length) 
	{
		$text.=" ";
	}
	return $text;
}


$path_parts = pathinfo($argv[0]);
$dir = $path_parts["dirname"];
$path_parts = pathinfo($dir);
$dir = $path_parts["dirname"];
$path_parts = pathinfo($dir);
$dir = $path_parts["dirname"];
$path_parts = pathinfo($dir);
$dir = $path_parts["dirname"];
$path_parts = pathinfo($dir);
$dir = $path_parts["dirname"];

define("PATH_site", $dir."\\");
define("PATH_typo3", $dir."\\typo3\\");
define("PATH_t3lib", PATH_typo3."t3lib\\");
define("PATH_typo3conf", PATH_site."typo3conf\\");	# Typo-configuraton path
define("TYPO3_MODE","BE");

require(PATH_t3lib."class.t3lib_div.php");
require(PATH_t3lib."class.t3lib_extmgm.php");
require(PATH_t3lib."config_default.php");		
if (!defined ("TYPO3_db")) 	die ("The configuration file was not included.");

# Connect to the database
$result = @mysql_pconnect(TYPO3_db_host, TYPO3_db_username, TYPO3_db_password); 
if (!$result)	
{
	die("Couldn't connect to database at ".TYPO3_db_host);
}

$mail = t3lib_div::makeInstance("t3lib_div");

$reminder_offset = 3; #Offset [days] between conferred_end and reminder
$m_subject = "Mahnung";
$m_header = "Die folgenden Buecher sind faellig:".chr(10).chr(10);
$m_name = "Bibliothek";
$m_from = "lorenz@sra.uni-hannover.de";
$m_cc = "kihyoun@gmail.com";

$query = "SELECT fe_users.uid AS fe_uid, fe_users.name AS fe_name, fe_users.email as email, tx_sramedia_books.* FROM tx_sramedia_books LEFT OUTER JOIN fe_users ON fe_users.uid = tx_sramedia_books.conferredto WHERE reminded = 0 AND conferredend <= ".(time()-$reminder_offset*24*3600).$this->enableFields;
$res = mysql(TYPO3_db,$query);
echo mysql_error();

$headers=array();
$headers[]="From: ".$m_name." <".$m_from.">";
$feuser_uid = 0;

while($row=mysql_fetch_assoc($res)) 
{
	if (($feuser_uid <> $row["fe_uid"]) && ($feuser_uid <> 0)) 
	{
		if ($m_email) $mail->plainMailEncoded($m_email, $m_subject, $m_msg, implode($headers,chr(10)));
		if ($m_cc) $mail->plainMailEncoded($m_cc, $m_subject, $m_msg, implode($headers,chr(10)));
		$m_msg = $m_header;
	}
	$m_email = $row["email"];
	$m_msg.= formatStr($row["callnumber"], 10).formatStr($row["title"], 40).formatStr($row["author"], 20).chr(10);
	$query = "UPDATE tx_sramedia_books SET reminded = 1 WHERE uid = ".$row["uid"];
	mysql_query($query);
	if ($feuser_uid <> $row["fe_uid"]) $feuser_uid = $row["fe_uid"];
}
if ($feuser_uid <> 0) 
{
	$m_msg = $m_header.$m_msg;
	if ($m_email) $mail->plainMailEncoded($m_email, $m_subject, $m_msg, implode($headers,chr(10)));
	if ($m_cc) $mail->plainMailEncoded($m_cc, $m_subject, $m_msg, implode($headers,chr(10)));
	$query = "UPDATE tx_sramedia_books SET reminded = 1 WHERE uid = ".$row["uid"];
	mysql_query($query);
}
?>
