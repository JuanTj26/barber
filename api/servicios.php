<?php
require_once __DIR__ . '/conexion.php';
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']); session_start();
function serviceJson($data,$status=200){http_response_code($status);echo json_encode($data);exit;}
function requireAdmin(){if(empty($_SESSION['admin_id']))serviceJson(['success'=>false,'message'=>'Inicia sesión como administrador.'],401);}
$method=$_SERVER['REQUEST_METHOD']; $data=json_decode(file_get_contents('php://input'),true)??[];
if($method==='GET'){$stmt=$conn->query('SELECT id,nombre,descripcion,precio,duracion_minutos FROM servicios WHERE activo=1 ORDER BY nombre');serviceJson($stmt->fetchAll());}
requireAdmin();
if($method==='POST'){
 $nombre=trim($data['nombre']??'');$descripcion=trim($data['descripcion']??'');$precio=(float)($data['precio']??0);$duracion=(int)($data['duracion_minutos']??0);
 if(!$nombre||$precio<0||$duracion<1)serviceJson(['success'=>false,'message'=>'Completa nombre, precio y duración válida.'],400);
 $stmt=$conn->prepare('INSERT INTO servicios(nombre,descripcion,precio,duracion_minutos,activo) VALUES(?,?,?,?,1)');$stmt->execute([$nombre,$descripcion,$precio,$duracion]);serviceJson(['success'=>true,'message'=>'Servicio agregado.']);
}
if($method==='PUT'){
 $id=(int)($data['id']??0);$nombre=trim($data['nombre']??'');$descripcion=trim($data['descripcion']??'');$precio=(float)($data['precio']??-1);$duracion=(int)($data['duracion_minutos']??0);
 if(!$id||!$nombre||$precio<0||$duracion<1)serviceJson(['success'=>false,'message'=>'Datos de servicio inválidos.'],400);
 $stmt=$conn->prepare('UPDATE servicios SET nombre=?,descripcion=?,precio=?,duracion_minutos=? WHERE id=? AND activo=1');$stmt->execute([$nombre,$descripcion,$precio,$duracion,$id]);serviceJson(['success'=>true,'message'=>'Servicio actualizado.']);
}
if($method==='DELETE'){$id=(int)($data['id']??0);if(!$id)serviceJson(['success'=>false,'message'=>'Servicio inválido.'],400);$stmt=$conn->prepare('UPDATE servicios SET activo=0 WHERE id=?');$stmt->execute([$id]);serviceJson(['success'=>true,'message'=>'Servicio desactivado.']);}
serviceJson(['success'=>false,'message'=>'Método no permitido.'],405);
