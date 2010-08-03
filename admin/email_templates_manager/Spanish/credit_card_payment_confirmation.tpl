#type=invoice
#name=Credit Card Payment Confirmation
#subject=Confirmación de pago por tarjeta
#lang=
<p>Estimado {$client_name},</p>
<p>Hemos recibido el pago de la factura {$invoice_num} enviada con fecha {$invoice_date_created}</p>
<p>{$invoice_html_contents}</p>
<p>Total a pagar: {$invoice_last_payment_amount}<br />Id de transacci&oacute;n: {$invoice_last_payment_transid}<br />Total abonado: {$invoice_amount_paid}<br />Pendiente de pago: {$invoice_balance}<br />Estado: {$invoice_status}</p>
<p>Puede revisar su historial de facturaci&oacute;n siempre que lo desee desde su &aacute;rea de clientes.</p>
<p>Nota: este email sirve como justificante oficial del pago.</p>
<p>{$signature}</p>