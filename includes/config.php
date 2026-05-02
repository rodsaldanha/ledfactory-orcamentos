<?php
/**
 * LED Factory - Sistema de Orçamentos
 * Configuração Principal
 */

// =============================================
// BANCO DE DADOS
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'ledfactory_orcamentos');  // ALTERAR para o nome real do banco
define('DB_USER', 'ledfactory_user');        // ALTERAR para o usuário real
define('DB_PASS', 'SENHA_DO_BANCO');         // ALTERAR para a senha real
define('DB_CHARSET', 'utf8mb4');

// =============================================
// AUTENTICAÇÃO
// =============================================
define('AUTH_USER', 'admin');
define('AUTH_PASS_HASH', '$2b$12$yffHXsbjO10ACTGC8bVVzu3r4lRYRS58OfXyL9vAP8FgpGkfHgvI.'); // Led@2026!
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_NAME', 'LEDF_SESSION');

// =============================================
// APLICAÇÃO
// =============================================
define('APP_NAME', 'LED Factory - Orçamentos');
define('APP_VERSION', '1.0.0');
define('BASE_PATH', dirname(__DIR__));
define('LOGS_PATH', BASE_PATH . '/logs');
define('TEMPLATES_PATH', BASE_PATH . '/templates');
define('ASSETS_PATH', BASE_PATH . '/assets');

// =============================================
// SEGURANÇA
// =============================================
define('CSRF_TOKEN_LIFETIME', 1800); // 30 minutos
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos
define('RATE_LIMIT_REQUESTS', 60); // máximo por minuto
define('RATE_LIMIT_WINDOW', 60);   // janela em segundos

// =============================================
// CONTATO RODAPÉ PDF
// =============================================
define('CONTACT_EMAIL', 'contato@ledfactory.com.br');
define('CONTACT_PHONE', '(11) 93379-7580');
define('CONTACT_INSTAGRAM', '@ledfactoryoficial');
define('CONTACT_SITE', 'ledfactory.com.br');

// =============================================
// VALIDADE DO ORÇAMENTO
// =============================================
define('QUOTE_VALIDITY_DAYS', 15);
