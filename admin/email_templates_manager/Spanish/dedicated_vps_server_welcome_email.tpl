#type=product
#name=Dedicated/VPS Server Welcome Email
#subject=Información del nuevo Servidor Dedicado
#lang=
<p>Estimado {$client_name},</p>
<p align="center"><strong>POR FAVOR, LEA DETENIDAMENTE TODO EL TEXTO Y GUARDE ESTE MENSAJE EN LUGAR SEGURO </strong></p>
<p>El servidor dedicado que nos ha solicitado se encuentra plenamente operativo y listo para su uso.</p>
<p><strong>Detalles del servidor<br /> </strong>=============================</p>
<p>{$service_product_name}</p>
<p>Direcci&oacute;n IP: {$service_dedicated_ip}<br /> Contrase&ntilde;a Root: {$service_password}</p>
<p>{if $service_assigned_ips}Asignaci&oacute;n de direcciones IP: <br /> {$service_assigned_ips}</p>
<p>{/if}Nombre del servidor: {$service_domain}</p>
<p><strong>Acceso Plesk<br /> </strong>=============================<br /> <a href="http://{$service_dedicated_ip}:8443/">http://{$service_dedicated_ip}:8443</a><br /> Usuario: admin<br />Contrase&ntilde;a: {$service_password}</p>
<p><strong>Servidores DNS</strong><br /> =============================<br /> Los servidores DNS que deber&aacute; usar para sus dominios son:<br />DNS primario: {$service_ns1}<br /> DNS secundario: {$service_ns2}</p>
<p><strong>Informaci&oacute;n de acceso por SSH<br /> </strong>=============================<br />Direcci&oacute;n IP: {$service_dedicated_ip}<br /> Contrase&ntilde;a Root: {$service_password}</p>
<p>Para el acceso SSH desde Windows puede utilizar el siguiente software:<br /> <a href="http://www.securitytools.net/mirrors/putty/">http://www.securitytools.net/mirrors/putty/</a></p>
<p><strong>Soporte</strong><br /> =============================<br />Para cualquier duda puede solicitar soporte desde {$whmcs_url}</p>
<p>Por favor, incluya toda la informaci&oacute;n necesaria para poder investigar la incidencia m&aacute;s r&aacute;pidamente, como por ejemplo la contrase&ntilde;a de root, el nombre del dominio y la descipci&oacute;n del problema o de la ayuda necesaria.</p>
<p>El manual de Plesk lo puede encontrar aqui: <a href="http://www.parallels.com/es/products/plesk/resources/">http://www.parallels.com/es/products/plesk/resources/</a> <a href="http://www.cpanel.net/docs/whm/index.html"><br /></a></p>
<p>Gracias por confiar en nosotros.</p>
<p>{$signature}</p>