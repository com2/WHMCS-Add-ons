#type=invoice
#name=Credit Card Payment Failed
#subject=Error en el pago con tarjeta
#lang=
<p>Estimado {$client_name},</p>
<p>Le informamos de que el cargo realizado en su tarjeta de cr&eacute;dito ha sido rechazado.</p>
<p>Fecha de factura: {$invoice_date_created}<br />N&ordm; de factura: {$invoice_num}<br />Total a pagar: {$invoice_total}<br />Estado: {$invoice_status}</p>
<p>Es necesario que acceda a su &aacute;rea de cliente para realizar el abono de la factura de forma manual. Durante el proceso de pago tendr&aacute; la posibilidad de cambiar los datos de la tarjeta de cr&eacute;dito registrada en nuestros sistemas.<br /> {$invoice_link}</p>
<p>{$signature}</p>