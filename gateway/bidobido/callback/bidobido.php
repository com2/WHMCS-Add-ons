<?php
/**
 * Callback del modulo de pago para BidoBido.com
 *
 * Este módulo recoge la respuesta de la pasarela BidoBido.com,
 * actualiza el estado de la factura y graba la transacción
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
 * @package    callback/bidobido.php
 * @author     Eduardo Gonzalez <egrueda@gmail.com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.0.2
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

# Nombre de la pasarela
$gatewaymodule = "bidobido";
$GATEWAY = getGatewayVariables($gatewaymodule);

# Comprobar que la pasarela esta activada
if (!$GATEWAY["type"]) die("Module Not Activated"); 

# Recibe la respuesta por POST y el invoiceid por GET
$status    = $_POST["resultado"];
$invoiceid = $_GET["invoiceid"];
$transid   = $_POST["referencia"];
$amount    = $_POST["cantidad"]/100;
$fee       = $_POST["x_fee"];

# Agrego el invoiceid en la variable POST
$_POST['invoiceid'] = $invoiceid;

# Comprueba que el invoiceid sea valido o aborta el proceso
$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]);

# Comprueba que ela transaccion no exista ya en la base de datos o aborta el proceso
checkCbTransID($transid);

# Comprueba que la firma de la transacción sea correcta
$identificador = $GATEWAY['identificador'];
$contrasena    = $GATEWAY['contrasena'];
$sql = "SELECT total FROM tblinvoices WHERE id='$invoiceid'";
$res = mysql_query($sql) or die("ERR3");
list($total) = mysql_fetch_array($res);
$total   = $total*100;
$message = $total.$transid.$GATEWAY['moneda'].$identificador.$contrasena.$status;
$firma   = sha1($message);
if($firma != $_POST['firma']) {
	logTransaction($GATEWAY["name"],$_POST,"Unsuccessful (ERR4)");
	die("ERR4");
}


# Si la respuesta de la pasarela es 'ok'
if ($status=="ok") {
	# Crea un pago para la factura y notifica por email
	addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule);
	# Guarda los datos en el registro de la pasarela
	logTransaction($GATEWAY["name"],$_POST,"Successful");

# Si la respuesta de la pasarela es distinta de 'ok'
} else {
	# Guarda los datos en el registro de la pasarela
	logTransaction($GATEWAY["name"],$_POST,"Unsuccessful");
}

?> 