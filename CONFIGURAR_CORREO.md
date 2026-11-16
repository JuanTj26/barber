# Configurar notificaciones por correo

El proyecto usa PHPMailer con Gmail SMTP. No se envían correos hasta configurar una contraseña de aplicación.

1. En la cuenta `angelmellodev@gmail.com`, activa la verificación en dos pasos.
2. En la configuración de seguridad de Google, crea una contraseña de aplicación para este proyecto. Google puede ocultar esa opción en cuentas administradas por una escuela o trabajo.
3. Abre `api/mail_config.local.php` y reemplaza `PEGA_AQUI_LA_CONTRASENA_DE_APLICACION` por la contraseña generada. Puedes pegarla con espacios; el código los elimina al conectarse.
4. Guarda el archivo y realiza una reservación con un correo válido para probar. También se manda aviso cuando cambia el horario, se cancela la cita o el administrador cambia su estado.
5. Desde el panel, en **Notificaciones por correo**, puedes ejecutar manualmente los recordatorios para citas de mañana. El sistema marca cada recordatorio enviado para no duplicarlo.

No uses la contraseña normal de Gmail ni compartas la contraseña de aplicación. `api/mail_config.local.php` está excluido de Git; conserva esa exclusión. Si algo falla, la cita permanece guardada y se registra el error de SMTP en el log de PHP de XAMPP. El servidor local necesita poder conectarse a `smtp.gmail.com` por TLS en el puerto 587.

InfinityFree gratuito no ofrece tareas programadas; por eso los recordatorios se ejecutan desde el panel cuando el administrador pulsa el botón. Los avisos de cancelación, reprogramación y cambios de estado sí se generan al ocurrir el cambio.
