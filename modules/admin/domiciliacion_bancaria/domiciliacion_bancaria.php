<?php
/**
 * Generacion de fichero de adeudos domiciliados
 *
 * Este módulo genera un fichero de adeudos domiciliados según
 * el formato del Cuaderno 19 de la Asociación Española de Banca
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
 * @package    domiciliacion_bancaria.php
 * @author     Eduardo Gonzalez <egrueda at gmail dot com>
 * @copyright  2010 Eduardo Gonzalez
 * @version    1.2.11
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/
 
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

// Funciones generales
include_once("../includes/invoicefunctions.php");

// Configuracion general
$module_name = "domiciliacion_bancaria";
$module_path = dirname(__FILE__);

// Tablas base de datos
$tbl_remesas  = "mod_domiciliaciones_remesas";
$tbl_recibos  = "mod_domiciliaciones_recibos";
$tbl_config   = "mod_domiciliaciones_config";

# Comprobamos la instalacion
$is_installed = TRUE;
if(
	!mysql_num_rows( mysql_query("SHOW TABLES LIKE '$tbl_config'")) ||
	!mysql_num_rows( mysql_query("SHOW TABLES LIKE '$tbl_remesas'")) ||
	!mysql_num_rows( mysql_query("SHOW TABLES LIKE '$tbl_recibos'"))
	) {
	if (!$_GET["install"]=='true') {
		$is_installed = FALSE;
		echo "<p><strong>M&oacute;dulo no instalado</strong></p>\n";
		echo "<p>Este m&oacute;dulo requiere de una instalaci&oacute;n para funcionar correctamente.</p>\n";
		echo "<p>Para instalarlo, pulsa el siguiente bot&oacute;n:</p>\n";
		echo '<p><input type="button" value="Instalar Domiciliaciones" onclick="window.location=\''.$modulelink.'&install=true\'"></p>';
	} else {
		instalacion();
		header("Location: $modulelink");
		exit;
	}
}

# Leemos la configuracion almacenada
if($is_installed) {
	$sql_config = "SELECT config, value FROM $tbl_config";
	$res_config = mysql_query($sql_config) or die($sql_config);
	$num_config = mysql_num_rows($res_config);
	while(list($config, $value) = mysql_fetch_array($res_config)) {
		$config_wdb[$config] = $value;
	}
}

// Modo de depuracion
$debug  = ($config_wdb['debug']) ? "1" : "0";

# Almacenamos la nueva configracion
if($_POST['configure']) {
	$config_wdb_excluded = array('token', 'configure', 'module');
		
	// Almacenamos la configuracion en mysql
	mysql_query("TRUNCATE TABLE $tbl_config");
	foreach($_POST as $config=>$value) {
		$config = trim($config); 
		$value  = trim($value);
		if(!in_array($config, $config_wdb_excluded)) 
		  mysql_query("INSERT INTO $tbl_config (config, value) VALUES ('$config', '$value');");
	}
	header("Location: $modulelink");
}

# Si nos piden una descarga, servimos el archivo
if($_GET['download']) {
	ob_end_clean();
  header("Content-disposition: attachment; filename=".$_GET['download']);
  header("Content-type: application/octet-stream");
  readfile($module_path."/".$_GET['download']);
  exit(0);
}

# Si nos envian un listado de facturas, generamos el archivo C19
if($_POST['export'] && $_POST['selectedinvoices']) {
	generar_c19();
} elseif($is_installed) {
	welcome_screen();
}

# Muestra la pantalla inicial
function welcome_screen () {
	# Javascript necesario
	echo '
	<script language="javascript">
	
	  function checkAllInvoices() {
	      var nodoCheck = document.getElementsByTagName("input");
	      var varCheck = document.getElementById("checkall").checked;
	      for (i=0; i<nodoCheck.length; i++){
	          if (nodoCheck[i].type == "checkbox" && nodoCheck[i].name != "all" && nodoCheck[i].disabled == false) {
	              nodoCheck[i].checked = varCheck;
	          }
	      }
	  }
	
		function check_selectedinvoices() {
			var varCheck = false;
	    var nodoCheck = document.getElementsByTagName("input");
	    for (i=0; i<nodoCheck.length; i++){
	    	if (nodoCheck[i].checked == true) {
	      	varCheck = true;
				}
	    }
	    if(!varCheck) alert("Por favor, seleccione las facturas que desea exportar");
			return varCheck;
		}

	$(document).ready(function(){

    $(".tabbox").css("display","none");
    $(".tabmain").css("display","");
		
		var selectedTab;
		$(".tab").click(function(){
		    var elid = $(this).attr("id");
		    $(".tab").removeClass("tabselected");
    		$("#"+elid).addClass("tabselected");
    		$(".tabbox").slideUp();
		    if (elid != selectedTab) {
		        selectedTab = elid;
		        $("#"+elid+"box").slideDown();
		    } else {
		        selectedTab = null;
		        $(".tab").removeClass("tabselected");
		    }
		    $("#tab").val(elid.substr(3));
		});


    $(".remesarow").css("display","none");
		$(".remesa").click(function(){
		    var elid = $(this).attr("id");
        $("#"+elid+"row").toggle();
		});
		
	});
		
	</script>
	';
	
	# Barra superior con tabs
	$tabs_open = '<div id="content_padded">
	<div id="tabs">
		<ul>
			<li id="tab0" class="tab tabselected"><a href="javascript:;">Facturas</a>
			<li id="tab1" class="tab"><a href="javascript:;">Historial</a>
			<li id="tab2" class="tab"><a href="javascript:;">Ajustes</a>
			</li>
		</ul>
	</div>';

	$tab_facturas  = '	<div id="tab0box" class="tabbox tabmain"><div id="tab_content">'.generar_tab_facturas() .'</div></div>';
	$tab_historial = '	<div id="tab1box" class="tabbox"><div id="tab_content">'.generar_tab_historial().'</div></div>';
	$tab_ajustes   = '	<div id="tab2box" class="tabbox"><div id="tab_content">'.generar_tab_ajustes()  .'</div></div>';
	$tabs_close    = '</div>';

	# Muestro las pestañas superiores
	echo $tabs_open."\n".$tab_facturas."\n".$tab_historial."\n".$tab_ajustes."\n".$tabs_close;
	
}

# Almacena el historial
function guardar_historial($remesa, $historial) {

	global $tbl_remesas, $tbl_recibos, $tbl_facturas;

	// Almaceno la remesa
	$num_recibos = count($historial);
	for($i=0; $i < $num_recibos; $i++) {
	  list($id_cliente, $cod_devolucion, $ref_interna, $invoice, $importe) = $historial[$i];
		$importe = str_replace(',', '.', $importe);
		$total_importe += $importe;
	}
	unset($id_cliente, $cod_devolucion, $ref_interna, $importe);
	$sql  = "INSERT INTO $tbl_remesas (remesa, num_recibos, importe)";
	$sql .= " VALUES ('$remesa', '$num_recibos', '$total_importe');";
	$res = mysql_query($sql) or die("ERROR1: ".mysql_error());
	$remesa_id = mysql_insert_id();
	
	// Almaceno los recibos
	for($i=0; $i < $num_recibos; $i++) {
	  list($id_cliente, $cod_devolucion, $ref_interna, $invoice, $importe) = $historial[$i];
		$importe = str_replace(',', '.', $importe);
		$sql_recibos  = "INSERT INTO $tbl_recibos (remesa_id, cod_devolucion, ref_interna, factura, importe)";
		$sql_recibos .= " VALUES ('$remesa_id', '$cod_devolucion', '$ref_interna', '$invoice', '$importe');";
		$res_recibos = mysql_query($sql_recibos) or die("ERROR2: ".mysql_error());
		$recibo_id = mysql_insert_id();
	}
	unset($id_cliente, $cod_devolucion, $ref_interna, $importe);
	
}

# Instalar el modulo
function instalacion() {
	
	global $tbl_remesas, $tbl_recibos, $tbl_facturas, $tbl_config;

	$sql = "CREATE TABLE IF NOT EXISTS `$tbl_remesas` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `remesa` varchar(15) CHARACTER SET utf8 DEFAULT '0',
	  `fecha` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
	  `num_recibos` int(11) DEFAULT NULL,
	  `importe` float DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	debug($sql);
	$res = mysql_query($sql) or die("<pre>$sql</pre>ERROR: ".mysql_error());


	$sql = "CREATE TABLE IF NOT EXISTS `$tbl_recibos` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `remesa_id` int(11) DEFAULT NULL,
	  `cod_devolucion` int(11) DEFAULT NULL,
	  `ref_interna` int(11) DEFAULT NULL,
	  `factura` int(11) DEFAULT NULL,
	  `importe` float DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	debug($sql);
	$res = mysql_query($sql) or die("<pre>$sql</pre>ERROR: ".mysql_error());
	
	$sql = "CREATE TABLE IF NOT EXISTS `$tbl_config` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `config` varchar(15) DEFAULT NULL,
	  `value` varchar(25) DEFAULT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8";
	debug($sql);
	$res = mysql_query($sql) or die("<pre>$sql</pre>ERROR: ".mysql_error());

	$sql = "INSERT INTO `$tbl_config` (`config`, `value`) VALUES
	('name', 'Nombre de Empresa'),
	('cc', '11112222004444444444'),
	('nif', 'A01020304'),
	('sufijo', '000'),
	('customfield_tit', 'n'),
	('customfield_nif', '1'),
	('customfield_cc', '2'),
	('line_feed', '0'),
	('file_ext', 'c19'),
	('payment_method', 'domiciliacion'),
	('invoice_status', 'Unpaid'),
	('mark_paid', '0'),
	('debug', '0');";
	debug($sql);
	$res = mysql_query($sql) or die("<pre>$sql</pre>ERROR: ".mysql_error());
}

# Contenido de la pestaña Facturas
function generar_tab_facturas() {

	global $config_wdb, $module_name;
	$payment_method  = ($_POST['payment_method'])  ? $_POST['payment_method']  : $config_wdb['payment_method'];
	$customfield_cc  = ($_POST['customfield_cc'])  ? $_POST['customfield_cc']  : $config_wdb['customfield_cc'];
	$customfield_nif = ($_POST['customfield_nif']) ? $_POST['customfield_nif'] : $config_wdb['customfield_nif'];
	$invoice_status  = ($_POST['invoice_status'])  ? $_POST['invoice_status']  : $config_wdb['invoice_status'];
	debug("\$payment_method: $payment_method");
	debug("\$invoice_status: $invoice_status");
	debug("Config: " . print_r($config_wdb, true));

	$output = "<form method=\"post\">\n";
	$output .= "<table width=100% style=\"border: solid 1px #ECECEC;\">\n";
	$output .= "	<tr align=\"center\">\n";
	$output .= "		<td><strong>Filtrar facturas</strong></td>\n";
	$output .= "		<td>\n";
	$output .= "			Forma de pago:&nbsp;";
	$output .= "		</td>\n";
	$output .= "		<td>\n";
	$output .= sql_dropdown("payment_method", "tblpaymentgateways", "", "gateway", $payment_method);
	$output .= "		</td>\n";
	$output .= "		<td>\n";
	$output .= "			Estado:&nbsp;\n";
	$output .= "		</td>\n";
	$output .= "		<td>\n";
	$output .= manual_dropdown("invoice_status", array("Paid"=>"Pagadas", "Unpaid"=>"No pagadas"), $invoice_status);
	$output .= "		</td>\n";
	$output .= "		<td align=\"center\">\n";
	$output .= "			<input type=\"submit\" name=\"filtrar\" value=\"Filtrar\">\n";
	$output .= "		</td>\n";
	$output .= "	</tr>\n";
	$output .= "</table>\n";
	$output .= "<input type=\"hidden\" name=\"module\" value=\"".$_GET['module']."\">\n";
	$output .= "</form>\n";
	
	$sql = "SELECT IF(LENGTH(c.companyname), c.companyname, CONCAT(c.firstname,' ', c.lastname)) cliente, c.id client_id,
					n.value nif, i.id, i.total, i.status, i.paymentmethod, b.value cc
	FROM tblinvoices i
	INNER JOIN tblclients c ON i.userid=c.id
	LEFT JOIN tblcustomfieldsvalues n ON c.id=n.relid
	LEFT JOIN tblcustomfieldsvalues b ON c.id=b.relid
	WHERE 1
	AND i.status='".$invoice_status."'
	AND i.paymentmethod='".$payment_method."'
	AND n.fieldid=".$customfield_nif."
	AND b.fieldid=".$customfield_cc;

	$sql = "SELECT 
		c.companyname, CONCAT(c.firstname,' ', c.lastname) cliente";
	if(is_numeric($config_wdb['customfield_tit'])) {
		$sql .= "\n		, (SELECT tit.value FROM tblcustomfieldsvalues tit WHERE tit.relid = c.id AND tit.fieldid=".$config_wdb['customfield_tit'].") titular";
	}
	$sql .= "
		, c.id client_id
		, (SELECT n.value FROM tblcustomfieldsvalues n WHERE n.relid = c.id AND n.fieldid=".$customfield_nif." LIMIT 1) nif
		, i.id invoiceid
		, i.total
		, i.status
		, i.paymentmethod
		, c.id
		, (SELECT b.value FROM tblcustomfieldsvalues b WHERE b.relid = c.id AND b.fieldid=".$customfield_cc." LIMIT 1) cc
	FROM tblinvoices i
		INNER JOIN tblclients c ON i.userid=c.id
	WHERE 1
		AND i.status='".$invoice_status."'
		AND i.paymentmethod='".$payment_method."'
	";

	$orderdir = $_GET['orderby'];
	switch($_GET['order']) {
		case "asc": $sql_order = "asc";  $link_order = "desc"; $img_order = "desc.gif"; break;
		case "des": $sql_order = "desc"; $link_order = "asc";  $img_order = "asc.gif";  break;
		default:    $sql_order = "asc";  $link_order = "desc"; $img_order = "desc.gif"; break;
	}
	
	$orderby = $_GET['orderby'];
	switch($_GET['orderby']) {
		case "client_id":  $sql_orderby = "ORDER BY c.id $sql_order"; break;
		case "invoice_id": $sql_orderby = "ORDER BY i.id $sql_order"; break;
		default:					 $sql_orderby = "ORDER BY i.id $sql_order"; break;
	}

	$sql .= " $sql_orderby";		
	
	debug($sql);
	$res = mysql_query($sql);
	if($res) $num = mysql_num_rows($res);
	if(!$num) {
		$output .= "No se han encontrado facturas que coincidan con el filtro<br>\n";
	} else {
		$output .= "Encontrados $num registros<br>\n";
		
		# Listado de facturas segun el filtro aplicado
		$output .= "<form method=\"post\" name=\"invoices\">\n";
		$output .= "<table class=\"datatable\" border='0' width=\"100%\" cellpadding=\"2\">\n";
		$output .= "	<tr>\n";
		$output .= "		<th><input type=\"checkbox\" id=\"checkall\" name=\"checkall\" onClick=\"checkAllInvoices()\"></th>\n";
		$output .= "		<th>\n";
		$output .= "			<a href='/whmcs/admin/addonmodules.php?module=$module_name&orderby=client_id'>Cliente</a>\n";
		if($orderby == 'client_id') $output .= "      <img src='images/$img_order' class='absmiddle' />\n";
		$output .= "    </th>\n";
		$output .= "		<th>NIF/CIF</th>\n";
		$output .= "		<th>\n";
		$output .= "		  <a href='/whmcs/admin/addonmodules.php?module=$module_name&orderby=invoice_id'>Factura</a>\n";
		if($orderby == 'invoice_id') $output .= "      <img src='images/$img_order' class='absmiddle' />\n";
		$output .= "		</th>\n";
		$output .= "		<th>Importe</th>\n";
		$output .= "		<th>Forma pago</th>\n";
		$output .= "		<th>Estado</th>\n";
		$output .= "		<th>Cuenta</th>\n";
		$output .= "	</tr>\n";
		while($row = mysql_fetch_array($res)) {

			// Defino el campo del titular de la cuenta
			if(empty($config_wdb['customfield_tit'])) $config_wdb['customfield_tit'] = 'n';
			switch($config_wdb['customfield_tit']) {
				case 'n':	// Utilizo el nombre completo 
					$nombre_cliente = $row['cliente'];		 
					break; 
				case 'c':	// Utilizo el nombre de la empresa (fallback al nombre del cliente)
					$nombre_cliente = ($row['companyname']) ? $row['companyname'] : $row['cliente'];
					break;  
				default:	// Utilizo el custom field  
					$nombre_cliente = $row['titular'];     
					break;
			}

			// Validacion de CC
			$valid_cc = (validate_cc($row['cc'])) ? "1" : "0";
			$checkboxEnabled = ($valid_cc) ? "" : "disabled";
			$cc_color        = ($valid_cc) ? "green" : "red";

			// Validacion de importe
			if($row['total']=='0.00' || $row['total']<'0') {
				$checkboxEnabled = "disabled";
				$total_color = "red";
			} else {
				$total_color = "";
			}

			$output .= "	<tr>\n";
			$output .= "		<td align=\"center\"><input type=\"checkbox\" name=\"selectedinvoices[]\" value=\"".$row['invoiceid']."\" class=\"checkall\" $checkboxEnabled></td>\n";
			$output .= "		<td><a href=\"clientssummary.php?userid=".$row['client_id']."\">".$nombre_cliente."</a></td>\n";
			$output .= "		<td align='center'>".$row['nif']."</td>\n";
			$output .= "		<td align='center'><a href=\"invoices.php?action=edit&id=".$row['invoiceid']."\">".$row['invoiceid']."</a></td>\n";
			$output .= "		<td align='right' style='color:$total_color;'>".$row['total']." €</td>\n";
			$output .= "		<td align='center'>".$row['paymentmethod']."</td>\n";
			$output .= "		<td align='center'>".$row['status']."</td>\n";
			$output .= "		<td align='center' style='color:$cc_color;'>..".substr($row['cc'],-4,4)." $cc_image</td>\n";
			$output .= "	</tr>\n";
		}	
		$output .= "	<tr>\n";
		$output .= "		<td colspan=\"8\" align=\"center\">\n";
		$output .= "			Marcar como 'Pagadas': ".manual_dropdown("mark_paid", array("0"=>"No", "1"=>"Si"), $config_wdb['mark_paid'])."\n";
		$output .= "			<input type=\"submit\" name=\"export\" value=\"Generar remesa\" onClick=\"return check_selectedinvoices()\">\n";
		$output .= "		</td>\n";
		$output .= "	</tr>\n";
		$output .= "</table>\n";
		$output .= "<input type=\"hidden\" name=\"stage\" value=\"1\">\n";
		$output .= "<input type=\"hidden\" name=\"payment_method\"  value=\"".$payment_method."\">\n";
		$output .= "<input type=\"hidden\" name=\"customfield_cc\"  value=\"".$customfield_cc."\">\n";
		$output .= "<input type=\"hidden\" name=\"customfield_nif\" value=\"".$customfield_nif."\">\n";
		$output .= "<input type=\"hidden\" name=\"invoice_status\"  value=\"".$invoice_status."\">\n";
		$output .= "</form>\n";
	}
	return $output;
}

# Contenido de la pestaña Historial
function generar_tab_historial() {
	global $tbl_remesas, $tbl_recibos, $tbl_facturas, $modulelink;
	$sql = "SELECT * FROM $tbl_remesas ORDER BY fecha DESC";
	$res = mysql_query($sql) or die("<pre>$sql</pre>ERROR: ".mysql_error());
	$output .= "<table class=\"datatable\" border='0' width=\"100%\" cellpadding=\"2\">\n";
	$output .= "	<tr>\n";
	$output .= "		<th>Remesa</th>\n";
	$output .= "		<th>Creación</th>\n";
	$output .= "		<th>Recibos</th>\n";
	$output .= "		<th>Importe</th>\n";
	$output .= "	</tr>\n";
	while($row = mysql_fetch_array($res)) {
		$fecha = date("d-M-Y H:i", strtotime($row['fecha']));
		$remesa_id = $row['id'];
		$remesa = $row['remesa'];
		$num_recibos = $row['num_recibos'];
		$importe = number_format($row['importe'], 2, ',', '')." &euro;";
		$output .= "	<tr>\n";
		$output .= "		<td><a href=\"#\" class=\"remesa\" id=\"remesa$remesa_id\">$remesa</a></td>\n";
		$output .= "		<td>$fecha</td>\n";
		$output .= "		<td>$num_recibos</td>\n";
		$output .= "		<td>$importe</td>\n";
		$output .= "	</tr>\n";
		$output .= "	<tr id=\"remesa".$remesa_id."row\" class=\"remesarow\">\n";
		$output .= "		<td colspan='4' style=\"padding-left:50px;\"><table width=\"100%\">\n";
		$output .= "			<tr><th>Cliente</th><th>Devolucion</th><th>Factura</th><th>Importe</th></tr>\n";
		$sql_facturas = "SELECT r.remesa, b.cod_devolucion, b.factura, i.total as importe, i.status as estado, 
										c.companyname, CONCAT(c.firstname,' ', c.lastname) cliente
										FROM mod_domiciliaciones_remesas r
										LEFT JOIN mod_domiciliaciones_recibos b ON (r.id = b.remesa_id)
										LEFT JOIN tblinvoices i ON (b.factura = i.id)
										LEFT JOIN tblclients c ON (i.userid = c.id)
										WHERE r.id=$remesa_id";
		$res_facturas = mysql_query($sql_facturas) or die("<pre>$sql_facturas</pre>ERROR: ".mysql_error());
		while($row_facturas = mysql_fetch_array($res_facturas)) {
			$cliente = $row_facturas['cliente'];
			$cod_devolucion = $row_facturas['cod_devolucion'];
			$factura = $row_facturas['factura'];
			$estado = $row_facturas['estado'];
			$importe = formato_moneda($row_facturas['importe']);
			$output .= "			<tr><td>$cliente</td><td>$cod_devolucion</td><td>$factura ($estado)</td><td>$importe</td></tr>\n";
		}
		$output .= "    </table></td>\n";
		$output .= "	</tr>\n";
	}	
	$output .= "</table>\n";
	return $output;
}

# Contenido de la pestaña Ajustes
function generar_tab_ajustes() {
	global $config_wdb;
	$output = '			<form method="post">
			<input type="hidden" name="configure" value="true">
			<input type="hidden" name="module" value="domiciliacion_bancaria">
			<table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
				<tr>
					<td class="fieldlabel">Nombre de la empresa</td>
					<td class="fieldarea"><input type="text" name="name" size="25" value="'. $config_wdb['name'] .'"></td>
					<td class="fieldlabel">Cuenta bancaria</td>
					<td class="fieldarea"><input type="text" name="cc" size="25" maxlength="20" value="'. $config_wdb['cc'] .'"></td>
				</tr>
				<tr>
					<td class="fieldlabel">NIF/CIF</td>
					<td class="fieldarea"><input type="text" name="nif" size="25" maxlength="9" value="'. $config_wdb['nif'] .'"></td>
					<td class="fieldlabel"></td>
					<td class="fieldarea"></td>
				</tr>
				<tr>
					<td class="fieldlabel">Sufijo Ordenante</td>
					<td class="fieldarea"><input type="text" name="sufijo" size="25" maxlength="3" value="'. $config_wdb['sufijo'] .'"></td>
					<td class="fieldlabel">Titular de la cuenta</td>
					<td class="fieldarea">
						'.sql_dropdown("customfield_tit", "tblcustomfields", "id", "fieldname", $config_wdb['customfield_tit'], $order_by="", array("n"=>"Nombre y Apellidos", "c"=>"Nombre de Empresa")).'
					  [<a href="configcustomfields.php" target=\"_blank\">crear</a>]
					</td>
				</tr>
				<tr>
					<td class="fieldlabel">Campo NIF/CIF clientes</td>
					<td class="fieldarea">
						'.sql_dropdown("customfield_nif", "tblcustomfields", "id", "fieldname", $config_wdb['customfield_nif']).'
					  [<a href="configcustomfields.php" target=\"_blank\">crear</a>]
					</td>
					<td class="fieldlabel">Campo CC clientes</td>
					<td class="fieldarea">
					  '.sql_dropdown("customfield_cc", "tblcustomfields", "id", "fieldname", $config_wdb['customfield_cc']).'
					  [<a href="configcustomfields.php" target=\"_blank\">crear</a>]
					</td>
				</tr>
				<tr>
					<td class="fieldlabel">Filtro por forma de pago</td>
					<td class="fieldarea">'.sql_dropdown("payment_method", "tblpaymentgateways", "", "gateway", $config_wdb['payment_method']).'</td>
					<td class="fieldlabel">Filtro por estado</td>
					<td class="fieldarea">'.manual_dropdown("invoice_status", array("Paid"=>"Pagadas", "Unpaid"=>"No pagadas"), $config_wdb['invoice_status']).'</td>
				</tr>
				<tr>
					<td class="fieldlabel">Salto de línea</td>
					<td class="fieldarea">'.manual_dropdown("line_feed", array("0"=>"Unix", "1"=>"DOS"), $config_wdb['line_feed']).'</td>
					<td class="fieldlabel">Extensión del fichero</td>
					<td class="fieldarea">'.manual_dropdown("file_ext", array("txt"=>"TXT", "dat"=>"DAT", "c19"=>"C19", "q19"=>"Q19"), $config_wdb['file_ext']).'</td>
				</tr>
				<tr>
					<td class="fieldlabel">Marcar facturas como pagadas</td>
					<td class="fieldarea">'.manual_dropdown("mark_paid", array("0"=>"No", "1"=>"Si"), $config_wdb['mark_paid']).'</td>
					<td class="fieldlabel">Modo Debug</td>
					<td class="fieldarea">'.manual_dropdown("debug", array("0"=>"Deshabilitado", "1"=>"En pantalla", "2"=>"En fichero"), $config_wdb['debug']).'</td>
				</tr>
			</table>			
			<img src="images/spacer.gif" height="8" width="1"><br>
			<div align="center"><input type="submit" value="Guardar ajustes" class="button"></div>
			</form>';
	return $output;
}

# Convertir cadena en formato de moneda
function formato_moneda($string) {
	$string = number_format($string, 2, ',', '') . "&euro;";
	return $string;
}

# Genera un desplegable con los datos de un array
function manual_dropdown($label, $data, $selected="") {
	$select = "<select name=\"$label\">\n";
	foreach($data as $value => $name) {
		$select .= "<option value=\"".$value."\"";
		if($value == $selected) $select .= " selected";
		$select .= ">".$name."</option>\n";
	}
	$select .= "</select>\n";	
	return $select;
}

# Genera un desplegable con los datos de una tabla SQL
function sql_dropdown($label, $table, $value="", $name, $selected="", $orderby="", $options=array()) {
	if(!$orderby) $orderby=$name;
	if($value) $sql = "SELECT DISTINCT $value as value, $name as name FROM $table ORDER BY $orderby";
	else       $sql = "SELECT DISTINCT $name as value, $name as name FROM $table ORDER BY $orderby";
	$res = mysql_query($sql);
	$select = "<select name=\"$label\">\n";
	if(count($options)) {
		foreach($options as $key => $value) {
			$select .= "<option value=\"".$key."\"";
			if($key == $selected) $select .= " selected";
			$select .= ">".$value."</option>\n";
		}
	}
	while($row = mysql_fetch_array($res)) {
		$select .= "<option value=\"".$row['value']."\"";
		if($row['value'] == $selected) $select .= " selected";
		$select .= ">".$row['name']."</option>\n";
	}
	$select .= "</select>\n";	
	return $select;
}

# Informacion de depuracion (si debug esta habilitado)
function debug($string) {
	global $config_wdb, $module_path, $module_name, $modulelink;

	// Si el debug está deshabilitado, salimos
	if(!$config_wdb['debug']) return;
	
	// Debug en pantalla
	if($config_wdb['debug']=="1") {
		echo "<pre>\n";
		if(is_array($string)) print_r($string);
		else echo $string;
		echo "</pre>\n";
	}

	// Debug en fichero
	if($config_wdb['debug']=="2") {
		$c19_logfile = "$module_path/".date("Ymd").".log";
		$fp_log = fopen($c19_logfile, "a");
		fputs($fp_log, "* LOG" . date("YmdHis") . "\n");
		fputs($fp_log, print_r($string, true));
		fputs($fp_log, "\n");
		fclose($fp_log);
	}
}

function generar_c19($selectedinvoices="") {
	global $config_wdb, $module_path, $module_name, $modulelink;

	// Salto de linea segun configuracion
	switch($config_wdb['line_feed']) {
		case 0: 	$EOL = "\n"; 						break;
		case 1: 	$EOL = chr(13).chr(10);	break;
		default: 	$EOL = "\n";						break;
	}
	
	$this_time = time();
	$c19_filename = $this_time . "." . $config_wdb['file_ext'];
	$c19_filepath = "$module_path/$c19_filename";
	if(!is_array($selectedinvoices) || count($selectedinvoices)<1) {
		$selectedinvoices = implode(",", $_POST['selectedinvoices']);
	}elseif(is_array($selectedinvoices) && count($selectedinvoices)) {
		$selectedinvoices = implode(",", $selectedinvoices);
	}
	
	## Generamos el listado de recibos
	$sql = "SELECT\n";
	$sql .= "	c.companyname\n";
	$sql .= "	, CONCAT(c.firstname,' ', c.lastname) cliente\n";
	if(is_numeric($config_wdb['customfield_tit'])) {
		$sql .= "	, (SELECT tit.value FROM tblcustomfieldsvalues tit WHERE tit.relid = c.id AND tit.fieldid=".$config_wdb['customfield_tit'].") titular\n";
	}
	$sql .= "	, c.id client_id\n";
	$sql .= "	, (SELECT n.value FROM tblcustomfieldsvalues n WHERE n.relid = c.id AND n.fieldid=".$config_wdb['customfield_nif'].") nif\n";
	$sql .= "	, i.id invoiceid\n";
	$sql .= "	, i.total importe\n";
	$sql .= "	, c.id\n";
	$sql .= "	, (SELECT b.value FROM tblcustomfieldsvalues b WHERE b.relid = c.id AND b.fieldid=".$config_wdb['customfield_cc'].") cuenta\n";
	$sql .= "	FROM tblinvoices i\n";
	$sql .= "		INNER JOIN tblclients c ON i.userid=c.id\n";
	$sql .= "	WHERE i.id IN (".$selectedinvoices.")\n";
	$sql .= "ORDER BY cuenta\n";
	
	debug($sql); 
	$res = mysql_query($sql) or die("SQL Error: " . mysql_error());

	# Inicializamos la clase C19
	include("c19.class.php");
	$c19 = new C19($config_wdb);

	## Cabecera de presentador
	$cabecera_presentador = $c19->cabecera_presentador();
	debug("[$cabecera_presentador]"); 
	
	## Cabecera de ordenante
	$cabecera_ordenante = $c19->cabecera_ordenante();
	debug("[$cabecera_ordenante]");

	## Individual obligatorio
	unset($historial);
	while($row = mysql_fetch_array($res)) {

		unset($data);
		// Defino el campo del titular de la cuenta
		if(empty($config_wdb['customfield_tit'])) $config_wdb['customfield_tit'] = 'n';
		switch($config_wdb['customfield_tit']) {
			case 'n': 	// Utilizo el nombre completo
				$data['nombre_cliente'] = $row['cliente'];		 
				break;
			case 'c': 	// Utilizo el nombre de la empresa
				$data['nombre_cliente'] = ($row['companyname']) ? $row['companyname'] : $row['cliente']; 
				break;
			default:	// Utilizo el custom field  
				$data['nombre_cliente'] = $row['titular'];     
				break;
		}

		$cod_devolucion = sprintf("%06s", substr($row['invoiceid'], -6));
		$ref_interna    = date("Ymd") . sprintf("%02d", ++$ri);
		$data['id_cliente']     = $row['id'];
		$data['entidad']        = $c19->ccparts($row['cuenta'], 1);
		$data['oficina']        = $c19->ccparts($row['cuenta'], 2);
		$data['dc']             = $c19->ccparts($row['cuenta'], 3);
		$data['cuenta']         = $c19->ccparts($row['cuenta'], 4);
		$data['invoiceid']     = $row['invoiceid'];
		$data['importe']        = number_format($row['importe'], 2, ',', '');
		$data['cod_devolucion'] = $cod_devolucion;
		$data['ref_interna']    = $ref_interna;
		$data['concepto']       = "FACTURA " . $data['invoiceid'];
		$individual_obligatorio .= $c19->individual_obligatorio($data).$EOL;
		$total_domiciliaciones++;
		$total_importes += str_replace(',', '.', $data['importe']);
		debug("Generando recibo para factura ".$data['invoiceid']." del cliente ".$data['nombre_cliente']);
		$row_output .= "		<tr>\n";
		$row_output .= "			<td>".$data['nombre_cliente']."</td>\n";
		$row_output .= "			<td>".$row['cuenta']."</td>\n";
		$row_output .= "			<td align=\"right\">".$data['importe']." &euro;</td>\n";
		$row_output .= "		</tr>\n";
		$historial[] = array($data['id_cliente'], $cod_devolucion, $ref_interna, $data['invoiceid'], $data['importe']);
	}
	
	## Total de ordenante
	unset($data);
	$data['importe'] = number_format($total_importes,2);
	$data['total_domiciliaciones'] = $total_domiciliaciones;
	$data['total_registros'] = $total_domiciliaciones+2;
	$total_ordenante = $c19->total_ordenante($data);
	
	## Total general
	unset($data);
	$data['total_importes'] = number_format($total_importes,2);
	$data['total_ordenantes'] = 1;
	$data['total_domiciliaciones'] = $total_domiciliaciones;
	$data['total_registros'] = $total_domiciliaciones+4;
	$total_general = $c19->total_general($data);

	## Borramos exportaciones anteriores
	if(is_array(glob("$module_path/*.".$config_wdb['file_ext']))) {
		foreach(glob("$module_path/*.".$config_wdb['file_ext']) as $filename) {
			unlink($filename);
		}
	}
  
	## Exportar datos
	debug("Abriendo $c19_filepath");
	$fp = fopen($c19_filepath, "w");
	fputs($fp, $cabecera_presentador.$EOL);
	fputs($fp, $cabecera_ordenante.$EOL);
	fputs($fp, $individual_obligatorio);
	fputs($fp, $total_ordenante.$EOL);
	fputs($fp, $total_general.$EOL);
	fclose($fp);
	
	## Almacenamos en el historial
	debug("Almacenando en el historial");
	guardar_historial($this_time, $historial);

	## Marcamos las facturas como pagadas
	$mark_paid = $_POST['mark_paid'];
	if($mark_paid) {
		debug("Creando transacciones");
		$num_recibos = count($historial);
		for($i=0; $i < $num_recibos; $i++) {
		  list($id_cliente, $cod_devolucion, $ref_interna, $invoiceid, $importe) = $historial[$i];
			$importe = str_replace(',', '.', $importe);
			$gatewaymodule = "domiciliacion";
			$fee = "0";
			debug("addInvoicePayment: $invoiceid, $this_time, $importe, $fee, $gatewaymodule");
			debug("resPayment1: $resPayment");
			$resPayment = addInvoicePayment($invoiceid, $this_time, $importe, $fee, $gatewaymodule);
			debug("resPayment2: $resPayment");
			debug("Transaccion creada: $this_time");
		}
		unset($id_cliente, $cod_devolucion, $ref_interna, $invoiceid, $importe);
	}
	
	## Mostramos por pantalla los datos generados
	echo "<table width=80% cellspacing=1 style=\"border: solid 1px #cccccc;\">\n";
	echo "	<tr bgcolor=\"#efefef\" style=\"text-align:center;\">\n";
	echo "		<th>Cliente</th>\n";
	echo "		<th>Cuenta corriente</th>\n";
	echo "		<th>Importe</th>\n";
	echo "	</tr>\n";
	echo $row_output;
	echo "</table>\n";
  
	## Importe acumulado
	echo "<br />\n";
	echo "<div style=\"width:80%; display: block; text-align:center\">\n";
	echo "Num. domiciliaciones: <strong>$total_domiciliaciones</strong>\n";
	echo "&nbsp;&nbsp;&nbsp;";
	echo "Total acumulado: <strong>".number_format($total_importes, 2, ',', '')." &euro;</strong>\n";
	echo "  -  ";

	## Enlace de descarga del archivo
	echo "<a href=\"$modulelink&download=$c19_filename\" target=\"_blank\"><u>Descargar C19</u></a><br />\n";
	echo "</div>\n";
}

# Valida un numero de cuenta corriente
function validate_cc($ccc="") {

	// Comprobar que el codigo no esta vacio
	$ccc = trim($ccc);
	if(!strlen($ccc))
		//return false;
		
	// Comprobar que el codigo es un numero
	$ccc = str_replace('.', '', $ccc);
	$ccc = str_replace(' ', '', $ccc);
	if(!is_numeric($ccc)) 
		return false;
		
	// Digito de control de la entidad y sucursal:
	$suma = 0;
	$suma += $ccc[0] * 4;
	$suma += $ccc[1] * 8;
	$suma += $ccc[2] * 5;
	$suma += $ccc[3] * 10;
	$suma += $ccc[4] * 9;
	$suma += $ccc[5] * 7;
	$suma += $ccc[6] * 3;
	$suma += $ccc[7] * 6;
	$division = floor($suma/11);
	$resto	= $suma - ($division  * 11);
	$primer_digito_control = 11 - $resto;
	if($primer_digito_control == 11) $primer_digito_control = 0;
	if($primer_digito_control == 10)$primer_digito_control = 1;
	if($primer_digito_control != $ccc[8])	return false;

	// Digito de control de la cuenta:
	$suma = 0;
	$suma += $ccc[10] * 1;
	$suma += $ccc[11] * 2;
	$suma += $ccc[12] * 4;
	$suma += $ccc[13] * 8;
	$suma += $ccc[14] * 5;
	$suma += $ccc[15] * 10;
	$suma += $ccc[16] * 9;
	$suma += $ccc[17] * 7;
	$suma += $ccc[18] * 3;
	$suma += $ccc[19] * 6;
	$division = floor($suma/11);
	$resto = $suma-($division  * 11);
	$segundo_digito_control = 11- $resto;
	if($segundo_digito_control == 11) $segundo_digito_control = 0;
	if($segundo_digito_control == 10) $segundo_digito_control = 1;
	if($segundo_digito_control != $ccc[9]) return false;

	return true;
}

?>