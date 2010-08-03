<?php
/**
 * Modulo de importacion de dominios para WHMCS
 *
 * Este módulo permite hacer una importación masiva de dominios
 * creando un nuevo pedido para cada uno de ellos.
 * El módulo funciona a través de la API
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package    domain_import.php
 * @author     Eduardo Gonzalez <egrueda@gmail.com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.2
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

$module_name = "domain_import";
$module_path = dirname(__FILE__);
$debug = false;

if($_POST['confirm_import']) {
	
	debug($_POST);
	
	// Includes WHMCS API Class
	include("wapi.class.php");
	$api_url = "http://" . $_SERVER['SERVER_NAME'] . str_replace('admin/addonmodules.php', 'includes/api.php', $_SERVER['SCRIPT_NAME']);
	$api_user = "admin";
	$api_pass = "123456";
	$wapi = new Wapi($api_url, $api_user, $api_pass);
	
	// Set new order
	echo "<ul>\n";
	foreach($_POST['domains'] as $domain) {
		unset($postfields);
		$domain = trim($domain);
		$clientid = trim($_POST['clientid']);
		echo "  <li>Domain: $domain  ::  \n";
		$postfields["action"] = "addorder";
		$postfields["clientid"] = $clientid;
		$postfields["domain"] = $domain;
		$postfields["domaintype"] = "transfer";
		$postfields["regperiod"] = "1";
		$postfields["noinvoice"] = "1";
		$postfields["noemail"] = "1";
		$postfields["paymentmethod"] = "banktransfer";
		$results = $wapi->call($postfields) or die("Error calling API interface");
		
		if ($results["result"]=="success") {
		  $success++;
			$orderid  = $results["orderid"];
			$domainid = $results["domainids"];
			// Print sucess message
		  echo "<span style=\"color:green;\">OK</span>";
		  if(!$_POST['autoaccept'])
		  	echo " (<a href=\"orders.php?action=view&id=$orderid\">order id: $orderid</a>)\n";
		  else {
		  	echo " (<a href=\"clientsdomains.php?userid=$clientid&domainid=$domainid\">domain id: $domainid</a>)\n";
			  // Activate order
			  if($_POST['autoaccept']) {
				  $wapi_activate = new Wapi($api_url, $api_user, $api_pass);
				  $activatefields["action"] = "acceptorder";
					$activatefields["orderid"] = $orderid;
					$results_activate = $wapi_activate->call($activatefields) or die("Error calling API interface");
					debug($results_activate);
				}
			}
		} else {
			// Print error message
		  echo "<span style=\"color:red;\">Error</span>\n";
		  $errors++;
		}
		debug($results);
		echo "</li>\n";
		flush();
	}
	echo "</ul>\n";
	if($success) echo "$success domains ordered succesfuly.<br />\n";
	if($errors) echo "Failed to create $errors orders<br />\n";
	echo "<br />\n";
	echo "<input type=\"button\" value=\"<- Back to module index\" onClick=\"document.location='$_SELF?module=$module_name'\"></a>\n";
	echo "&nbsp;&nbsp;&nbsp;";
	echo "<input type=\"button\" value=\"View pending orders ->\" onClick=\"document.location='orders.php?status=Pending'\"></a>\n";

} elseif($_POST['do_import']) {
	
	debug($_POST);

	// Get client name
	$sql_client = "SELECT companyname FROM tblclients WHERE id='".$_POST['clientid']."'";
	$res_client = mysql_query($sql_client);
	list($companyname) = mysql_fetch_row($res_client);

	// Get domains list
	$domain_list = explode("\r", trim($_POST['domains_list']));
	$num_domains = count($domain_list);
	
	// Show confirmation text
	echo "Importing <strong>$num_domains</strong> domains for client <strong>$companyname</strong><br />\n";
	echo "Automatically accept orders: ";
	echo ($_POST['autoaccept']) ? "<strong>Yes</strong>" : "<strong>No</strong>";
	echo "<ul>\n";
	foreach($domain_list as $domain) {
		$domain = trim($domain);
		$domains[] = trim($domain);
		echo "<li>$domain</li>\n";
	}
	echo "</ul>\n";
	//echo "Do you wish to proceed?<br>\n";
	echo "<form method=\"post\">\n";
	echo "	<input type=\"submit\" name=\"confirm_import\" value=\"Confirm Import\">\n";
	echo "	<input type=\"button\" name=\"cancel_import\" value=\"Cancel\" onClick=\"history.back();\">\n";
	echo "	<input type=\"hidden\" name=\"clientid\" value=\"".$_POST['clientid']."\">\n";
	echo "	<input type=\"hidden\" name=\"autoaccept\" value=\"".$_POST['autoaccept']."\">\n";
	foreach($domains as $domain) {
		echo "	<input type=\"hidden\" name=\"domains[]\" value=\"$domain\">\n";
	}
	echo "</form>\n";

} else {
	// Clients list
	$sql_clients = "SELECT id, companyname, firstname, lastname FROM tblclients ORDER BY companyname";
	$res_clients = mysql_query($sql_clients);
	$num_clients = mysql_num_rows($res_clients);
	if(!$num_clients) {
		echo "No clients found. Can't make an order whitout a client ID<br>\n";
	} else {
		while($row_clients = mysql_fetch_array($res_clients)) {
			$options .= "<option value=\"".$row_clients['id']."\">".$row_clients['companyname']."</option>\n";
		}
		echo "<form method=\"post\">\n";
		echo "Select target client:<br>\n";
		echo "&nbsp;&nbsp;<select name=\"clientid\">\n$options</select>\n";
		echo "<br /><br />\n";
		echo "Insert domains names, one per row<br />\n";
		echo "&nbsp;&nbsp;<textarea name=\"domains_list\" rows=\"10\" cols=\"50\"></textarea>\n";
		echo "<br />\n";
		echo "<input type=\"checkbox\" id=\"autoaccept\" name=\"autoaccept\" value=\"1\" checked>";
		echo "<label for=\"autoaccept\">Automatically accept new orders</label>\n";
		echo "<br />\n";
		echo "<input type=\"submit\" name=\"do_import\" value=\"Do Import\">\n";		
		echo "</form>\n";
	}
	
}

echo "<br /><br />";

function debug($string) {
	global $debug;
	if(!$debug) return;
	echo "<pre>\n";
	if(is_array($string)) print_r($string);
	else echo $string;
}
	
?>
