<?php
/*
WHMCS API PHP Class version 1.0
Author: Eduardo Gonzalez <egrueda@gmail.com>
Date:  Jan/2009
Info:
	This class helps making API interfaces
	It creates an order for each domain
WHMCS API PHP Class is Free Software released under the GNU/GPL License
*/

class Wapi {
	
	// Default values for API access
	var $api_url  = "http://127.0.0.1/whmcs/includes/api.php";
	var $api_user = "admin";
	var $api_pass = "1234";
	
	function __construct($api_url='', $api_user='', $api_pass='') {
		if(!empty($api_url))  $this->api_url  = $api_url;
		if(!empty($api_user)) $this->api_user = $api_user;
		if(!empty($api_pass)) $this->api_pass = $api_pass;
	}
	
	function call($postfields) {

		// Get API username and password
		$postfields["username"] = $this->api_user;
		$postfields["password"] = md5($this->api_pass);

		// Make curl request
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		$data = curl_exec($ch);
		curl_close($ch);

		// Format response
		$data = explode(";",$data);
		foreach ($data AS $temp) {
		  $temp  = explode("=",$temp);
		  $key   = trim($temp[0]);
		  $value = trim($temp[1]);
		  $results[$key] = $value;
		}
		
		// Returns array with response
		return $results;
	}
	
}


?>