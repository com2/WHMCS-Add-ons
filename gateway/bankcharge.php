<?php
/**
 * Modulo de pago para Recibo Bancario
 *
 * Este módulo ofrece la posibilidad de pago por recibo bancario
 * y permite introducir la cuenta bancaria para la domiciliacion
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
 * @package    bankcharge.php
 * @author     Eduardo Gonzalez <egrueda@gmail.com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.0
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

$GATEWAYMODULE["bankchargename"]        = "bankcharge";
$GATEWAYMODULE["bankchargevisiblename"] = "Recibo bancario";
$GATEWAYMODULE["bankchargetype"]        = "Invoices";

function bankcharge_config() {
	$result = select_query("tblcustomfields","fieldname","1");
	$data = mysql_fetch_array($result);
	$fields = implode(',', $data);
	echo "\$fields: $fields<br>\n";
	$configarray = array(
		"FriendlyName" 	=> array("Type" => "System", "Value"=>"Recibo bancario"),
		//"identificador" => array("Type" => "text",   "FriendlyName" => "Custom Field", "Size" => "1")
    "custom_field"  => array("FriendlyName" => "Custom Field", "Type" => "dropdown", "Options" => "Option1,Value2,Method3", ),
	);
	return $configarray;
}
function bankcharge_activate() {}

function bankcharge_link($params) {
	if ($_POST["bankchargesave"]) {
		if ((!$_REQUEST["part1"])OR(!$_REQUEST["part2"])OR(!$_REQUEST["part3"])OR(!$_REQUEST["part4"])) {
			$code.="<div align=center style=\"color:#cc0000;\"><strong>You must fill out all the fields</strong></div>";
		} else {
			$code.="<div align=center style=\"color:#cc0000;\"><strong>Banking Details Saved Successfully!</strong></div>";
			$bankchargevalue = $_REQUEST["part1"].$_REQUEST["part2"].$_REQUEST["part3"].$_REQUEST["part4"];
			delete_query("tblcustomfieldsvalues",array("fieldid"=>"2","relid"=>$params['clientdetails']['userid']));
			insert_query("tblcustomfieldsvalues",array("fieldid"=>"2","relid"=>$params['clientdetails']['userid'],"value"=>$bankchargevalue));
			$done=true;
		}
	}
    $result = select_query("tblcustomfieldsvalues","value",array("fieldid"=>"2","relid"=>$params['clientdetails']['userid']));
    $data = mysql_fetch_array($result);
		$bankchargevalue = $data["value"];
		$entidad = substr($bankchargevalue, 0, 4);
		$oficina = substr($bankchargevalue, 4, 4);
		$dc      = substr($bankchargevalue, 8, 2);
		$cuenta  = substr($bankchargevalue, 10, 10);
//    if ($bankchargevalue) {
//        $code='';
//    } else {
        if (!$done) {
    	    $code.='
    <form method="post" action="'.$_SERVER["PHP_SELF"].'?id='.$params['invoiceid'].'">
    <input type="hidden" name="bankchargesave" value="true" />
    <input type="text" name="part1" value="'.$entidad.'" size="4"  maxlength="4" />.
    <input type="text" name="part2" value="'.$oficina.'" size="4"  maxlength="4" />.
    <input type="text" name="part3" value="'.$dc.'"      size="2"  maxlength="2" />.
    <input type="text" name="part4" value="'.$cuenta.'"  size="10" maxlength="10" /><br />
    <input type="submit" value="Confirmar cuenta" />
    </form>
    ';
        }
//    }
	return $code;
}

?>