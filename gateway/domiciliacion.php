<?php
/**
 * Modulo de pago para Domiciliacion Bancaria
 *
 * Este módulo de pago simplemente declara la forma de pago con el
 * nombre de "domiciliacion" para su procesamiento posterior
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
 * @package    domiciliacion.php
 * @author     Eduardo Gonzalez  Rueda<egrueda@gmail.com>
 * @copyright  2009 Eduardo Gonzalez Rueda
 * @version    1.0
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/
 
function domiciliacion_activate () {
	global $GATEWAYMODULE;
	definegatewayfield ('domiciliacion', 'textarea', 'instructions', 'El importe sera cargado en su cuenta bancaria', 'Instrucciones Domiciliación Bancaria', '5', 'Instrucciones para el cliente');
}

function domiciliacion_link ($params) {
	global $_LANG;
	$code = '<p>' . nl2br ($params['instructions']) . '</p>';
	return $code;
}

$GATEWAYMODULE['domiciliacionname'] = 'domiciliacion';
$GATEWAYMODULE['domiciliacionvisiblename'] = 'Domiciliación Bancaria';
$GATEWAYMODULE['domiciliaciontype'] = 'Invoices';

?>