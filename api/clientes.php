<?php
require_once __DIR__ . '/conexion.php';
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']);session_start();
if (!empty($_GET['perfil']) && !empty($_SESSION['client_id'])) {
    $stmt=$conn->prepare('SELECT id,nombre,telefono,email FROM clientes WHERE id=?');$stmt->execute([$_SESSION['client_id']]);$cliente=$stmt->fetch();
    if(!$cliente){http_response_code(404);echo json_encode(['success'=>false,'message'=>'No se encontró el perfil.']);exit;}
    $stmt=$conn->prepare('SELECT c.folio,c.fecha,c.hora_inicio,c.hora_fin,c.estado,s.nombre AS servicio_name,s.precio,b.nombre AS barbero_nombre FROM citas c JOIN servicios s ON s.id=c.servicio_id LEFT JOIN barberos b ON b.id=c.barbero_id WHERE c.cliente_id=? ORDER BY c.fecha DESC,c.hora_inicio DESC');$stmt->execute([$cliente['id']]);
    echo json_encode(['success'=>true,'cliente'=>$cliente,'citas'=>$stmt->fetchAll()]);exit;
}
if(empty($_SESSION['admin_id'])){http_response_code(401);echo json_encode(['success'=>false,'message'=>'Inicia sesión como administrador.']);exit;}
$stmt=$conn->query('SELECT cl.id,cl.nombre,cl.telefono,cl.email,COUNT(c.id) AS total_citas,MAX(c.fecha) AS ultima_cita FROM clientes cl LEFT JOIN citas c ON c.cliente_id=cl.id GROUP BY cl.id,cl.nombre,cl.telefono,cl.email ORDER BY cl.nombre');echo json_encode($stmt->fetchAll());
