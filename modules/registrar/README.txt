******************************
**  WHMCS Domain Registrar  **
******************************

1. INTRODUCCION

Este módulo permite hacer registros y traslados de dominios usando un WHMCS como agente registrador a través del API
Esto lo que hace es generar un nuevo pedido en el WHMCS de nuestro proveedor, asociado con nuestro ID de usuario en WHMCS
Así, un proveedor con WHMCS permite que su cliente con WHMCS permita el registro de dominios al usuario final.

2. INSTALACION

 - Descomprimir el archivo "whmcs_domain_registrar.zip" en la raiz del WHMCS del cliente
 - No hace falta instalar nada en el WHMCS del proveedor

3. FUNCIONAMIENTO

Para el funcionamiento del módulo, el proveedor de WHMCS ofrece a su cliente este módulo de registrador.
A través del módulo, el cliente será capaz de conectar con la API de WHMCS del proveedor y crear un pedido.
Para ello, el cliente debe tener cuenta de cliente en el WHMCS de su proveedor para asociar los dominios a ella.
Además, el cliente deberá tener cuenta de administrador en el WHMCS de su proveedor para acceder a la API.

4. CONFIGURACION

4.a) Crear un "Role Group" en el WHMCS del proveedor
Para ello entramos en "Setup" -> "Administrator Roles" y pulsamos en "Add New Role Group"
Por ejemplo podemos crear un grupo llamado "API Users"
Pulsamos en el botón "Continue" y en la siguiente página marcamos sólo la casilla "API Access"
Pulsamos en el botón "Save Changes"

4.b) Crear un usuario de API en el WHMCS del proveedor
Para ello entramos en "Setup" -> "Administrators" y pulsamos en "Add New Administrator"
En "Para ello entramos en "Setup" -> "Administrator Roles" y pulsamos en "Add New Role Group"
En "Administrator Role" seleccionamos el grupo creado en el paso anterior
Rellenamos los campos "Email", "Username", "Password" y "Confirm Password"

4.c) Configuramos el módulo de registrador en el WHMCS del cliente
Accedemos a "Setup" -> "Domain Registrars"
En el desplegable seleccionamos el registrador "Whmcs" y pulsamos "Go"
En el campo "APIUser" introducimos el nombre del usuario administrador creado en el WHMCS del proveedor.
En el campo "APIKey" introducimos la contraseña del usuario administrador creado en el WHMCS del proveedor.
En el campo "URL" introducimos la dirección web del API del WHMCS del proveedor (http://www.example.com/whmcs/includes/api.php).
En los campos "UserEmail" y "UserPassword" introducimos el email y contraseña de usuario creado en el WHMCS del proveedor.

5. CREDITOS

Este módulo ha sido creado por:
Eduardo Gonzalez
egonzalez@cyberpymes.com
www.CyberPymes.com
