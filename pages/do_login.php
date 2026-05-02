<?php
/**
 * Processamento do Login
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ?p=login');
    exit;
}

// Validar CSRF
if (!Security::validateCSRF($_POST['csrf_token'] ?? '')) {
    $_SESSION['login_error'] = 'Token de segurança inválido. Tente novamente.';
    header('Location: ?p=login');
    exit;
}

// Verificar brute force
if (!Security::checkBruteForce()) {
    $_SESSION['login_error'] = 'Muitas tentativas. Aguarde 15 minutos.';
    header('Location: ?p=login');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validar credenciais
if ($username === AUTH_USER && password_verify($password, AUTH_PASS_HASH)) {
    // Login OK
    Security::resetBruteForce();
    session_regenerate_id(true);
    $_SESSION['authenticated'] = true;
    $_SESSION['user'] = $username;
    $_SESSION['last_activity'] = time();
    $_SESSION['ip'] = Security::getClientIP();

    // Log de acesso
    $logMsg = date('Y-m-d H:i:s') . ' | LOGIN OK | IP: ' . Security::getClientIP() . ' | User: ' . $username . PHP_EOL;
    file_put_contents(LOGS_PATH . '/access.log', $logMsg, FILE_APPEND | LOCK_EX);

    header('Location: ?p=dashboard');
    exit;
} else {
    // Login falhou
    Security::recordFailedLogin();

    $logMsg = date('Y-m-d H:i:s') . ' | LOGIN FAIL | IP: ' . Security::getClientIP() . ' | User: ' . $username . PHP_EOL;
    file_put_contents(LOGS_PATH . '/access.log', $logMsg, FILE_APPEND | LOCK_EX);

    $_SESSION['login_error'] = 'Usuário ou senha incorretos.';
    header('Location: ?p=login');
    exit;
}
