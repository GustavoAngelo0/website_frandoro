<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - TRAVA DE SEGURANÇA CENTRALIZADA
 * ============================================================================
 * Arquivo: config/autenticacao.php
 * Propósito: Validar a sessão e aplicar travas de segurança nos cookies.
 */

if (session_status() === PHP_SESSION_NONE) {
    // Diretivas de segurança para os cookies de sessão
    ini_set('session.cookie_httponly', 1);  // Protege o cookie contra roubo via JavaScript (XSS)
    ini_set('session.cookie_use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax'); // Proteção contra requisições forjadas (CSRF)

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1); // Exige HTTPS para enviar o cookie em produção
    }

    session_start();
}

// Redireciona para o login se não houver usuário autenticado na sessão
$usuarioId = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if (!$usuarioId) {
    header("Location: login.php");
    exit();
}