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
 * @package    c19.class.php
 * @author     Eduardo Gonzalez <egonzalez@cyberpymes.com>
 * @copyright  2010 CyberPymes
 * @version    1.2.1
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/

class C19 {

	var $empty    = "";
	var $name     = "";
	var $nif      = "";
	var $entidad  = "";
	var $oficina  = "";
	var $dc       = "";
	var $cuenta   = "";
	var $sufijo   = "";
	var $concepto = "";
	
	// Constructor for php4
	function C19($config_wdb) {
		$this->name     = $this->string_format($config_wdb['name']);
		$this->nif      = $config_wdb['nif'];
		$this->entidad  = $this->ccparts($config_wdb['cc'], 1);
		$this->oficina  = $this->ccparts($config_wdb['cc'], 2);
		$this->dc       = $this->ccparts($config_wdb['cc'], 3);
		$this->cuenta   = $this->ccparts($config_wdb['cc'], 4);
		$this->sufijo   = $this->string_format($config_wdb['sufijo']);
		$this->concepto = $this->string_format($config_wdb['concepto']);
	}

	// Constructor for php5
	function __construct($config_wdb) {
		$this->name     = $this->string_format($config_wdb['name']);
		$this->nif      = $config_wdb['nif'];
		$this->entidad  = $this->ccparts($config_wdb['cc'], 1);
		$this->oficina  = $this->ccparts($config_wdb['cc'], 2);
		$this->dc       = $this->ccparts($config_wdb['cc'], 3);
		$this->cuenta   = $this->ccparts($config_wdb['cc'], 4);
		$this->sufijo   = $this->string_format($config_wdb['sufijo']);
		$this->concepto = $this->string_format($config_wdb['concepto']);
	}
	
	function cabecera_presentador() {
		$z['a1'] = "51";
		$z['a2'] = "80";
		$z['b1'] = sprintf("%09s", $this->nif).sprintf("%03s", $this->sufijo);
		$z['b2'] = date("dmy");
		$z['b3'] = sprintf("%6s", $this->empty);
		$z['c']  = $this->format($this->name, 40, 'l', ' ');
		$z['d']  = sprintf("%20s", $this->empty);
		$z['e1'] = sprintf("%04s", $this->entidad);
		$z['e2'] = sprintf("%04s", $this->oficina);
		$z['e3'] = sprintf("%12s", $this->empty);
		$z['f']  = sprintf("%40s", $this->empty);
		$z['g']  = sprintf("%14s", $this->empty);

		$cabecera_presentador = implode("", $z);
		return $cabecera_presentador;
		
	}
	
	function cabecera_ordenante() {
		$z['a1'] = "53";
		$z['a2'] = "80";
		$z['b1'] = sprintf("%09s", $this->nif).sprintf("%03s", $this->sufijo);
		$z['b2'] = date("dmy");
		$z['b3'] = date("dmy", mktime(0, 0, 0, date("m"), date("d")+1, date("y"))); // Fecha de mañana
		$z['c']  = $this->format($this->name, 40, 'l', ' ');
		$z['d1'] = sprintf("%04s",  $this->entidad);
		$z['d2'] = sprintf("%04s",  $this->oficina);
		$z['d3'] = sprintf("%02s",  $this->dc);
		$z['d4'] = sprintf("%010s", $this->cuenta);
		$z['e1'] = sprintf("%8s", $this->empty);
		$z['e2'] = "01";
		$z['e3'] = sprintf("%10s", $this->empty);
		$z['f']  = sprintf("%40s", $this->empty);
		$z['g']  = sprintf("%14s", $this->empty);

		$cabecera_ordenante = implode("", $z);
		return $cabecera_ordenante;
	}
	
	function individual_obligatorio($data) {
		$z['a1'] = "56";
		$z['a2'] = "80";
		$z['b1'] = sprintf("%09s", $this->nif).sprintf("%03s", $this->sufijo);
		$z['b2'] = "CPC".sprintf("%09s", $data['id_cliente']);
		$z['c']  = $this->format($data['nombre_cliente'], 40, 'l', ' ');
		$z['d1'] = sprintf("%04s", $data['entidad']);
		$z['d2'] = sprintf("%04s", $data['oficina']);
		$z['d3'] = sprintf("%02s", $data['dc']);
		$z['d4'] = sprintf("%010s", $data['cuenta']);
		$z['e']  = sprintf("%010s", str_replace(',', '', $data['importe']));
		$z['f1'] = sprintf("%06s", $data['cod_devolucion']);
		$z['f2'] = sprintf("%010s", $data['ref_interna']);
		$importe = number_format(str_replace(",", ".", $data['importe']), 2, ',', '.');
		$z['g']  = sprintf("%-40s", $data['concepto']);
		$z['h']  = sprintf("%8s", $this->empty);
		
		$individual_obligatorio = implode("", $z);
		return $individual_obligatorio;
	}

	function total_ordenante($data) {
		$z['a1'] = "58";
		$z['a2'] = "80";
		$z['b1'] = sprintf("%09s", $this->nif).sprintf("%03s", $this->sufijo);
		$z['b2'] = sprintf("%12s", $this->empty);
		$z['c']  = sprintf("%40s", $this->empty);
		$z['d']  = sprintf("%20s", $this->empty);
		$z['e1'] = sprintf("%010s", str_replace(array(",", "."), '', $data['importe']));
		$z['e2'] = sprintf("%6s", $this->empty);
		$z['f1'] = sprintf("%010s", $data['total_domiciliaciones']);
		$z['f2'] = sprintf("%010s", $data['total_registros']);
		$z['f3'] = sprintf("%20s", $this->empty);
		$z['g']  = sprintf("%18s", $this->empty);
		
		$total_ordenante = implode("", $z);
		return $total_ordenante;
	}
	
	function total_general($data) {
		$z['a1'] = "59";
		$z['a2'] = "80";
		$z['b1'] = sprintf("%09s", $this->nif).sprintf("%03s", $this->sufijo);
		$z['b2'] = sprintf("%12s", $this->empty);
		$z['c']  = sprintf("%40s", $this->empty);
		$z['b2'] = sprintf("%12s", $this->empty);
		$z['c']  = sprintf("%40s", $this->empty);
		$z['d1'] = sprintf("%04s", $data['total_ordenantes']);
		$z['d2'] = sprintf("%16s", $this->empty);
		$z['e1'] = sprintf("%010s", str_replace(array(",", "."), '', $data['total_importes']));
		$z['e2'] = sprintf("%6s", $this->empty);
		$z['f1'] = sprintf("%010s", $data['total_domiciliaciones']);
		$z['f2'] = sprintf("%010s", $data['total_registros']);
		$z['f3'] = sprintf("%20s", $this->empty);
		$z['g']  = sprintf("%18s", $this->empty);

		$total_general = implode("", $z);
		return $total_general;
	}
	
	function ccparts($string, $op="") {

		// Remove all non-numbers
		$string = ereg_replace("[^0-9]", "", $string);

		// Define separator string
		$sep = ".";

		// Extract each part from the string
		$entidad = substr($string,0,4);
		$oficina = substr($string,4,4);
		$control = substr($string,8,2);
		$cuenta  = substr($string,10,10);

		switch($op) {
			// Return just one part
			case "1": return $entidad; break;
			case "2": return $oficina; break;
			case "3": return $control; break;
			case "4": return $cuenta;	 break;
			// Or return the whole string
			default:
				return $entidad.$sep.$oficina.$sep.$control.$sep.$cuenta;
				break;
		}
	}

	function string_format($data) {
		$replace_from = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
		$replace_to   = array('a', 'e', 'i', 'o', 'u', 'n');
		$data = str_replace($replace_from, $replace_to, $data);
		$data = strtoupper(strtolower($data));
		return($data);
	}

	function format($string, $length, $position="r", $separator=" ") {
		$string = $this->clean_string($string);
		if(!$separator) $separator = " ";
		if(strlen($string)>$length) {
			$string = substr($string, 0, $length);
		} else {
			switch($position) {
				case "l": $position = '-'; break;
				default:  $position = '';  break;
			}
			$format = "%'".$separator.$position.$length."s";
			$string = sprintf($format, $string);
		}
		return $string;
	}

	function clean_string($data) {
		$data = trim($data);
		$replace_from = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
		$replace_to   = array('a', 'e', 'i', 'o', 'u', 'n');
		$data = str_replace($replace_from, $replace_to, $data);
		$data = strtoupper(strtolower($data));
		return($data);
	}
}


?>
 