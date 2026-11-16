<?php
require_once __DIR__ . '/conexion.php';
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']);
session_start();
function barberJson($data,$status=200){http_response_code($status);echo json_encode($data);exit;}
function barberAdmin(){if(empty($_SESSION['admin_id']))barberJson(['success'=>false,'message'=>'Inicia sesión como administrador.'],401);}
$method=$_SERVER['REQUEST_METHOD'];$data=json_decode(file_get_contents('php://input'),true)??[];
if($method==='GET'){
    if(!empty($_GET['admin'])){barberAdmin();$stmt=$conn->query('SELECT id,nombre,especialidad,activo FROM barberos ORDER BY nombre');}
    else{$stmt=$conn->query('SELECT id,nombre,especialidad FROM barberos WHERE activo=1 ORDER BY nombre');}
    barberJson($stmt->fetchAll());
}
barberAdmin();
if($method==='POST'||$method==='PUT'){
    $id=(int)($data['id']??0);$nombre=trim($data['nombre']??'');$especialidad=trim($data['especialidad']??'');
    if($method==='PUT' && isset($data['activo']) && !$nombre){if(!$id)barberJson(['success'=>false,'message'=>'Barbero inválido.'],400);$stmt=$conn->prepare('UPDATE barberos SET activo=? WHERE id=?');$stmt->execute([(int)(bool)$data['activo'],$id]);barberJson(['success'=>true,'message'=>'Estado del barbero actualizado.']);}
    if(!$nombre)barberJson(['success'=>false,'message'=>'El nombre del barbero es obligatorio.'],400);
    if($method==='POST'){$stmt=$conn->prepare('INSERT INTO barberos(nombre,especialidad,activo) VALUES(?,?,1)');$stmt->execute([$nombre,$especialidad]);barberJson(['success'=>true,'message'=>'Barbero agregado.']);}
    if(!$id)barberJson(['success'=>false,'message'=>'Barbero inválido.'],400);
    $stmt=$conn->prepare('UPDATE barberos SET nombre=?,especialidad=? WHERE id=?');$stmt->execute([$nombre,$especialidad,$id]);barberJson(['success'=>true,'message'=>'Barbero actualizado.']);
}
if($method==='DELETE'){
    $id=(int)($data['id']??0);if(!$id)barberJson(['success'=>false,'message'=>'Barbero inválido.'],400);
    $stmt=$conn->prepare('UPDATE barberos SET activo=0 WHERE id=?');$stmt->execute([$id]);barberJson(['success'=>true,'message'=>'Barbero desactivado.']);
}
barberJson(['success'=>false,'message'=>'Método no permitido.'],405);
