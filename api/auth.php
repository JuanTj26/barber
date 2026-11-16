<?php
require_once __DIR__ . '/conexion.php';
session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']), 'samesite' => 'Lax']);
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true) ?? [];

function authJson($payload, $status = 200) { http_response_code($status); echo json_encode($payload); exit; }
if ($method === 'GET') {
    if (($_GET['action'] ?? '') === 'status') {
        $count = (int)$GLOBALS['conn']->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
        $client = null;
        if (!empty($_SESSION['client_id'])) {
            $stmt = $GLOBALS['conn']->prepare('SELECT id,nombre,telefono,email FROM clientes WHERE id=?');
            $stmt->execute([$_SESSION['client_id']]); $client = $stmt->fetch() ?: null;
        }
        authJson(['success' => true, 'setup' => $count === 0, 'authenticated' => !empty($_SESSION['admin_id']), 'cliente' => $client]);
    }
    authJson(['success' => !empty($_SESSION['admin_id']), 'authenticated' => !empty($_SESSION['admin_id'])]);
}
if ($method !== 'POST') authJson(['success' => false, 'message' => 'Método no permitido.'], 405);
$action = $data['action'] ?? '';
if ($action === 'setup') {
    $count = (int)$conn->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
    $email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $data['password'] ?? '';
    if ($count > 0) authJson(['success' => false, 'message' => 'La configuración inicial ya se realizó.'], 409);
    if (!$email || strlen($password) < 8) authJson(['success' => false, 'message' => 'Usa un correo válido y una contraseña de al menos 8 caracteres.'], 400);
    $stmt = $conn->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)');
    $stmt->execute([trim($data['nombre'] ?? 'Administrador'), $email, password_hash($password, PASSWORD_DEFAULT)]);
    session_regenerate_id(true); $_SESSION['admin_id'] = (int)$conn->lastInsertId(); $_SESSION['admin_email'] = $email;
    authJson(['success' => true, 'message' => 'Cuenta de administrador creada.']);
}
if ($action === 'login') {
    $stmt = $conn->prepare('SELECT id, email, password FROM usuarios WHERE email = ?');
    $stmt->execute([trim($data['email'] ?? '')]); $admin = $stmt->fetch();
    if (!$admin || !password_verify($data['password'] ?? '', $admin['password'])) authJson(['success' => false, 'message' => 'Correo o contraseña incorrectos.'], 401);
    session_regenerate_id(true); $_SESSION['admin_id'] = (int)$admin['id']; $_SESSION['admin_email'] = $admin['email'];
    authJson(['success' => true, 'message' => 'Sesión iniciada.']);
}
if ($action === 'logout') { $_SESSION = []; session_destroy(); authJson(['success' => true]); }
if ($action === 'customer_logout') { unset($_SESSION['client_id']); session_regenerate_id(true); authJson(['success' => true]); }
if ($action === 'customer_register') {
    $nombre = trim($data['nombre'] ?? ''); $telefono = preg_replace('/\D+/', '', $data['telefono'] ?? '');
    $email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL); $password = $data['password'] ?? '';
    if (!$nombre || strlen($telefono) < 10 || !$email || strlen($password) < 8) authJson(['success' => false, 'message' => 'Completa nombre, teléfono, correo válido y contraseña de al menos 8 caracteres.'], 400);
    try { $stmt = $conn->prepare('INSERT INTO clientes (nombre, telefono, email, password_hash) VALUES (?, ?, ?, ?)'); $stmt->execute([$nombre, $telefono, $email, password_hash($password, PASSWORD_DEFAULT)]); }
    catch (PDOException $e) { authJson(['success' => false, 'message' => 'Ese teléfono o correo ya está registrado.'], 409); }
    authJson(['success' => true, 'message' => 'Cuenta de cliente creada. Ya puedes reservar con tus datos.']);
}
if ($action === 'customer_login') {
    $stmt = $conn->prepare('SELECT id, nombre, telefono, email, password_hash FROM clientes WHERE email = ? AND password_hash IS NOT NULL');
    $stmt->execute([trim($data['email'] ?? '')]); $client = $stmt->fetch();
    if (!$client || !password_verify($data['password'] ?? '', $client['password_hash'])) authJson(['success' => false, 'message' => 'Correo o contraseña incorrectos.'], 401);
    session_regenerate_id(true); $_SESSION['client_id'] = (int)$client['id'];
    unset($client['password_hash']); authJson(['success' => true, 'cliente' => $client]);
}
authJson(['success' => false, 'message' => 'Acción no válida.'], 400);
