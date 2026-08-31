<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - CONTROLADOR DE CADASTRO DE USUÁRIOS
 * ============================================================================
 * Arquivo: controllers/cadastrar_usuario.php
 * Propósito: Receber formulário, criptografar a senha, salvar no banco 
 *            e autenticar o novo usuário enviando-o ao painel.
 */

session_start();

// 1. Carrega a conexão PDO com o banco de dados
require_once '../config/conexao.php';

// 2. Garante que o envio seja via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 3. Recebe e remove espaços desnecessários
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    // 4. Validação de preenchimento obrigatório
    if (empty($nome) || empty($email) || empty($senha)) {
        $_SESSION['mensagem'] = "Atenção: Por favor, preencha todos os campos.";
        $_SESSION['erro']     = "Atenção: Por favor, preencha todos os campos.";
        header("Location: ../views/cadastro.php");
        exit();
    }

    // 5. Criptografia segura de senha (BCrypt)
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        // 6. Prepara e insere o usuário na tabela 'usuarios'
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
        $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $senhaHash
        ]);

        // 7. Resgata o ID gerado pelo MySQL para este novo usuário
        $novoId = $pdo->lastInsertId();

        // 8. Autentica o usuário automaticamente na sessão
        $_SESSION['usuario_id']   = $novoId;
        $_SESSION['id']           = $novoId;
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['nome']         = $nome;

        // 9. Envia o novo usuário logado diretamente ao Painel Principal
        header("Location: ../views/painel.php");
        exit();

    } catch (PDOException $e) {
        // Trata e-mail duplicado (Código 23000 do MySQL) ou outros erros de banco
        if ($e->getCode() == 23000) {
            $_SESSION['mensagem'] = "Erro: Este e-mail já está cadastrado no sistema.";
            $_SESSION['erro']     = "Erro: Este e-mail já está cadastrado no sistema.";
        } else {
            $_SESSION['mensagem'] = "Erro no banco de dados: " . $e->getMessage();
            $_SESSION['erro']     = "Erro no banco de dados: " . $e->getMessage();
        }

        header("Location: ../views/cadastro.php");
        exit();
    }

} else {
    // Redireciona caso o arquivo seja acessado diretamente pela URL
    header("Location: ../views/cadastro.php");
    exit();
}