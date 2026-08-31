<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - CONTROLADOR DE LOGOUT
 * ============================================================================
 * Arquivo: controllers/logout.php
 * Propósito: Destruir a sessão ativa e redirecionar o usuário para a tela inicial.
 */

session_start();

// Limpa todas as variáveis da sessão
$_SESSION = array();

// Destrói a sessão
session_destroy();

// Redireciona para a landing page principal
header("Location: ../index.php");
exit();