<?php
/**
 * Email Template Manager for WHMCS
 *
 * Este módulo ofrece la posibilidad de exportar e importar plantillas
 * de correos electrónicos, lo que ayuda con el proceso de traducción
 * y de mantenimiento para las traducciones
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
 * @version    1.2
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 3
 *
**/


if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

$module_name = "email_templates_manager";
$module_path = dirname(__FILE__);
$debug = 0;

if($_GET['do_export']) {
	$language = $_GET['language'];
	$lang_value = ($language=="Default") ? "" : $language;
	echo "Exporting $language language<br />\n";
	
	$sql = "SELECT type, name, subject, message FROM tblemailtemplates WHERE language='$lang_value'";
	$res = mysql_query($sql);
	$num = mysql_num_rows($res);
	
	if($num) {
		# Create dir if not exists
		if(!file_exists("$module_path/$language")) mkdir("$module_path/$language");
		while($row = mysql_fetch_array($res)) {
			echo " - Dumping ".$row['name']."\n";

			$tpl_type    = "#type=".$row['type']."\n";
			$tpl_name    = "#name=".$row['name']."\n";
			$tpl_subject = "#subject=".$row['subject']."\n";
			$tpl_lang    = "#lang=".$row['language']."\n";
			$tpl_message = $row['message'];

			$filename = str_replace(' ', '_', $row['name']);
			$filename = str_replace('/', '_', $filename);
			$filename = strtolower($filename);
			$filename .= ".tpl";
			
			# Try to open outout file
			$fp = @fopen("$module_path/$language/$filename", "w");
			if($fp) {
				fwrite($fp, $tpl_type); 
				fwrite($fp, $tpl_name); 
				fwrite($fp, $tpl_subject); 
				fwrite($fp, $tpl_lang); 
				fwrite($fp, $tpl_message); 
				fclose($fp);
				echo "&nbsp;&nbsp;&nbsp;<span style=\"color: green\">Done!</span><br />\n";
			} else {
				echo "&nbsp;&nbsp;&nbsp;<span style=\"color: red\">Error</span><br />\n";
			}

		}

	echo "<br />\n";
	echo "All templates have been dumped to filesystem! \n";
	echo "<a href=\"$_SELF?module=$module_name\">Back to previous page</a>\n";
	}

} elseif($_GET['do_import']) {
	echo "<p>Loading email templates for selected language into database . . .</p>";

	# Load template files list
	$language = $_GET['language'];
	$current_directory = $module_path."/".$language;
	
	# Read current directory
	if ($handle = opendir($current_directory)) {

		# For each template
    while (false !== ($filename = readdir($handle))) {
        # Read file details
        $path_info = pathinfo(dirname(_FILE_)."/".$filename);
        # Exclude non-tpl extensions
        if($path_info['extension']=="tpl") {
        	# Array with all existing templates
        	$templates[] = $language."/".$filename;
        }
    }
    closedir($handle);
	}
	
	# For each template in array
	foreach($templates as $template) {

		# Clear previous message
		unset($message);

		# Set import source
		$tpl_filename = $module_path."/".$template;

		# Some onscreen info
		echo "Loading $template\n";

		$fp = fopen($tpl_filename, "r");
		while(!feof($fp)) {
			unset($translation);
			$line = fgets($fp);
			if(substr($line,0,1)=='#') {
				$separator = strpos($line,'=');
				$key = substr($line,1,$separator-1);
				$value = substr($line,$separator+1, strlen($line));
				$$key = trim($value);
			} else {
				$message .= $line;
			}
		}
		fclose($fp);

	  # Detete current template
		$sql_delete  = "DELETE FROM tblemailtemplates ";
		$sql_delete .= "WHERE name='$name' AND language='Spanish'";
		debug("SQL: $sql_delete");
		$res_delete = mysql_query($sql_delete);
		debug("Delete: $res_delete, affected rows: ".mysql_affected_rows());
			
		# Insert translated message
		$message = trim($message);
		//$row = array("type"=>$type,"name"=>$name,"subject"=>$subject,"language"=>$language,"message"=>$message);
		$sql_insert  = "INSERT INTO tblemailtemplates (type, name, subject, language, message) ";
		$sql_insert .= "VALUES ('$type', '$name', '$subject', '$language', '$message')";
		debug("SQL: $sql_insert");
		$res_insert = mysql_query($sql_insert);
		debug("Insert: $res_insert, affected rows: ".mysql_affected_rows());

		if($res_insert) 
			echo "&nbsp;&nbsp;&nbsp;<span style=\"color: green\">Done!</span><br />\n";
		else 
			echo "&nbsp;&nbsp;&nbsp;<span style=\"color: red\">Error</span><br />\n";
	}
	echo "<br />\n";
	echo "All templates have been loaded into database! \n";
	echo "<a href=\"$_SELF?module=$module_name\">Back to previous page</a>\n";


} else {

	echo "<p>\n";
	echo "	This module manages import/export email templates for selected language.<br />";
	echo "	Note that language must be already created from <a href=\"configemailtemplates.php\" target=\"_blank\">Setup > Email Templates</a>.\n";
	echo "</p>";

	echo "<h3>Import languages</h3>\n";
	if ($handle = opendir($module_path)) {

    while (false !== ($dirname = readdir($handle))) {
			if($dirname != "." && $dirname != ".." && is_dir("$module_path/$dirname"))
				$languages[] = $dirname;
    }
    closedir($handle);
	}
	
	if(count($languages)) {
		echo "<form method=\"get\">\n";
		echo "Select language from filesystem: ";
		echo "<select name=\"language\">\n";
		foreach($languages as $language) {
			echo "<option value=\"$language\">$language</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"submit\" name=\"do_import\" value=\"Import\">\n";
		echo "<input type=\"hidden\" name=\"module\" value=\"$module_name\">\n";
		echo "</form>\n";
	} else {
		echo "No languages found at $module_path.<br />Please, export or download a language first.<br />\n";
	}

	echo "<h3>Export languages</h3>\n";
	$sql = "SELECT DISTINCT language FROM tblemailtemplates ORDER BY language";
	$res = mysql_query($sql);

	echo "<form method=\"get\">\n";
	echo "Select language from database: ";
	echo "<select name=\"language\">\n";
	while($row = mysql_fetch_array($res)) {
		$language = $row['language']; if($language=="") $language="Default";
		echo "<option value=\"$language\">$language</option>\n";
	}
	echo "</select>\n";
	echo "<input type=\"submit\" name=\"do_export\" value=\"export\">\n";
	echo "<input type=\"hidden\" name=\"module\" value=\"$module_name\">\n";
	echo "</form>\n";

}

echo "<br /><br />";

function debug($string) {
	global $debug;
	if(!$debug) return;
	echo "<pre>\n";
	if(is_array($string)) print_r($string);
	else echo $string;
}
	
?>