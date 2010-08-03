<?php
/**
 * Modulo de pago para pagos en efectivo
 *
 * Este módulo de pago simplemente declara la forma de pago con el
 * nombre de "efectivo" para su procesamiento posterior
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
 * @package    efectivo.php
 * @author     Eduardo Gonzalez <egrueda@gmail.com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.0
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/
 
function efectivo_activate () {
	global $GATEWAYMODULE;
	definegatewayfield ('efectivo', 'textarea', 'instructions', 'El pago se realizará en efectivo', 'Instrucciones Pago en efectivo', '5', 'Instrucciones para el cliente');
}

function efectivo_link ($params) {
	global $_LANG;
	$code = '<p>' . nl2br ($params['instructions']) . '</p>';
	return $code;
}

$GATEWAYMODULE['efectivoname'] = 'efectivo';
$GATEWAYMODULE['efectivovisiblename'] = 'Efectivo / Metálico';
$GATEWAYMODULE['efectivotype'] = 'Invoices';

?>