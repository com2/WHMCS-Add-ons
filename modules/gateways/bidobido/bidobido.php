<?php
/**
 * Modulo de pago para BidoBido.com
 *
 * Este módulo permite usar la pasarela de pagos BidoBido.com
 * utilizando un nombre de usuario, una contraseña y una URL de pago
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
 * @package    bidobido.php
 * @author     Eduardo Gonzalez <egrueda@gmail.com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.0.2
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/
 
function bidobido_config() {
   $configarray = array(
	   "FriendlyName" 	=> array("Type" => "System", "Value"=>"BidoBido.com"),

	   "identificador"  => array("Type" => "text",   "FriendlyName" => "Usuario BidoBido",    "Size" => "20"),
	   "contrasena"   	=> array("Type" => "text",   "FriendlyName" => "Contraseña BidoBido", "Size" => "20"),
	   "nombreComercio"	=> array("Type" => "text",   "FriendlyName" => "Nombre del comercio", "Size" => "30"),
     "test_url"       => array("FriendlyName" => "URL Pruebas",     "Type" => "yesno", "Description" => "Seleccione la casilla para usar URL de pruebas", ),
     "debug_mode"     => array("FriendlyName" => "Habilitar debug", "Type" => "yesno", "Description" => "Modo debug (solo admins)", ),

     "idioma_usuario"   => array("FriendlyName" => "Idioma",      "Type" => "dropdown", "Options" => "1", ),
     "tipo_transaccion" => array("FriendlyName" => "Tipo",        "Type" => "dropdown", "Options" => "0", ),
     "moneda"           => array("FriendlyName" => "Moneda",      "Type" => "dropdown", "Options" => "1", ),
     "terminal"         => array("FriendlyName" => "Terminal",    "Type" => "dropdown", "Options" => "1", ),
     "test"             => array("FriendlyName" => "Modo Prueba", "Type" => "dropdown", "Options" => "0", ),

	  );
   return $configarray;
}

function bidobido_activate(){
   return true;
}

function bidobido_link($params) {
	
	$num_factura     = $params['invoiceid'];
	$description     = $params["description"];
	$cantidad        = str_replace('.', '', number_format($params['amount'], 2, '.', ''));
	$url_pago_oculto = $params['systemurl']."/modules/gateways/callback/".$params['paymentmethod'].".php?invoiceid=$num_factura";
	$url_pago_ok     = $params['returnurl'];
	$url_pago_ko     = $params['returnurl'];
	$urlPago         = ($params['test_url']=='on') ? "http://demo.bidobido.com/pay/bidopay_oculto.php" : "https://www.bidobido.com/pagos/bidopay_oculto.php";
	bidobido_debug($urlPago);

	$identificador_bidobido = $params['identificador'];
	$contrasena_metodo_pago = $params['contrasena'];
	$comercio               = $params['nombreComercio'];
	$email                  = $params['clientdetails']['email'];
	$comercio               = $params['nombreComercio'];
	$moneda                 = $params['moneda'];
	$terminal               = $params['terminal'];
	$tipo_transaccion       = $params['tipo_transaccion'];
	$idioma_usuario         = $params['idioma_usuario'];
	$test                   = $params['test'];
	bidobido_debug("User:<br />$identificador_bidobido");
	bidobido_debug("Pass:<br />$contrasena_metodo_pago");
	
	// Generamos la firma
	$referencia = rand(10,99).time(); 
	bidobido_debug("Referencia:<br />$referencia");

	$mensaje['cantidad']               = $cantidad;
	$mensaje['referencia']             = $referencia;
	$mensaje['moneda']                 = $moneda;
	$mensaje['tipo_transaccion']       = $tipo_transaccion;
	$mensaje['identificador_bidobido'] = $identificador_bidobido;
	$mensaje['contrasena_metodo_pago'] = $contrasena_metodo_pago;
	$mensaje['test']                   = $test;
	bidobido_debug($mensaje);

	$mensaje = implode("", $mensaje);
	bidobido_debug("Mensaje:<br />$mensaje");

	$firma = sha1($mensaje);
	bidobido_debug("Firma:<br />$firma");

	// Generamos la cadena
	$cadena['referencia']             = $referencia;
	$cadena['cantidad']               = $cantidad;
	$cadena['moneda']                 = $moneda;
	$cadena['terminal']               = $terminal;
	$cadena['tipo_transaccion']       = $tipo_transaccion;
	$cadena['idioma_usuario']         = $idioma_usuario;
	$cadena['identificador_bidobido'] = $identificador_bidobido;
	$cadena['firma']                  = $firma;
	$cadena['url_pago_oculto']        = $url_pago_oculto;
	$cadena['url_pago_ok']            = $url_pago_ok;
	$cadena['url_pago_ko']            = $url_pago_ko;
	$cadena['comercio']               = $comercio;
	$cadena['test']                   = $test;
	$cadena['num_factura']            = $num_factura;
	$cadena = bidobido_cadena($cadena);

	// Enviamos la peticion a la URL de BidoBido
	$respuesta = bidobido_peticion($cadena, $urlPago);
	if($respuesta) {
		bidobido_debug("respuesta:<br />$respuesta");
		$url       = parse_url($respuesta);
		bidobido_debug($url);
		if($url['query']) 
			$queries = explode('&', $url['query']);
		bidobido_debug($queries);
	
		// Si me devuelven una URL
		if(is_array($queries)){
			$action = $url['scheme'] . '://' . $url['host'] . $url['path'];
			$code = '<form method="get" action="'.$action.'">';
			foreach($queries as $query) {
				list($name, $value) = explode('=', $query);
				$code .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
			}
			$code .= '<input type="submit" value="Pagar" /></form>';
		// Si me devuelven un ERROR
	   }else{
	      $code='error';
	   }
	   return $code;
	 }
}

function bidobido_cadena($cadena) {
	bidobido_debug($cadena);
   $post_url="referencia=".$cadena['referencia'].
      "&cantidad=".$cadena['cantidad'].
      "&moneda=".$cadena['moneda'].
      "&terminal=".$cadena['terminal'].
      "&URL_respuesta=".urlencode($cadena['url_pago_oculto']).
      "&UrlKO=".urlencode($cadena['url_pago_ko']).
      "&UrlOK=".urlencode($cadena['url_pago_ok']).
      "&idioma=".$cadena['idioma_usuario'].
      "&empresa_id=".$cadena['identificador_bidobido'].
      "&comercio=".$cadena['comercio'].
      "&tipo_transaccion=".$cadena['tipo_transaccion'].
      "&ref_descrip=".$cadena['num_factura'].
      "&firma=".$cadena['firma'].
      "&test=".$cadena['test'];
	return $post_url;
}

function bidobido_peticion($cadena, $url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $cadena);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$resultado = curl_exec($ch);
	curl_close($ch);

   if(ereg('\<error\>',$resultado)){
      preg_match_all ("/<error>(.*?)<\/error>/",$resultado,$error);
      $error=str_replace('<![CDATA[','',$error[1][0]);
      $error=str_replace(']]>','',$error);
      echo 'error: '.$error;
      return false;
   }elseif(ereg('\<url\>',$resultado)){
      preg_match_all ("/<url>(.*?)<\/url>/",$resultado,$url);
      $url=str_replace('<![CDATA[','',$url[1][0]);
      $url=str_replace(']]>','',$url);
      return $url;
   }else{
      echo 'Error no especificado';
      return false;
   }
}

function bidobido_refund($params) {
   return false;
}

function bidobido_debug($string) {
	if($_SESSION['adminid']) {
		global $params;
		if($params['debug_mode']=="on") {
			echo "<pre>\n";
			if(is_array($string)) {
				print_r($string);
			} else {
				echo $string;
			}
			echo "</pre>\n";
		}
		
	}
}
$GATEWAYMODULE['bidobidoname']        = 'bidobido';
$GATEWAYMODULE['bidobidovisiblename'] = 'BidoBido';
$GATEWAYMODULE['bidobidotype']        = 'Invoices';

?>