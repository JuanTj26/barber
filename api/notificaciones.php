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
    $stmt = $conn->prepare('SELECT c.folio,c.fecha,c.hora_inicio,c.hora_fin,c.estado,c.motivo_cancelacion,cl.nombre AS cliente_nombre,cl.email,s.nombre AS servicio_name,b.nombre AS barbero_nombre FROM citas c JOIN clientes cl ON cl.id=c.cliente_id JOIN servicios s ON s.id=c.servicio_id LEFT JOIN barberos b ON b.id=c.barbero_id WHERE c.id=?');
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
        'created' => ['Nueva reservación', 'Recibimos tu solicitud y guardamos los datos de tu cita.', 'CITA REGISTRADA'],
        'rescheduled' => ['Tu cita fue reprogramada', 'El horario de tu cita fue actualizado.', 'HORARIO ACTUALIZADO'],
        'cancelled' => ['Tu cita fue cancelada', 'La cita indicada quedó cancelada.', 'CITA CANCELADA'],
        'reminder' => ['Recordatorio de tu cita', 'Te recordamos que mañana tienes una cita en Barber TECH.', 'RECORDATORIO'],
        'status' => ['Actualización de tu cita', 'El estado de tu cita cambió.', 'CAMBIO DE ESTADO'],
    ][$evento] ?? ['Actualización de tu cita', 'Hay una actualización en tu reservación.', 'AVISO DE CITA'];

    $escape = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $brandName = $config['from_name'] ?? 'Barber TECH';
    $statusLabels = ['pendiente'=>'Pendiente','confirmada'=>'Confirmada','cancelada'=>'Cancelada','completada'=>'Completada'];
    $statusLabel = $statusLabels[$cita['estado']] ?? $cita['estado'];
    $cancellationReason = trim($cita['motivo_cancelacion'] ?? '');
    $statusColors = [
        'pendiente'=>['#fff4d6','#805b00'], 'confirmada'=>['#e8f4ff','#155a91'],
        'cancelada'=>['#fde8e7','#a8322a'], 'completada'=>['#e7f6ed','#21683c'],
    ];
    [$statusBg,$statusFg] = $statusColors[$cita['estado']] ?? ['#eeeeee','#444444'];
    $siteUrl = filter_var($config['site_url'] ?? '', FILTER_VALIDATE_URL) ?: '';
    $cta = $siteUrl !== ''
        ? '<tr><td align="center" style="padding:8px 32px 28px"><a href="' . $escape($siteUrl) . '" style="display:inline-block;background:#c88a37;color:#171717;text-decoration:none;font-family:Arial,sans-serif;font-size:14px;font-weight:bold;padding:14px 24px;border-radius:6px">Consultar mi cita</a></td></tr>'
        : '';
    $reasonBlock = $evento === 'cancelled' && $cancellationReason !== ''
        ? '<tr><td style="padding:0 32px 22px"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#fff8ed;border-left:4px solid #c88a37"><tr><td style="padding:15px 17px"><div style="font-size:11px;letter-spacing:1px;color:#8b5b1a;font-weight:bold;margin-bottom:6px">MOTIVO DE CANCELACIÓN</div><div style="font-size:14px;line-height:1.6;color:#333">' . $escape($cancellationReason) . '</div></td></tr></table></td></tr>'
        : '';

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
        $mail->setFrom($config['username'], $brandName);
        $mail->addAddress($cita['email'], $cita['cliente_nombre']);
        $mail->isHTML(true);
        $mail->Subject = $eventText[0] . ' | ' . $brandName;
        $mail->Body = '<!doctype html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>' . $escape($eventText[0]) . '</title></head>'
            . '<body style="margin:0;padding:0;background:#f2f0ec;color:#222;font-family:Arial,Helvetica,sans-serif">'
            . '<div style="display:none;font-size:1px;color:#f2f0ec;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden">' . $escape($eventText[1]) . '</div>'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f2f0ec;padding:30px 12px"><tr><td align="center">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background:#fff;border-radius:12px;overflow:hidden">'
            . '<tr><td style="background:#171717;padding:26px 32px;border-bottom:4px solid #c88a37">'
            . '<div style="font-size:11px;letter-spacing:3px;color:#d4a45e;font-weight:bold">BARBERÍA</div>'
            . '<div style="font-size:24px;letter-spacing:1px;color:#fff;font-weight:bold;margin-top:5px">' . $escape($brandName) . '</div></td></tr>'
            . '<tr><td style="padding:32px 32px 12px"><div style="font-size:11px;letter-spacing:1.5px;color:#a66a1f;font-weight:bold">' . $escape($eventText[2]) . '</div>'
            . '<h1 style="font-size:25px;line-height:1.25;color:#1b1b1b;margin:12px 0">' . $escape($eventText[0]) . '</h1>'
            . '<p style="font-size:15px;line-height:1.6;color:#5b5b5b;margin:0">Hola ' . $escape($cita['cliente_nombre']) . ', ' . $escape($eventText[1]) . '</p></td></tr>'
            . '<tr><td style="padding:18px 32px 22px"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#faf8f4;border:1px solid #ebe5da;border-radius:8px">'
            . '<tr><td style="padding:18px 20px 6px;color:#777;font-size:11px;letter-spacing:1.5px;font-weight:bold">FOLIO DE CITA</td></tr>'
            . '<tr><td style="padding:0 20px 16px;color:#1a1a1a;font-family:monospace;font-size:23px;font-weight:bold;letter-spacing:3px">' . $escape($cita['folio']) . '</td></tr>'
            . '<tr><td style="padding:0 20px 18px"><span style="display:inline-block;background:' . $statusBg . ';color:' . $statusFg . ';padding:6px 10px;border-radius:20px;font-size:12px;font-weight:bold">' . $escape($statusLabel) . '</span></td></tr>'
            . '</table></td></tr>'
            . '<tr><td style="padding:0 32px 24px"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">'
            . '<tr><td style="padding:12px 0;border-bottom:1px solid #eee;color:#888;font-size:13px;width:38%">Servicio</td><td style="padding:12px 0;border-bottom:1px solid #eee;color:#222;font-size:14px;font-weight:bold">' . $escape($cita['servicio_name']) . '</td></tr>'
            . '<tr><td style="padding:12px 0;border-bottom:1px solid #eee;color:#888;font-size:13px">Barbero</td><td style="padding:12px 0;border-bottom:1px solid #eee;color:#222;font-size:14px;font-weight:bold">' . $escape($barber) . '</td></tr>'
            . '<tr><td style="padding:12px 0;border-bottom:1px solid #eee;color:#888;font-size:13px">Fecha</td><td style="padding:12px 0;border-bottom:1px solid #eee;color:#222;font-size:14px;font-weight:bold">' . $escape($date) . '</td></tr>'
            . '<tr><td style="padding:12px 0;color:#888;font-size:13px">Horario</td><td style="padding:12px 0;color:#222;font-size:14px;font-weight:bold">' . $escape($start . ' – ' . $end) . '</td></tr>'
            . '</table></td></tr>' . $reasonBlock . $cta
            . '<tr><td style="padding:20px 32px;background:#f8f7f4;border-top:1px solid #eee;color:#777;font-size:12px;line-height:1.6">Conserva este correo y tu folio para consultar o gestionar tu cita. Este es un mensaje automático de ' . $escape($brandName) . '.</td></tr>'
            . '</table><div style="font-size:11px;color:#999;padding:16px">Gracias por elegirnos.</div></td></tr></table></body></html>';
        $mail->AltBody = $eventText[0] . "\n\nHola {$cita['cliente_nombre']}, {$eventText[1]}\n\nFolio: {$cita['folio']}\nEstado: {$statusLabel}\nServicio: {$cita['servicio_name']}\nBarbero: {$barber}\nFecha: {$date}\nHorario: {$start}-{$end}"
            . ($evento === 'cancelled' && $cancellationReason !== '' ? "\nMotivo de cancelación: $cancellationReason" : '')
            . ($siteUrl ? "\n\nConsulta tu cita: $siteUrl" : '');
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
