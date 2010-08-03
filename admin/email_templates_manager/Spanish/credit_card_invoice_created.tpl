#type=invoice
#name=Credit Card Invoice Created
#subject=Nueva factura generada
#lang=
<p>Estimado {$client_name},</p>
<p>Le informamos de que se ha generado una nueva factura con fecha {$invoice_date_created}.</p>
<p>Su forma de pago es: {$invoice_payment_method}</p>
<p>N&ordm; de factura: {$invoice_num}<br />Total a pagar: {$invoice_total}<br /> Fecha de vencimiento: {$invoice_date_due}</p>
<p><strong>Conceptos facturados</strong></p>
<p>{$invoice_html_contents} <br /> ------------------------------------------------------</p>
<p>Payment will be taken automatically on {$invoice_date_due} from your credit card on record with us. To update or change the credit card details we hold for your account please login at {$invoice_link} and click Pay Now then following the instructions on screen.</p>
<p>{$signature}</p>