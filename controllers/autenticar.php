<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - CONTROLADOR DE AUTENTICAÇÃO (LOGIN)
 * ============================================================================
 * Arquivo: controllers/autenticar.php
 * Propósito: Validar credenciais, iniciar a sessão e redirecionar para o painel.
 */

session_start();

// 1. Carrega a conexão PDO com o MySQL
require_once '../config/conexao.php';

// 2. Garante execução apenas via envio de formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Recebe e higieniza os campos do formulário
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    // 4. Valida se os campos foram preenchidos
    if (empty($email) || empty($senha)) {
        // Preenche tanto 'mensagem' quanto 'erro' para garantir exibição na tela
        $_SESSION['mensagem'] = "Preencha todos os campos para entrar.";
        $_SESSION['erro']     = "Preencha todos os campos para entrar.";
        header("Location: ../views/login.php");
        exit();
    }

    try {
        // 5. Busca o usuário no banco de dados pelo e-mail
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 6. Confirma se o usuário existe e se a senha criptografada confere
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            // [SEGURANÇA] Regenera o ID da sessão para evitar Session Fixation
            session_regenerate_id(true);

            // Grava os dados essenciais na Sessão do PHP
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['id']           = $usuario['id']; // Compatibilidade com autenticacao.php
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['nome']         = $usuario['nome'];

            // [PERFIL / ACOMPANHAMENTO DE NÍVEL]
            // Grava o perfil (admin ou operador). Se a coluna não existir ainda, define 'operador' por padrão.
            $_SESSION['usuario_perfil'] = $usuario['perfil'] ?? 'operador';
            $_SESSION['perfil']         = $usuario['perfil'] ?? 'operador';

            // Redireciona com sucesso para o Painel Principal
            header("Location: ../views/painel.php");
            exit();

        } else {
            // Credenciais incorretas
            $_SESSION['mensagem'] = "E-mail ou senha incorretos.";
            $_SESSION['erro']     = "E-mail ou senha incorretos.";
            header("Location: ../views/login.php");
            exit();
        }

    } catch (PDOException $e) {
        // Captura e exibe falhas do MySQL durante o processamento
        $_SESSION['mensagem'] = "Erro no banco de dados: " . $e->getMessage();
        $_SESSION['erro']     = "Erro no banco de dados: " . $e->getMessage();
        header("Location: ../views/login.php");
        exit();
    }

} else {
    // Bloqueia acesso direto via URL
    header("Location: ../views/login.php");
    exit();
}