#type=product
#name=Reseller Account Welcome Email
#subject=Información de Cuenta Reseller
#lang=
<p align="center"><strong>POR FAVOR, LEA DETENIDAMENTE TODO EL TEXTO Y GUARDE ESTE MENSAJE EN LUGAR SEGURO </strong></p>
<p>Nota: si ha solicitado un nombre de dominio durante el proceso, tenga en cuenta que el dominio no estar&aacute; disponible de manera instant&aacute;nea en Internet. Existe un per&iacute;odo de propagaci&oacute;n que puede durar hasta 48 horas. Hasta entonces no funcionar&aacute;n su sitio web y su correo electr&oacute;nico, aunque nosotros le proporcionamos una direcci&oacute;n temporal para que pueda subir y ver su sitio web durante la propagaci&oacute;n</p>
<p>Estimado {$client_name},</p>
<p>La cuenta reseller para {$service_domain} ha sido creada correctamente. A continuaci&oacute;n encontrar&aacute; el usuario y la contrase&ntilde;a necearios para acceder a su panel de control Plesk</p>
<p><strong>Informaci&oacute;n de la cuenta</strong><strong></strong></p>
<p>Dominio: {$service_domain}<br /> Usuario: {$service_username}<br /> Contrase&ntilde;a: {$service_password}<br /> Plan de hospedaje: {$service_product_name}</p>
<p>Panel de control: <a href="http://{$service_server_ip}:8443/">http://{$service_server_ip}:8443/</a></p>
<p>-------------------------------------------------------------------------------------------- <br /> <strong>Panel de control - Primeros pasos</strong><br /> -------------------------------------------------------------------------------------------- <br /> <br />Lo primero que debe hacer es acceder al panel de control Plesk desde la siguiente direcci&oacute;n:<br /> <br /> <a href="http://{$service_server_ip}:8443/">http://{$service_server_ip}:8443/</a><br /> <br />Es necesario usar el <strong>http://</strong> en la direcci&oacute;n para poder conectar con el puerto :8443<br />Utilice su usuario y contrase&ntilde;a para acceder al panel de control.<br /> <br /> <em><strong>Para crear un nuevo dominio</strong></em><strong><em> <br /> </em></strong><br />Para poder hospedar un dominio dentro de su cuenta reseller es necesario dar de alta el dominio en el panel de control Plesk para crear todos los recursos necesarios del dominio.</p>
<p>1. Haga clic en <em>Dominios</em><br /> 2. Haga clic en <em>Crear dominio</em><br /> 3. Rellene los datos del dominio y el acceso FTP<br /> 4. Pulse en el bot&oacute;n <em>Finalizar</em><br /> <br />Con estos pasos, el dominio estar&aacute; listo para ser hospedado en su cuenta reseller.</p>
<p><em><strong>Para crear cuentas de correo</strong></em><strong><em> en un dominio<br /> </em></strong><br />Para poder usar el correo asociado con un dominio es neceario crear antes las cuentas de correo que se vayan a utilizar.</p>
<p>1. Haga clic en <em>Dominios</em><br /> 2. Haga clic en el nombre del dominio seleccionado<br /> 3. Haga clic en el icono <em>Cuentas de correo</em><br /> 4. Haga clic en el icono <em>Crear cuenta de correo</em><br /> 5. Rellene el nombre de la cuenta e introduzca la contrase&ntilde;a<br /> 6. Pulse en el bot&oacute;n <em>Finalizar</em></p>
<p><em><strong>Para cambiar la contrase&ntilde;a FTP de un dominio</strong></em><strong><em><br /> </em></strong><br />En ocasiones puede ser neceario cambiar la contrase&ntilde;a de la cuenta FTP de un dominio.</p>
<p>1. Haga clic en <em>Dominios</em><br /> 2. Haga clic en el nombre del dominio seleccionado<br /> 3. Haga clic en el icono <em>Ajustes de alojamiento web</em><br /> 4. Baje hasta la secci&oacute;n <em>Preferencias de la cuenta</em><br /> 5. Introduzca y confirme la nueva contrase&ntilde;a<br /> 6. Pulse en el bot&oacute;n <em>Aceptar</em> al final de la p&aacute;gina</p>
<p>Gracias por confiar en nosotros.</p>
<p>{$signature}</p>