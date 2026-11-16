<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once dirname(__DIR__) . '/vendor/autoload.php';

function registrarLogCorreo(string $linea): void
{
    $logPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'barberApp-mail.log';
    @file_put_contents($logPath, '[' . date('Y-m-d H:i:s') . '] ' . $linea . PHP_EOL, FILE_APPEND | LOCK_EX);
}

/** Envía aviso al correo asociado con la cita. No lanza errores al flujo de reserva. */
function notificarCita(PDO $conn, int $citaId, string $evento): array
{
    $config = [];
    try {
    $stmt = $conn->prepare('SELECT c.folio,c.fecha,c.hora_inicio,c.hora_fin,c.estado,cl.nombre AS cliente_nombre,cl.email,s.nombre AS servicio_name,b.nombre AS barbero_nombre FROM citas c JOIN clientes cl ON cl.id=c.cliente_id JOIN servicios s ON s.id=c.servicio_id LEFT JOIN barberos b ON b.id=c.barbero_id WHERE c.id=?');
    $stmt->execute([$citaId]);
    $cita = $stmt->fetch();
    if (!$cita || empty($cita['email'])) {
        registrarLogCorreo("cita_id=$citaId evento=$evento resultado=omitido motivo=sin_correo");
        return ['sent' => false, 'reason' => 'no_email'];
    }

    $configFile = __DIR__ . '/mail_config.local.php';
    if (!is_file($configFile)) {
        registrarLogCorreo("cita_id=$citaId evento=$evento resultado=omitido motivo=archivo_configuracion_ausente");
        return ['sent' => false, 'reason' => 'config_file_missing'];
    }
    $config = require $configFile;
    if (empty($config['username']) || empty($config['app_password']) || strpos($config['app_password'], 'PEGA_AQUI_') === 0) {
        $reason = empty($config['username']) ? 'sender_missing' : 'app_password_missing';
        registrarLogCorreo("cita_id=$citaId evento=$evento resultado=omitido motivo=$reason");
        return ['sent' => false, 'reason' => $reason];
    }

    $date = date('d/m/Y', strtotime($cita['fecha']));
    $start = substr($cita['hora_inicio'], 0, 5);
    $end = substr($cita['hora_fin'], 0, 5);
    $barber = $cita['barbero_nombre'] ?: 'Sin asignar';
    $eventText = [
        'created' => ['Tu cita está registrada', 'Hemos recibido tu reservación.'],
        'rescheduled' => ['Tu cita fue reprogramada', 'El horario de tu cita fue actualizado.'],
        'cancelled' => ['Tu cita fue cancelada', 'La cita indicada quedó cancelada.'],
        'status' => ['Actualización de tu cita', 'El estado de tu cita cambió a: ' . $cita['estado'] . '.'],
    ][$evento] ?? ['Actualización de tu cita', 'Hay una actualización en tu reservación.'];

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = str_replace(' ', '', $config['app_password']);
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->Timeout = 8;
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->setFrom($config['username'], $config['from_name'] ?? 'Barbería El Estilo');
        $mail->addAddress($cita['email'], $cita['cliente_nombre']);
        $mail->isHTML(true);
        $mail->Subject = $eventText[0] . ' — Barbería El Estilo';
        $mail->Body = '<div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;color:#222">'
            . '<h2>' . htmlspecialchars($eventText[0], ENT_QUOTES, 'UTF-8') . '</h2>'
            . '<p>Hola ' . htmlspecialchars($cita['cliente_nombre'], ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>' . htmlspecialchars($eventText[1], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p><strong>Folio:</strong> ' . htmlspecialchars($cita['folio'], ENT_QUOTES, 'UTF-8') . '<br>'
            . '<strong>Servicio:</strong> ' . htmlspecialchars($cita['servicio_name'], ENT_QUOTES, 'UTF-8') . '<br>'
            . '<strong>Barbero:</strong> ' . htmlspecialchars($barber, ENT_QUOTES, 'UTF-8') . '<br>'
            . '<strong>Fecha:</strong> ' . $date . '<br><strong>Horario:</strong> ' . $start . '–' . $end . '<br>'
            . '<strong>Estado:</strong> ' . htmlspecialchars($cita['estado'], ENT_QUOTES, 'UTF-8') . '</p>'
            . '<p>Conserva tu folio para consultar o gestionar tu cita en nuestro sitio.</p></div>';
        $mail->AltBody = $eventText[0] . "\nFolio: {$cita['folio']}\nServicio: {$cita['servicio_name']}\nBarbero: {$barber}\nFecha: {$date}\nHorario: {$start}-{$end}\nEstado: {$cita['estado']}";
        $mail->send();
        registrarLogCorreo("cita_id=$citaId evento=$evento resultado=enviado");
        return ['sent' => true];
    } catch (Throwable $e) {
        $detail = preg_replace('/[\r\n\t]+/', ' ', $e->getMessage());
        foreach ([$config['username'] ?? '', $config['app_password'] ?? '', str_replace(' ', '', $config['app_password'] ?? '')] as $secret) {
            if ($secret !== '') $detail = str_replace($secret, '[dato oculto]', $detail);
        }
        $detail = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[correo oculto]', $detail);
        $detail = substr($detail, 0, 300);
        registrarLogCorreo("cita_id=$citaId evento=$evento resultado=error detalle=" . $detail);
        return ['sent' => false, 'reason' => 'send_failed'];
    }
}
