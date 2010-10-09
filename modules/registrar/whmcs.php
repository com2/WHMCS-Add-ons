<?php
/**
 * WHMCS Domain Registrar
 *
 * Este módulo permite hacer registros y traslados de dominios 
 * usando un WHMCS como agente registrador a través del API
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
 * @package    whmcs.php
 * @author     Eduardo Gonzalez <egonzalez@cyberpymes.com>
 * @copyright  2010 CyberPymes
 * @version    1.1
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

function whmcs_getConfigArray() {
	$configarray = array(
	 "APIUser" 	=> array( "Type" => "text", "Size" => "20", "Description" => "Enter API user name", ),
	 "APIKey"		=> array( "Type" => "text", "Size" => "20", "Description" => "Enter API secret key", ),
	 "URL" 			=> array( "Type" => "text", "Size" => "60", "Description" => "Enter your provider URL", ),
	 "UserEmail"		=> array( "Type" => "text", "Size" => "30", "Description" => "Enter your user email here", ),
	 "UserPassword"	=> array( "Type" => "password", "Size" => "30", "Description" => "Enter your password here", ),
	);
	return $configarray;
}

function whmcs_RegisterDomain($params) {
	
	$valid_user = whmcs_checkUser($params);
	if($valid_user['result'] != "success") {
		$values["error"] = $valid_user['error'];
		return $values;
	} else {
		$clientid = $valid_user['id'];
	}

	$username 		= $params["APIUser"];
	$password 		= $params["APIKey"];
	$url 					= $params["URL"];
	$userpassword	= $params["UserPassword"];
	
	$tld 					= $params["tld"];
	$sld 					= $params["sld"];
	$regperiod 		= $params["regperiod"];
	$nameserver1 	= $params["ns1"];
	$nameserver2 	= $params["ns2"];
  $nameserver3 	= $params["ns3"];
  $nameserver4 	= $params["ns4"];

  $dnsmanagement 		= $params["dnsmanagement"];
  $emailforwarding 	= $params["emailforwarding"];
  $idprotection 		= $params["idprotection"];

	$postfields["username"] 				= $username;
	$postfields["password"] 				= md5($password);
	$postfields["action"] 					= "addorder";
	$postfields["clientid"] 				= $clientid;
	$postfields["domain"] 					= "$sld.$tld";
	$postfields["domaintype"] 			= "register";
	$postfields["regperiod"] 				= $regperiod;
	$postfields["nameserver1"] 			= $nameserver1;
	$postfields["nameserver2"] 			= $nameserver2;
	$postfields["dnsmanagement"] 		= $dnsmanagement;
	$postfields["emailforwarding"] 	= $emailforwarding;
	$postfields["idprotection"] 		= $idprotection;
	$postfields["paymentmethod"]		= "paypal";
	$postfields["noemail"]					= "true";

	$results = whmcs_doapicall($url, $postfields);

	if ($results["result"] == "success") {
	  # Result was OK!
	} else {
	  # An error occured
	  echo "The following error occured: ".$results["message"];
	}

	# If error, return the error message in the value below
	$values["error"] = $error;
	
	return $values;
}

function whmcs_TransferDomain($params) {
	
	$valid_user = whmcs_checkUser($params);
	if($valid_user['result'] != "success") {
		$values["error"] = $valid_user['error'];
		return $values;
	} else {
		$clientid = $valid_user['id'];
	}

	$username 		= $params["APIUser"];
	$password 		= $params["APIKey"];
	$url 					= $params["URL"];
	$userpassword	= $params["UserPassword"];

	$tld = $params["tld"];
	$sld = $params["sld"];
	$regperiod = $params["regperiod"];
	$nameserver1 = $params["ns1"];
	$nameserver2 = $params["ns2"];
  $nameserver3 = $params["ns3"];
  $nameserver4 = $params["ns4"];

  $dnsmanagement 		= $params["dnsmanagement"];
  $emailforwarding 	= $params["emailforwarding"];
  $idprotection 		= $params["idprotection"];
  $transfersecret 	= $params["transfersecret"];

	$postfields["username"] 		= $username;
	$postfields["password"] 		= md5($password);
	$postfields["action"] 			= "addorder";
	$postfields["clientid"] 		= $clientid;
	$postfields["domain"] 			= "$sld.$tld";
	$postfields["domaintype"] 	= "transfer";
	$postfields["regperiod"]		= $regperiod;
	$postfields["nameserver1"] 	= $nameserver1;
	$postfields["nameserver2"] 	= $nameserver2;
	$postfields["dnsmanagement"] 		= $dnsmanagement;
	$postfields["emailforwarding"] 	= $emailforwarding;
	$postfields["idprotection"] 		= $idprotection;
	$postfields["eppcode"] 			= $transfersecret;
	$postfields["paymentmethod"]= "paypal";
	$postfields["noemail"]			= "true";

	$results = whmcs_doapicall($url, $postfields);

	if ($results["result"] == "success") {
	  # Result was OK!
	} else {
	  # An error occured
	  echo "The following error occured: ".$results["message"];
	}

	# If error, return the error message in the value below
	$values["error"] = $error;
	
	return $values;
}

function whmcs_checkUser($params) {
	$url					= $params["URL"];
	$userpassword	= $params["UserPassword"];

	$postfields["action"] 	= "getclientsdetails";
	$postfields["username"] = $params["APIUser"];
	$postfields["password"] = md5($params['APIKey']);
	$postfields["email"] 		= $params["UserEmail"];
		
	$results = whmcs_doapicall($url, $postfields);

	if($results['result'] == "success") {
		list($cryptedpassword, $salt) = explode(':', $results['password']);
		if($cryptedpassword == md5($salt.$userpassword)) {
			return $results;
		} else {
			return array("error" => "UserEmail and UserPassword don't match");
		}
	} else {
		return false;
	}
}

function whmcs_doapicall($url, $postfields) {
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($ch, CURLOPT_VERBOSE, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$data = curl_exec($ch);
	curl_close($ch);
	
	$data = explode(";",$data);
	foreach ($data AS $temp) {
	  $temp 	= explode("=",$temp);
	  $key 		= trim($temp[0]); 
	  $value 	= trim($temp[1]);
	  if($key && $value) {
	  	$results[$key] = $value;
	  }
	}	
	return $results;
}

?>