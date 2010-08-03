#type=invoice
#name=Invoice Payment Reminder
#subject=Recordatorio de abono de factura
#lang=
<p>Estimado {$client_name},</p>
<p>Este mensaje es para recordarle que la factura n&uacute;mero {$invoice_num}, que fue generada con fecha {$invoice_date_created}, venci&oacute; el {$invoice_date_due}.</p>
<p>Su forma de pago es: {$invoice_payment_method}</p>
<p>Factura: {$invoice_num}<br /> Total a pagar: {$invoice_total}<br /> Fecha de vencimiento: {$invoice_date_due}</p>
<p>Puede acceder a su &aacute;rea de cliente para ver y abonar esta factura desde {$invoice_link}</p>
<p>{$signature}</p>