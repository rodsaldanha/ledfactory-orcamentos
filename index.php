<?php
/**
 * LED Factory - Sistema de Orçamentos
 * Ponto de entrada principal
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/database.php';

// Inicializar segurança
Security::init();

// Limpar arquivos temporários periodicamente
if (mt_rand(1, 50) === 1) {
    Security::cleanupTempFiles();
}

// Roteamento simples
$page = isset($_GET['p']) ? Security::sanitize($_GET['p']) : 'dashboard';

// Verificar autenticação (exceto login)
if ($page !== 'login' && $page !== 'do_login') {
    if (empty($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
        header('Location: ?p=login');
        exit;
    }
    // Verificar expiração da sessão
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
        session_unset();
        session_destroy();
        header('Location: ?p=login&expired=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Rotas
switch ($page) {
    case 'login':
        include __DIR__ . '/pages/login.php';
        break;

    case 'do_login':
        include __DIR__ . '/pages/do_login.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ?p=login');
        exit;

    case 'dashboard':
        include __DIR__ . '/pages/dashboard.php';
        break;

    case 'novo':
        include __DIR__ . '/pages/novo.php';
        break;

    case 'salvar':
        include __DIR__ . '/pages/salvar.php';
        break;

    case 'download':
        include __DIR__ . '/pages/download.php';
        break;

    case 'historico':
        include __DIR__ . '/pages/historico.php';
        break;

    case 'deletar':
        include __DIR__ . '/pages/deletar.php';
        break;

    case 'setup':
        // Executar apenas uma vez para criar tabelas
        if (!empty($_SESSION['authenticated'])) {
            $db = Database::getInstance();
            $db->setupSchema();
            echo '<div style="background:#0A0A0A;color:#27AE60;font-family:sans-serif;padding:40px;text-align:center;"><h2>✓ Tabelas criadas com sucesso!</h2><p><a href="?p=dashboard" style="color:#149BB9;">Ir para o Dashboard</a></p></div>';
        }
        break;

    default:
        header('Location: ?p=dashboard');
        exit;
}
