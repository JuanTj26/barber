<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/notificaciones.php';
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']);
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Inicia sesión como administrador.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'message'=>'Método no permitido.']);
    exit;
}

$manana=(new DateTimeImmutable('tomorrow'))->format('Y-m-d');
$stmt=$conn->prepare("SELECT c.id,cl.email FROM citas c JOIN clientes cl ON cl.id=c.cliente_id WHERE c.fecha=? AND c.estado IN ('pendiente','confirmada') AND c.recordatorio_enviado_en IS NULL ORDER BY c.hora_inicio");
$stmt->execute([$manana]);
$citas=$stmt->fetchAll();
$sent=0;$failed=0;$sinCorreo=0;

$claim=$conn->prepare('UPDATE citas SET recordatorio_enviado_en=\'1970-01-01 00:00:01\' WHERE id=? AND recordatorio_enviado_en IS NULL');
$markSent=$conn->prepare('UPDATE citas SET recordatorio_enviado_en=NOW() WHERE id=? AND recordatorio_enviado_en=\'1970-01-01 00:00:01\'');
$release=$conn->prepare('UPDATE citas SET recordatorio_enviado_en=NULL WHERE id=? AND recordatorio_enviado_en=\'1970-01-01 00:00:01\'');
foreach($citas as $row){
    $id=(int)$row['id'];
    if(empty($row['email'])){$sinCorreo++;registrarLogCorreo("cita_id=$id evento=reminder resultado=omitido motivo=sin_correo");continue;}
    $claim->execute([$id]);if(!$claim->rowCount())continue;
    $notice=notificarCita($conn,$id,'reminder');
    if(!empty($notice['sent'])){
        $markSent->execute([$id]);
        if($markSent->rowCount())$sent++;else $failed++;
    }else{$release->execute([$id]);$failed++;}
}

registrarLogCorreo("lote_recordatorios fecha=$manana enviados=$sent fallidos=$failed sin_correo=$sinCorreo");
echo json_encode(['success'=>true,'message'=>'Proceso de recordatorios terminado.','fecha_objetivo'=>$manana,'enviados'=>$sent,'fallidos'=>$failed,'sin_correo'=>$sinCorreo,'sin_pendientes'=>count($citas)===0]);
