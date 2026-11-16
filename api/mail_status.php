<?php
require_once __DIR__ . '/conexion.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']),'samesite'=>'Lax']);
session_start();
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'message'=>'Inicia sesión como administrador.']);
    exit;
}
$path=__DIR__.'/mail_config.local.php';
$exists=is_file($path);$readable=$exists&&is_readable($path);$config=$readable?require $path:[];
$appPassword=str_replace(' ','',$config['app_password']??'');
$result=[
    'success'=>true,
    'config_file_exists'=>$exists,
    'config_file_readable'=>$readable,
    'sender_configured'=>!empty($config['username']),
    'app_password_configured'=>$appPassword!==''&&strpos($appPassword,'PEGA_AQUI_')!==0,
    'openssl_enabled'=>extension_loaded('openssl'),
    'phpmailer_loaded'=>class_exists(\PHPMailer\PHPMailer\PHPMailer::class),
];
if (!empty($_GET['logs'])) {
    $logPath=rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'barberApp-mail.log';
    $entries=is_readable($logPath)?file($logPath,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES):[];
    $result['logs']=array_slice($entries,-50);
}
echo json_encode($result);
