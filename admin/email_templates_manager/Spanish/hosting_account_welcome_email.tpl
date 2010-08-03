#type=product
#name=Hosting Account Welcome Email
#subject=Información de nueva cuenta
#lang=
<p>Estimado {$client_name},</p>
<p align="center"><strong>POR FAVOR, LEA DETENIDAMENTE TODO EL TEXTO Y GUARDE ESTE EMAIL EN LUGAR SEGURO </strong></p>
<p>&iexcl;Gracias por su pedido! Hemos dado de alta correctamente su cuenta de hospedaje y en este email encontrar&aacute; toda la informaci&oacute;n necesaria para comenzar a usar el servicio.</p>
<p>Nota: si ha solicitado un nombre de dominio durante el proceso, tenga en cuenta que el dominio no estar&aacute; disponible de manera instant&aacute;nea en Internet. Existe un per&iacute;odo de propagaci&oacute;n que puede durar hasta 48 horas. Hasta entonces no funcionar&aacute;n su sitio web y su correo electr&oacute;nico, aunque nosotros le proporcionamos una direcci&oacute;n temporal para que pueda subir y ver su sitio web durante la propagaci&oacute;n</p>
<p><strong>Informaci&oacute;n de la cuenta<br /> </strong></p>
<p>Plan de hospedaje: {$service_product_name}<br />Dominio: {$service_domain}<br />Precio del primer pago: {$service_first_payment_amount}<br />Precio recurrente: {$service_recurring_amount}<br />Ciclo de facturaci&oacute;n: {$service_billing_cycle}<br />Fecha de renovaci&oacute;n: {$service_next_due_date}</p>
<p><strong>Datos de acceso</strong></p>
<p>Usuario: {$service_username}<br />Contrase&ntilde;a: {$service_password}</p>
<p>Panel de administraci&oacute;n temporal: <a href="http://{$service_server_ip}:8443/">http://{$service_server_ip}:8443/</a><br />Una vez que el dominio se ha propagado puede acceder desde <a href="http://www.{$service_domain}:8443/">http://www.{$service_domain}:8443/</a></p>
<p><strong>Server Information</strong></p>
<p>Nombre del servidor: {$service_server_name}<br />Direcci&oacute;n IP del servidor: {$service_server_ip}</p>
<p>Si va a usar un dominio ya existente con su nueva cuenta de hosting, ser&aacute; necesario que actualice los servidores de nombre del dominio para usar los que se muestran a continuaci&oacute;n</p>
<p>Nameserver 1: {$service_ns1} ({$service_ns1_ip})<br />Nameserver 2: {$service_ns2} ({$service_ns2_ip}){if $service_ns3}<br />Nameserver 3: {$service_ns3} ({$service_ns3_ip}){/if}{if $service_ns4}<br />Nameserver 4: {$service_ns4} ({$service_ns4_ip}){/if}</p>
<p><strong>Publicar su sitio web</strong></p>
<p>Temporalmente Puede utilizar una de las siguientes direcciones para gestionar su sitio web:</p>
<p>Servidor FTP temporal: {$service_server_ip}<br />Direcci&oacute;n web temporal: <a href="https://{$service_server_ip}:8443/sitepreview/http/{$service_domain}/">https://{$service_server_ip}:8443/sitepreview/http/{$</a><a href="http://www.{$service_domain}">service_domain</a><a href="http://{$service_server_ip}/~{$service_username}/">}/</a></p>
<p>Y una vez que el dominio se haya propagado podr&aacute; usar las siguientes direcciones.</p>
<p>FTP Hostname: {$service_domain}<br />Webpage URL: <a href="http://www.{$service_domain}">http://www.{$service_domain}</a></p>
<p><strong>Configuraci&oacute;n del correo<br /></strong></p>
<p>Para cada cuenta de correo creada utilice los datos que se muestran a continuaci&oacute;n dentro de su cliente de correo:</p>
<p>Servidor POP3: mail.{$service_domain}<br />Servidor SMTP: mail.{$service_domain}<br />Usuario: la direcci&oacute;n de correo que est&aacute; configurando<br />Contrase&ntilde;a: la contrase&ntilde;a establecida desde el panel de control</p>
<p>Gracias por confiar en nosotros.</p>
<p>{$signature}</p>