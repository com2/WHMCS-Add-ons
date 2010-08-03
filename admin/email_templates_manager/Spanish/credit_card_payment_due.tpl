#type=invoice
#name=Credit Card Payment Due
#subject=Recordatorio de pago de factura
#lang=
<p>Estimado {$client_name},</p>
<p>Le informamos de que existe una factura a su nombre con fecha de vencimiento {$invoice_date_due}. Hemos tratado de hacer el cobro autom&aacute;tico pero no ha sido posible porque no tenemos los datos de su tarjeta de cr&eacute;dito.</p>
<p>Fecha de factura: {$invoice_date_created}<br />N&ordm; de factura: {$invoice_num}<br />Total a pagar: {$invoice_total}<br />Fecha de vencimiento: {$invoice_date_due}</p>
<p>Por favor, acceda a su cuenta de cliente a trav&eacute;s del enlace que se muestra a continuaci&oacute;n para rellenar los datos de la tarjeta de cr&eacute;dito o para realizar el abno utilizando otra forma de pago disponible.</p>
<p>{$invoice_link}</p>
<p>{$signature}</p>