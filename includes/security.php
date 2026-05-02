<?php
/**
 * LED Factory - Módulo de Segurança
 * Proteção contra CSRF, brute force, rate limiting e headers
 */

class Security {

    /**
     * Inicializa todas as proteções de segurança
     */
    public static function init() {
        self::setSecurityHeaders();
        self::startSecureSession();
        self::rateLimitCheck();
    }

    /**
     * Headers HTTP de segurança
     */
    public static function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; img-src 'self' data:;");
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
    }

    /**
     * Sessão segura
     */
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $secure,
                'httponly'  => true,
                'samesite'  => 'Strict'
            ]);
            session_start();
            session_regenerate_id(false);
        }
    }

    /**
     * Gera token CSRF
     */
    public static function generateCSRF() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_time'] = time();
        return $token;
    }

    /**
     * Valida token CSRF
     */
    public static function validateCSRF($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        if (time() - $_SESSION['csrf_time'] > CSRF_TOKEN_LIFETIME) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_time']);
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Rate limiting por IP
     */
    public static function rateLimitCheck() {
        $ip = self::getClientIP();
        $file = LOGS_PATH . '/rate_' . md5($ip) . '.tmp';

        if (!file_exists(LOGS_PATH)) {
            mkdir(LOGS_PATH, 0750, true);
        }

        $requests = [];
        if (file_exists($file)) {
            $data = file_get_contents($file);
            $requests = json_decode($data, true) ?: [];
        }

        $now = time();
        $requests = array_filter($requests, function($t) use ($now) {
            return ($now - $t) < RATE_LIMIT_WINDOW;
        });

        if (count($requests) >= RATE_LIMIT_REQUESTS) {
            http_response_code(429);
            die('<!DOCTYPE html><html><body style="background:#0A0A0A;color:#F5F5F5;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;"><div style="text-align:center"><h1 style="color:#149BB9">429</h1><p>Muitas requisições. Aguarde um momento.</p></div></body></html>');
        }

        $requests[] = $now;
        file_put_contents($file, json_encode($requests), LOCK_EX);
    }

    /**
     * Proteção contra brute force no login
     */
    public static function checkBruteForce() {
        $ip = self::getClientIP();
        $file = LOGS_PATH . '/bruteforce_' . md5($ip) . '.tmp';

        if (!file_exists($file)) {
            return true;
        }

        $data = json_decode(file_get_contents($file), true);
        if (!$data) return true;

        if ($data['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            if (time() - $data['last_attempt'] < LOGIN_LOCKOUT_TIME) {
                $remaining = LOGIN_LOCKOUT_TIME - (time() - $data['last_attempt']);
                return false;
            }
            // Lockout expirou, resetar
            unlink($file);
            return true;
        }

        return true;
    }

    /**
     * Registra tentativa de login falha
     */
    public static function recordFailedLogin() {
        $ip = self::getClientIP();
        $file = LOGS_PATH . '/bruteforce_' . md5($ip) . '.tmp';

        $data = ['attempts' => 0, 'last_attempt' => 0];
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true) ?: $data;
        }

        $data['attempts']++;
        $data['last_attempt'] = time();
        file_put_contents($file, json_encode($data), LOCK_EX);
    }

    /**
     * Reseta contador de brute force
     */
    public static function resetBruteForce() {
        $ip = self::getClientIP();
        $file = LOGS_PATH . '/bruteforce_' . md5($ip) . '.tmp';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Obtém IP real do cliente
     */
    public static function getClientIP() {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = explode(',', $_SERVER[$header])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Sanitiza input
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Limpa arquivos temporários antigos (rate limit, brute force)
     */
    public static function cleanupTempFiles() {
        $files = glob(LOGS_PATH . '/{rate_,bruteforce_}*.tmp', GLOB_BRACE);
        $now = time();
        foreach ($files as $file) {
            if ($now - filemtime($file) > 3600) {
                unlink($file);
            }
        }
    }
}
