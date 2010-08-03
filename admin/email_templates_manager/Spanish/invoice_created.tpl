#type=invoice
#name=Invoice Created
#subject=Nueva factura generada
#lang=
<p>Estimado {$client_name},</p>
<p>Este es un mensaje para avisarle de que se ha generado una nueva factura con fecha {$invoice_date_created}.</p>
<p>Su forma de pago es: {$invoice_payment_method}</p>
<p>N&ordm; de Factura #{$invoice_num}<br />Total a pagar: {$invoice_total}<br /> Fecha de vencimiento: {$invoice_date_due}</p>
<p><strong>Elementos de la factura</strong></p>
<p>{$invoice_html_contents} <br /> ------------------------------------------------------</p>
<p>Puede acceder al &aacute;rea de clientes para ver y abonar la factura desde {$invoice_link}</p>
<p>{$signature}</p>