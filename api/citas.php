<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/notificaciones.php';
session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']), 'samesite' => 'Lax']);
session_start();
function citaJson($payload, $status = 200)
{
    http_response_code($status);
    echo json_encode($payload);
    exit;
}
function adminRequired()
{
    if (empty($_SESSION['admin_id'])) citaJson(['success' => false, 'message' => 'Inicia sesión como administrador.'], 401);
}
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];

if ($method === 'POST') {
    $nombre = trim($data['nombre'] ?? '');
    $telefono = preg_replace('/\D+/', '', $data['telefono'] ?? '');
    $email = trim($data['email'] ?? '') ?: null;
    $servicioId = (int)($data['servicio_id'] ?? 0);
    $barberoId = (int)($data['barbero_id'] ?? 0);
    $clientPassword = $data['password'] ?? '';
    $fecha = $data['fecha'] ?? '';
    $horaInicio = $data['hora_inicio'] ?? '';
    $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
    $horaObj = DateTime::createFromFormat('H:i', $horaInicio);
    if (!$nombre || strlen($telefono) < 10 || !$servicioId || !$barberoId || !$fechaObj || $fechaObj->format('Y-m-d') !== $fecha || !$horaObj || $horaObj->format('H:i') !== $horaInicio || ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) || ($clientPassword && (!$email || strlen($clientPassword) < 8))) citaJson(['success' => false, 'message' => 'Revisa los datos y selecciona un barbero. Para crear cuenta se requieren correo válido y contraseña de al menos 8 caracteres.'], 400);
    if ($fecha < date('Y-m-d')) citaJson(['success' => false, 'message' => 'No puedes reservar en una fecha pasada.'], 400);
    if ((int)$fechaObj->format('w') === 0) citaJson(['success' => false, 'message' => 'Los domingos la barbería permanece cerrada. Por favor selecciona una fecha de lunes a sábado.'], 400);
    if ($horaInicio < '10:00' || $horaInicio > '19:30') citaJson(['success' => false, 'message' => 'El horario de atención es de 10:00 a 20:00. Selecciona una hora entre 10:00 y 19:30.'], 400);
    $stmt = $conn->prepare('SELECT duracion_minutos FROM servicios WHERE id = ? AND activo = 1');
    $stmt->execute([$servicioId]);
    $service = $stmt->fetch();
    if (!$service) citaJson(['success' => false, 'message' => 'Servicio inválido o no disponible.'], 400);
    $stmt=$conn->prepare('SELECT id FROM barberos WHERE id=? AND activo=1');$stmt->execute([$barberoId]);
    if(!$stmt->fetchColumn())citaJson(['success'=>false,'message'=>'El barbero seleccionado ya no está disponible.'],400);
    $start = DateTime::createFromFormat('Y-m-d H:i', "$fecha $horaInicio");
    $end = clone $start;
    $end->modify('+' . (int)$service['duracion_minutos'] . ' minutes');
    if ($end->format('Y-m-d') !== $fecha || $end->format('H:i:s') > '20:00:00') citaJson(['success' => false, 'message' => 'La cita debe iniciar y terminar dentro del horario laboral (10:00 a 20:00).'], 400);
    $horaFin = $end->format('H:i:s');
    $folio = strtoupper(bin2hex(random_bytes(5)));
    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare('SELECT id FROM citas WHERE fecha = ? AND (barbero_id = ? OR barbero_id IS NULL) AND estado <> \'cancelada\' AND (? < hora_fin AND ? > hora_inicio) FOR UPDATE');
        $stmt->execute([$fecha, $barberoId, $horaInicio . ':00', $horaFin]);
        if ($stmt->fetch()) {
            $conn->rollBack();
            citaJson(['success' => false, 'message' => 'Ese horario ya está ocupado. Elige otro.'], 409);
        }
        $stmt = $conn->prepare('SELECT id FROM clientes WHERE telefono = ?');
        $stmt->execute([$telefono]);
        $clientId = $stmt->fetchColumn();
        if ($clientId) {
            $stmt = $conn->prepare('UPDATE clientes SET nombre = ?, email = COALESCE(?, email), password_hash = COALESCE(?, password_hash) WHERE id = ?');
            $stmt->execute([$nombre, $email, $clientPassword ? password_hash($clientPassword, PASSWORD_DEFAULT) : null, $clientId]);
        } else {
            $stmt = $conn->prepare('INSERT INTO clientes (nombre, telefono, email, password_hash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$nombre, $telefono, $email, $clientPassword ? password_hash($clientPassword, PASSWORD_DEFAULT) : null]);
            $clientId = $conn->lastInsertId();
        }
        $stmt = $conn->prepare('INSERT INTO citas (cliente_id, servicio_id, barbero_id, fecha, hora_inicio, hora_fin, folio, estado) VALUES (?, ?, ?, ?, ?, ?, ?, \'pendiente\')');
        $stmt->execute([$clientId, $servicioId, $barberoId, $fecha, $horaInicio . ':00', $horaFin, $folio]);
        $id = (int)$conn->lastInsertId();
        $conn->commit();
        $emailNotice = notificarCita($conn, $id, 'created');
        citaJson(['success' => true, 'message' => '¡Cita agendada!', 'folio' => $folio, 'id' => $id, 'email_notification' => $emailNotice]);
    } catch (PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        citaJson(['success' => false, 'message' => 'No se pudo guardar la cita.'], 500);
    }
}
if ($method === 'GET') {
    if (!empty($_GET['folio'])) {
        $stmt = $conn->prepare('SELECT c.id, c.folio, c.fecha, c.hora_inicio, c.hora_fin, c.estado, cl.nombre AS cliente_nombre, cl.telefono, s.nombre AS servicio_name, s.precio, s.duracion_minutos, b.nombre AS barbero_nombre FROM citas c JOIN clientes cl ON c.cliente_id=cl.id JOIN servicios s ON c.servicio_id=s.id LEFT JOIN barberos b ON c.barbero_id=b.id WHERE c.folio=?');
        $stmt->execute([strtoupper(trim($_GET['folio']))]);
        $cita = $stmt->fetch();
        if (!$cita) citaJson(['success' => false, 'message' => 'No encontramos una cita con ese folio.'], 404);
        unset($cita['id']);
        citaJson(['success' => true, 'cita' => $cita]);
    }
    adminRequired();
    $stmt = $conn->query('SELECT c.*, cl.nombre AS cliente_nombre, cl.telefono, cl.email, s.nombre AS servicio_name, b.nombre AS barbero_nombre FROM citas c JOIN clientes cl ON c.cliente_id=cl.id JOIN servicios s ON c.servicio_id=s.id LEFT JOIN barberos b ON c.barbero_id=b.id ORDER BY c.fecha DESC, c.hora_inicio ASC');
    citaJson($stmt->fetchAll());
}
if ($method === 'PUT') {
    $action = $data['action'] ?? '';
    if ($action === 'customer_update') {
        $folio = strtoupper(trim($data['folio'] ?? ''));
        $fecha = $data['fecha'] ?? '';
        $hora = $data['hora_inicio'] ?? '';
        $dateObj = DateTime::createFromFormat('Y-m-d', $fecha);
        $timeObj = DateTime::createFromFormat('H:i', $hora);
        if (!$folio || !$dateObj || $dateObj->format('Y-m-d') !== $fecha || !$timeObj || $timeObj->format('H:i') !== $hora || $fecha < date('Y-m-d')) citaJson(['success' => false, 'message' => 'Fecha u hora inválida.'], 400);
        if ((int)$dateObj->format('w') === 0) citaJson(['success' => false, 'message' => 'Los domingos la barbería permanece cerrada. Elige de lunes a sábado.'], 400);
        if ($hora < '10:00' || $hora > '19:30') citaJson(['success' => false, 'message' => 'El horario de atención es de 10:00 a 20:00. Elige una hora entre 10:00 y 19:30.'], 400);
        try {
            $conn->beginTransaction();
                $stmt = $conn->prepare('SELECT c.id,c.servicio_id,c.barbero_id,c.estado,s.duracion_minutos,b.nombre AS barbero_nombre FROM citas c JOIN servicios s ON s.id=c.servicio_id LEFT JOIN barberos b ON b.id=c.barbero_id WHERE c.folio=? FOR UPDATE');
            $stmt->execute([$folio]);
            $old = $stmt->fetch();
            if (!$old || $old['estado'] === 'cancelada' || $old['estado'] === 'completada') {
                $conn->rollBack();
                citaJson(['success' => false, 'message' => 'La cita no existe o ya no se puede modificar.'], 404);
            }
            if (!empty($_SESSION['client_id'])) {
                $owner = $conn->prepare('SELECT cliente_id FROM citas WHERE id=?');
                $owner->execute([$old['id']]);
                if ((int)$owner->fetchColumn() !== (int)$_SESSION['client_id']) {
                    $conn->rollBack();
                    citaJson(['success' => false, 'message' => 'Esa cita no pertenece a tu cuenta.'], 403);
                }
            }
            $start = DateTime::createFromFormat('Y-m-d H:i', "$fecha $hora");
            $end = clone $start;
            $end->modify('+' . (int)$old['duracion_minutos'] . ' minutes');
            if ($end->format('Y-m-d') !== $fecha || $end->format('H:i:s') > '20:00:00') {
                $conn->rollBack();
                citaJson(['success' => false, 'message' => 'La cita debe iniciar y terminar dentro del horario laboral (10:00 a 20:00).'], 400);
            }
            $stmt = $conn->prepare('SELECT id FROM citas WHERE fecha=? AND (barbero_id=? OR barbero_id IS NULL) AND id<>? AND estado<>\'cancelada\' AND (? < hora_fin AND ? > hora_inicio) FOR UPDATE');
            $stmt->execute([$fecha, $old['barbero_id'], $old['id'], $hora . ':00', $end->format('H:i:s')]);
            if ($stmt->fetch()) {
                $conn->rollBack();
                citaJson(['success' => false, 'message' => 'Ese horario ya está ocupado. Elige otro.'], 409);
            }
            $stmt = $conn->prepare('UPDATE citas SET fecha=?,hora_inicio=?,hora_fin=? WHERE id=?');
            $stmt->execute([$fecha, $hora . ':00', $end->format('H:i:s'), $old['id']]);
            $conn->commit();
            $emailNotice = notificarCita($conn, (int)$old['id'], 'rescheduled');
            citaJson(['success' => true, 'message' => 'Horario actualizado.', 'email_notification' => $emailNotice]);
        } catch (PDOException $e) {
            if ($conn->inTransaction()) $conn->rollBack();
            citaJson(['success' => false, 'message' => 'No se pudo actualizar la cita.'], 500);
        }
    }
    if ($action === 'customer_cancel') {
        if (!empty($_SESSION['client_id'])) {
            $owner = $conn->prepare('SELECT cliente_id FROM citas WHERE folio=?');
            $owner->execute([strtoupper(trim($data['folio'] ?? ''))]);
            if ((int)$owner->fetchColumn() !== (int)$_SESSION['client_id']) citaJson(['success' => false, 'message' => 'Esa cita no pertenece a tu cuenta.'], 403);
        }
        $folio = strtoupper(trim($data['folio'] ?? ''));
        $find = $conn->prepare('SELECT id FROM citas WHERE folio=? AND estado IN (\'pendiente\',\'confirmada\')');
        $find->execute([$folio]); $citaId = (int)$find->fetchColumn();
        if (!$citaId) citaJson(['success' => false, 'message' => 'No se encontró una cita activa con ese folio.'], 404);
        $stmt = $conn->prepare('UPDATE citas SET estado=\'cancelada\',motivo_cancelacion=NULL WHERE id=?'); $stmt->execute([$citaId]);
        $emailNotice = notificarCita($conn, $citaId, 'cancelled');
        citaJson(['success' => true, 'message' => 'Cita cancelada.', 'email_notification' => $emailNotice]);
    }
    adminRequired();
    $id = (int)($data['id'] ?? 0);
    $estado = $data['estado'] ?? '';
    if (!$id || !in_array($estado, ['pendiente', 'confirmada', 'cancelada', 'completada'], true)) citaJson(['success' => false, 'message' => 'Datos inválidos.'], 400);
    $motivo = trim($data['motivo_cancelacion'] ?? '');
    if ($estado === 'cancelada' && $motivo === '') citaJson(['success'=>false,'message'=>'Escribe el motivo de cancelación.'],400);
    $motivoLength = function_exists('mb_strlen') ? mb_strlen($motivo, 'UTF-8') : strlen($motivo);
    if ($motivoLength > 500) citaJson(['success'=>false,'message'=>'El motivo no puede superar 500 caracteres.'],400);
    if ($estado === 'cancelada') {
        $stmt = $conn->prepare('UPDATE citas SET estado=?,motivo_cancelacion=? WHERE id=?');
        $stmt->execute([$estado,$motivo,$id]);
    } else {
        $stmt = $conn->prepare('UPDATE citas SET estado=?,motivo_cancelacion=NULL WHERE id=?');
        $stmt->execute([$estado,$id]);
    }
    $emailNotice = $stmt->rowCount() ? notificarCita($conn, $id, $estado === 'cancelada' ? 'cancelled' : 'status') : ['sent' => false, 'reason' => 'unchanged'];
    citaJson(['success' => true, 'message' => 'Estado actualizado.', 'email_notification' => $emailNotice]);
}
citaJson(['success' => false, 'message' => 'Método no permitido.'], 405);
