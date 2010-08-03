#type=invoice
#name=First Invoice Overdue Notice
#subject=Abono de factura fuera de plazo
#lang=
<p>Estimado {$client_name},</p>
<p>Este es un aviso para recordarle que el abono de la factura n&uacute;mero {$invoice_num}, que fue generada con fecha {$invoice_date_created}, est&aacute; fuera de plazo.</p>
<p>Su forma de pago es: {$invoice_payment_method}</p>
<p>Factura: {$invoice_num}<br /> Total a pagar: {$invoice_total}<br /> Fecha de vencimiento: {$invoice_date_due}</p>
<p>Puede acceder a su &aacute;rea de cliente para ver y abonar esta factura desde {$invoice_link}</p>
<p>Sus datos de acceso son los siguientes:</p>
<p>Correo electr&oacute;nico: {$client_email}<br /> Contrase&ntilde;a: {$client_password}</p>
<p>{$signature}</p>