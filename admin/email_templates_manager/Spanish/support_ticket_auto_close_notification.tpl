#type=support
#name=Support Ticket Auto Close Notification
#subject=Ticket de soporte resuelto
#lang=
<p>Estimado {$client_name},</p>
<p>Esta es una notificaci&oacute;n para informarle de que el ticket #{$ticket_id} ha sido cerrado dado que no hemos recibido ninguna respuesta en {$ticket_auto_close_time} horas.</p>
<p>Asunto: {$ticket_subject}<br />Departamento: {$ticket_department}<br />Prioridad: {$ticket_priority}<br />Estado: {$ticket_status}</p>
<p>Si a&uacute;n tiene alguna duda o comentario, por favor, responda a este email para reabrir el ticket de soporte.</p>
<p>{$signature}</p>