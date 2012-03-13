<?php
/**
 * OVH Registrar Module for WHMCS
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
 * @package    ovh.php
 * @author     Eduardo Gonzalez <egonzalez@cyberpymes.com>
 * @copyright  2010 CyberPymes
 * @version    1.0
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

function ovh_getConfigArray() {
	$configarray = array(
	 "Username" => array( "Type" => "text", "Size" => "20", "Description" => "OVH Nic", ),
	 "Password" => array( "Type" => "password", "Size" => "20", "Description" => "OVH Password", ),
	 "TestMode" => array( "Type" => "yesno", ),
	 "Debug" 		=> array( "Type" => "yesno", ),
	);
	return $configarray;
}

/*
function ovh_GetNameservers($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Get Name Servers Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

	try {
		$result = $soap->domainDnsList($session, $domainName);
		$values["ns1"] = $result[0]->name;
		$values["ns2"] = $result[1]->name;
  	$values["ns3"] = $result[2]->name;
	  $values["ns4"] = $result[3]->name;
	  return $values;
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
	
	# Put your code to get the nameservers here and return the values below
	$values["ns1"] = $nameserver1;
	$values["ns2"] = $nameserver2;
  $values["ns3"] = $nameserver3;
  $values["ns4"] = $nameserver4;
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function ovh_SaveNameservers($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Set Name Servers Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

	try {
		$ns1 = $params["ns1"];	$ip1 = "";
		$ns2 = $params["ns2"];	$ip2 = "";
		$ns3 = $params["ns3"];	$ip3 = "";
		$ns4 = $params["ns4"];	$ip4 = "";
		$ns5 = $params["ns5"];	$ip5 = "";
		$result = $soap->domainDnsUpdate($session, $domainName, $ns1, $ip1, $ns2, $ip2, $ns3, $ip3, $ns4, $ip4, $ns5, $ip5);
		return $values;
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
}
*/
function ovh_GetRegistrarLock($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Get Registrar Lock: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

	try {
		$ns1 = $params["ns1"];	$ip1 = "";
		$ns2 = $params["ns2"];	$ip2 = "";
		$ns3 = $params["ns3"];	$ip3 = "";
		$ns4 = $params["ns4"];	$ip4 = "";
		$ns5 = $params["ns5"];	$ip5 = "";
		$result = $soap->domainLockStatus($session, $domainName);

		return $lockstatus;	// locked, unlocked
		
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
}

function ovh_SaveRegistrarLock($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Set Name Servers Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

	try {
		if ($params["lockenabled"]) {
			$lockstatus="locked";
			$result = $soap->domainUnlock($session, $domainName);
		} else {
			$lockstatus="unlocked";
			$result = $soap->domainLock($session, $domainName);
		}
		return $lockstatus;	// locked, unlocked
		
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
}

/*
function ovh_GetEmailForwarding($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Put your code to get email forwarding here - the result should be an array of prefixes and forward to emails (max 10)
	foreach ($result AS $value) {
		$values[$counter]["prefix"] = $value["prefix"];
		$values[$counter]["forwardto"] = $value["forwardto"];
	}
	return $values;
}
*/

/*
function ovh_SaveEmailForwarding($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	foreach ($params["prefix"] AS $key=>$value) {
		$forwardarray[$key]["prefix"] =  $params["prefix"][$key];
		$forwardarray[$key]["forwardto"] =  $params["forwardto"][$key];
	}
	# Put your code to save email forwarders here
}
*/

/*
function ovh_GetDNS($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    # Put your code here to get the current DNS settings - the result should be an array of hostname, record type, and address
    $hostrecords = array();
    $hostrecords[] = array( "hostname" => "ns1", "type" => "A", "address" => "192.168.0.1", );
    $hostrecords[] = array( "hostname" => "ns2", "type" => "A", "address" => "192.168.0.2", );
	return $hostrecords;

}

function ovh_SaveDNS($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    # Loop through the submitted records
	foreach ($params["dnsrecords"] AS $key=>$values) {
		$hostname = $values["hostname"];
		$type = $values["type"];
		$address = $values["address"];
		# Add your code to update the record here
	}
    # If error, return the error message in the value below
	$values["error"] = $Enom->Values["Err1"];
	return $values;
}
*/

function ovh_RegisterDomain($params) {
	//echo "<pre>\n"; print_r($params); echo "</pre>\n"; die();
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* New Domain Create Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession($params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}
 	
 	# Check domain availability
 	$is_available = ovh_checkDomain($domainName, '0');
 	if(!$is_available->value) {
	 	$values["error"] = $is_available->reason;
	 	return $values;
	}
	
	# Create nic handle for owner contact
	$owner_response = ovh_nicCreateOwner($params);
	if($owner_response['error']) {
		$values["error"] = $owner_response['error'];
		return $values;
	} else {
		$owner = $owner_response['nic'];
		ovh_Debug("Owner contact: $owner");
	}

	# Create nic handle for admin contact
	$admin_response = ovh_nicCreateAdmin($params);
	if($admin_response['error']) {
		$values["error"] = $admin_response['error'];
		return $values;
	} else {
		$admin = $admin_response['nic'];
		ovh_Debug("Admin contact: $admin");
	}

	# Send registration command
	try {
		$regperiod 	= $params["regperiod"];
		$testmode		= ($params["TestMode"]=='on') ? 'true' : 'false';
		$domain 	= $domainName;		//string domain : the domain name
		$hosting 	= "none";					//string hosting : the hosting type (none|start1m|perso|pro|business|premium)
		$offer 		= "gold";					//string offer : the domain offer (gold|platinum|diamond)
		$profile 	= "whiteLabel";		//string profile : the reseller profile (none | whiteLabel | agent)
		$owo 			= "no";						//string owo : activate OwO for .com, .net, .org, .info and .biz (yes | no)
		$owner 		= $owner;					//string owner : the owner nichandle
		$admin 		= $admin;					//string admin : the admin nichandle
		$tech 		= $username;			//string tech : the tech nichandle
		$billing 	= $username;			//string billing : the billing nichandle
		$dns1 		= $params["ns1"];	//string dns1 : the primary dns hostname (if hosting, default OVH dns will be installed)
		$dns2 		= $params["ns2"];	//string dns2 : the secondary dns hostname
		$dns3 		= $params["ns3"];	//string dns3 : the third dns hostname
		$dns4 		= $params["ns4"];	//string dns4 : the fourth dns hostname
		$dns5 		= "";							//string dns5 : the fifth dns hostname
		$dryRun 	= $testmode;			//boolean dryRun : enable the TEST MODE when enabled (true), will not debit your account

		/* ToDo */
		$method 					= "";		//string method : only for .fr (AFNIC) : identification method (siren | inpi | birthPlace | afnicIdent)
		$legalName 				= "";		//string legalName : only for .fr (AFNIC) : corporation name /trademark owner
		$legalNumber 			= "";		//string legalNumber : only for .fr (AFNIC) : SIREN/SIRET/INPI number
		$afnicIdent 			= "";		//string afnicIdent : only for .fr (AFNIC) : afnic ident code
		$birthDate 				= "";		//string birthDate : only for .fr (AFNIC) : owner birth date
		$birthCity 				= "";		//string birthCity : only for .fr (AFNIC) : owner birth city
		$birthDepartement = "";		//string birthDepartement : only for .fr (AFNIC) : owner birth french departement
		$birthCountry 		= "";		//string birthCountry : only for .fr (AFNIC) : owner bith country
		/* End of ToDo */
	 
		ovh_Debug("Parameters: $session, $domain, $hosting, $offer, $profile, $owo, $owner, $admin, $tech, $billing, $dns1, $dns2, $dns3, $dns4, $dns5, $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $dryRun");
		$soap->resellerDomainCreate($session, $domain, $hosting, $offer, $profile, $owo, $owner, $admin, $tech, $billing, $dns1, $dns2, $dns3, $dns4, $dns5, $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $dryRun);
	 	$soap->logout($session);
	 	
	 	# Save domain information into whmcs database
	 	ovh_Debug("Saving domain contacts into database");
	 	$domainid = $params['domainid'];
	 	$owner = strtoupper($owner);
	 	$admin = strtoupper($admin);
	 	$sql = "INSERT INTO mod_ovh_domains (domainid, owner, admin, tech, billing) values ('$domainid', '$owner', '$admin', '$tech', '$billing');";
	 	ovh_Debug($sql);
	 	mysql_query($sql);
	 	
	
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 	}
	}
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function ovh_TransferDomain($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* New Domain Transfer Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession($params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}
 	
 	# Check domain availability
 	$is_transferable = ovh_checkDomain($domainName, '1');
 	if(!$is_transferable->value) {
	 	$values["error"] = $is_transferable->reason;
	 	return $values;
	}

	# Create nic handle for owner contact
	$owner_response = ovh_nicCreateOwner($params);
	if($owner_response['error']) {
		$values["error"] = $owner_response['error'];
		return $values;
	} else {
		$owner = $owner_response['nic'];
		ovh_Debug("Owner contact: $owner");
	}

	# Create nic handle for admin contact
	$admin_response = ovh_nicCreateAdmin($params);
	if($admin_response['error']) {
		$values["error"] = $admin_response['error'];
		return $values;
	} else {
		$admin = $admin_response['nic'];
		ovh_Debug("Admin contact: $admin");
	}

	# Send transfer command
	try {
		$testmode		= ($params["TestMode"]=='on') ? 'true' : 'false';
		$transfersecret = $params['transfersecret'];
		
		$domain 	= $domainName;		//string domain : the domain name
		$authinfo	= $transfersecret;//stringauthinfo : authinfo code, mandatory for gTlds (.com, .net, .org, .info, .biz) and .pl
		$hosting 	= "none";					//string hosting : the hosting type (none|start1m|perso|pro|business|premium)
		$offer 		= "gold";					//string offer : the domain offer (gold|platinum|diamond)
		$profile 	= "whiteLabel";		//string profile : the reseller profile (none | whiteLabel | agent)
		$owo 			= "no";						//string owo : activate OwO for .com, .net, .org, .info and .biz (yes | no)
		$owner 		= $owner;					//string owner : the owner nichandle
		$admin 		= $admin;					//string admin : the admin nichandle
		$tech 		= $username;			//string tech : the tech nichandle
		$billing 	= $username;			//string billing : the billing nichandle
		$dns1 		= $params["ns1"];	//string dns1 : the primary dns hostname (if hosting, default OVH dns will be installed)
		$dns2 		= $params["ns2"];	//string dns2 : the secondary dns hostname
		$dns3 		= $params["ns3"];	//string dns3 : the third dns hostname
		$dns4 		= $params["ns4"];	//string dns4 : the fourth dns hostname
		$dns5 		= "";							//string dns5 : the fifth dns hostname
		$dryRun 	= $testmode;			//boolean dryRun : enable the TEST MODE when enabled (true), will not debit your account

		/* ToDo */
		$method 					= "";		//string method : only for .fr (AFNIC) : identification method (siren | inpi | birthPlace | afnicIdent)
		$legalName 				= "";		//string legalName : only for .fr (AFNIC) : corporation name /trademark owner
		$legalNumber 			= "";		//string legalNumber : only for .fr (AFNIC) : SIREN/SIRET/INPI number
		$afnicIdent 			= "";		//string afnicIdent : only for .fr (AFNIC) : afnic ident code
		$birthDate 				= "";		//string birthDate : only for .fr (AFNIC) : owner birth date
		$birthCity 				= "";		//string birthCity : only for .fr (AFNIC) : owner birth city
		$birthDepartement = "";		//string birthDepartement : only for .fr (AFNIC) : owner birth french departement
		$birthCountry 		= "";		//string birthCountry : only for .fr (AFNIC) : owner bith country
		/* End of ToDo */
	 
		ovh_Debug("Parameters: $session, $domain, $authinfo, $hosting, $offer, $profile, $owo, $owner, $admin, $tech, $billing, $dns1, $dns2, $dns3, $dns4, $dns5, $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $dryRun");
		$soap->resellerDomainTransfer($session, $domain, $authinfo, $hosting, $offer, $profile, $owo, $owner, $admin, $tech, $billing, $dns1, $dns2, $dns3, $dns4, $dns5, $method, $legalName, $legalNumber, $afnicIdent, $birthDate, $birthCity, $birthDepartement, $birthCountry, $dryRun);
	 	$soap->logout($session);
	
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 	}
	}
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function ovh_RenewDomain($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Set Name Servers Request: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

 	# Check domain availability
 	$is_renewable = ovh_checkDomain($domainName, '2');	// 2 = is_renewable
 	if(!$is_renewable->value) {
	 	$values["error"] = $is_renewable->reason;
	 	return $values;
	}

	try {
		$dryRun	= ($params["TestMode"]=='on') ? 'true' : 'false';
		$result = $soap->resellerDomainRenew($session, $domainName, $dryRun);
		return $result;
		
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
}

/*
function ovh_GetContactDetails($params) {
	global $soap, $session;
	$domainName = $params["sld"].".".$params["tld"];
	ovh_Debug("* Get Contact Details: ".$domainName);

	# OVH SOAP Init	
	$soap = ovh_initSoap();
	$session = ovh_initSession(&$params);
	if(is_array($session) && $session['error']) { 
		$values["error"] = $session['error'];	
		return $values;
	}

	# Get Domain OVH nics
	$domainid = $params['domainid'];
	$sql = "SELECT owner, admin FROM mod_ovh_domains WHERE domainid='$domainid'";
	ovh_debug($sql);
	$res = mysql_query($sql);
	if(!mysql_num_rows($res)) {
		$values['error'] = "No se encuentran los contactos para este dominio";
		return $values;
	}
	list($owner, $admin) = mysql_fetch_array($res);

	# Get owner details
	try {
		ovh_Debug("Get Owner Details");
		$resultOwner = $soap->nicInfo($session, $owner);
		$values["Registrant"]["First Name"] = $resultOwner->firstname;
		$values["Registrant"]["Last Name"] = $resultOwner->name;
		$values["Registrant"]["Organisation Name"] = $resultOwner->organisation;
		$values["Registrant"]["Email"] = $resultOwner->email;
		$values["Registrant"]["Address"] = $resultOwner->address;
		$values["Registrant"]["City"] = $resultOwner->city;
		$values["Registrant"]["State"] = $resultOwner->area;
		$values["Registrant"]["Postcode"] = $resultOwner->zip;
		$values["Registrant"]["Country"] = $resultOwner->country;
		$values["Registrant"]["Phone"] = $resultOwner->phone;
		$values["Registrant"]["Fax"] = $resultOwner->fax;
		$values["Registrant"]["Legal Name"] = $resultOwner->legalName;
		$values["Registrant"]["Legal Number"] = $resultOwner->legalNumber;
		$values["Registrant"]["VAT"] = $resultOwner->vat;
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
	
	# Get admin details
	try {
		ovh_Debug("Get Admin Details");
		$resultAdmin = $soap->nicInfo($session, $admin);
		$values["Admin"]["First Name"] 				= $resultAdmin->firstname;
		$values["Admin"]["Last Name"] 				= $resultAdmin->name;
		$values["Admin"]["Organisation Name"] = $resultAdmin->organisation;
		$values["Admin"]["Email"] 						= $resultAdmin->email;
		$values["Admin"]["Address"] 					= $resultAdmin->address;
		$values["Admin"]["City"] 							= $resultAdmin->city;
		$values["Admin"]["State"] 						= $resultAdmin->area;
		$values["Admin"]["Postcode"] 					= $resultAdmin->zip;
		$values["Admin"]["Country"]				 		= $resultAdmin->country;
		$values["Admin"]["Phone"] 						= $resultAdmin->phone;
		$values["Admin"]["Fax"] 							= $resultAdmin->fax;
		$values["Admin"]["Legal Name"]		 		= $resultAdmin->legalName;
		$values["Admin"]["Legal Number"] 			= $resultAdmin->legalNumber;
		$values["Admin"]["VAT"] 							= $resultAdmin->vat;
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
	
		return $values;
}

function ovh_SaveContactDetails($params) {
	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	# Data is returned as specified in the GetContactDetails() function
	$firstname = $params["contactdetails"]["Registrant"]["First Name"];
	$lastname = $params["contactdetails"]["Registrant"]["Last Name"];
	$adminfirstname = $params["contactdetails"]["Admin"]["First Name"];
	$adminlastname = $params["contactdetails"]["Admin"]["Last Name"];
	$techfirstname = $params["contactdetails"]["Tech"]["First Name"];
	$techlastname = $params["contactdetails"]["Tech"]["Last Name"];
	# Put your code to save new WHOIS data here
	# If error, return the error message in the value below
	$values["error"] = $error;
	return $values;
}

function ovh_GetEPPCode($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    # Put your code to request the EPP code here - if the API returns it, pass back as below - otherwise return no value and it will assume code is emailed
    $values["eppcode"] = $eppcode;
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}

function ovh_RegisterNameserver($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $ipaddress = $params["ipaddress"];
    # Put your code to register the nameserver here
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}

function ovh_ModifyNameserver($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    $currentipaddress = $params["currentipaddress"];
    $newipaddress = $params["newipaddress"];
    # Put your code to update the nameserver here
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}

function ovh_DeleteNameserver($params) {
    $username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
    $nameserver = $params["nameserver"];
    # Put your code to delete the nameserver here
    # If error, return the error message in the value below
    $values["error"] = $error;
    return $values;
}
*/

function ovh_nicCreateOwner($params) {
	# Create nic handle for owner contact
 	ovh_Debug("Creating owner contact");
	$RegistrantContact['name'] 				= $params["lastname"];
	$RegistrantContact['firstname']		= $params["firstname"];
	$RegistrantContact['password'] 		= $params[""];
	$RegistrantContact['email'] 			= $params["email"];
	$RegistrantContact['phone'] 			= $params["phonenumber"];
	$RegistrantContact['fax'] 				= $params[""];
	$RegistrantContact['address'] 		= $params["address1"];
	$RegistrantContact['city'] 				= $params["city"];
	$RegistrantContact['area'] 				= $params["state"];
	$RegistrantContact['zip'] 				= $params["postcode"];
	$RegistrantContact['country'] 		= $params["country"];
	$RegistrantContact['language'] 		= "es";
	$RegistrantContact['isOwner'] 		= true;
	$RegistrantContact['legalform']		= $params[""];
	$RegistrantContact['organisation']= $params[""];
	$RegistrantContact['legalName']		= $params[""];
	$RegistrantContact['legalNumber']	= $params[""];
	$RegistrantContact['vat']					= $params[""];
	$owner_response = ovh_nicCreate($RegistrantContact);
	if($owner_response['error']) {
		$values["error"] = "RegistrantContact " . $owner_response['error'];
		return $values;
	} else {
		return $owner_response;
	}
}

function ovh_nicCreateAdmin($params) {
	# Create nic handle for admin contact
	$AdminContact['name'] 				= $params["adminlastname"];
	$AdminContact['firstname']		= $params["adminfirstname"];
	$AdminContact['password'] 		= $params[""];
	$AdminContact['email'] 				= $params["adminemail"];
	$AdminContact['phone'] 				= $params["adminphonenumber"];
	$AdminContact['fax'] 					= $params[""];
	$AdminContact['address'] 			= $params["adminaddress1"];
	$AdminContact['city'] 				= $params["admincity"];
	$AdminContact['area'] 				= $params["adminstate"];
	$AdminContact['zip'] 					= $params["adminpostcode"];
	$AdminContact['country'] 			= $params["country"];
	$AdminContact['language'] 		= "es";
	$AdminContact['isOwner'] 			= false;
	$AdminContact['legalform']		= $params[""];
	$AdminContact['organisation']	= $params[""];
	$AdminContact['legalName']		= $params[""];
	$AdminContact['legalNumber']	= $params[""];
	$AdminContact['vat']					= $params[""];
 	ovh_Debug("Creating admin contact");
	$admin_response = ovh_nicCreate($AdminContact);
	if($admin_response['error']) {
		$values["error"] = "AdminContact " . $admin_response['error'];
		return $values;
	} else {
		return $admin_response;
	}
}

function ovh_nicCreate($contact) {
	global $soap, $session;
	try {
		$result = $soap->nicCreate($session, 
			$contact['name'], 
			$contact['firstname'], 
			$contact['password'], 
			$contact['email'], 
			$contact['phone'], 
			$contact['fax'], 
			$contact['address'], 
			$contact['city'], 
			$contact['area'], 
			$contact['zip'], 
			$contact['country'],
			$contact['language'], 
			$contact['isOwner'], 
			$contact['legalform'], 
			$contact['organisation'], 
			$contact['legalName'], 
			$contact['legalNumber'], 
			$contact['vat']
		);
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
 		$response['error'] =  "[".$fault->faultcode."] " . $fault->faultstring;
 		return $response;
	}
	ovh_Debug("New nic created: $result");
	$response['nic'] = $result;
	return $response;
}

function ovh_checkDomain($domainName, $action) {
	global $soap, $session;
 	ovh_Debug("Checking domain $domainName");
 	$result = $soap->domainCheck($session, $domainName);
	ovh_Debug("$domainName: ".$result[$action]->reason);
	return $result[$action];
}

function ovh_initSoap() {
	# Create a new SOAP instance
	ovh_Debug("Creating new SOAP instance");
	$soap = new SoapClient("https://www.ovh.com/soapi/soapi-re-1.11.wsdl");
	return $soap;
}

function ovh_initSession($params) {
	# Start a new SOAP session
	global $soap, $session;
	ovh_Debug("Creating new SOAP session");
	$username = $params["Username"];
	$password = $params["Password"];
	try {
	 	$session = $soap->login($username, $password, "en", false);
	} catch(SoapFault $fault) {
		ovh_Debug("faultcode:   ".$fault->faultcode);
		ovh_Debug("faultstring: ".$fault->faultstring);
		if($fault->faultcode) {
	 		$error = "[".$fault->faultcode."] " . $fault->faultstring;
	 		$result['error'] = $error;
	 		return $result;
	 	}
	}
 	ovh_Debug("OVH session: $session");
 	return $session;
}

function ovh_Debug($string="", $die=false) {
	global $params;
	
	// Check if debug is enabled
	$debug = ($params["Debug"]=="on") ? true : false;
	if(!$debug)	return;
	
	// Gets debug filename
	$ovh_logfile = dirname(__FILE__) . "/ovh." . date("Ymd") . ".log";
	
	// Saves debug
	$string = date("H:i:s"). " " .$string."\n";
	$fp = fopen($ovh_logfile, "a");
	fwrite($fp, $string);
	fclose($fp);
	
	// Prints debug on screen
	echo $string . "<br>";

	// Should I die?
	if($die) die();
}

?>