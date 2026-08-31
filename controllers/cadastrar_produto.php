<?php
/**
 * Arquivo: controllers/cadastrar_produto.php
 * Propósito: Validar e cadastrar um novo produto/assado no banco de dados frandoro.
 * Funcionalidade: Processa os campos enviados, formata os valores decimais e efetua a inserção.
 */

// 1. Inclui o arquivo de conexão PDO com o banco
require_once '../config/conexao.php';

// 2. Inicia o gerenciador de sessões
session_start();

// 3. Trava de Segurança: Impede execuções por usuários não autenticados
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    header("Location: ../views/login.php");
    exit;
}

// 4. Recebe a requisição formulada via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 5. Limpa e captura os inputs do usuário
    $nome          = trim($_POST['nome']);
    $preco         = floatval($_POST['preco']);
    $unidadeMedida = trim($_POST['unidade_medida']);

    // 6. Validação dos dados recebidos
    if (empty($nome) || $preco <= 0 || !in_array($unidadeMedida, ['unidade', 'kg'])) {
        $_SESSION['erro_produto'] = "Preencha todos os campos corretamente. O preço deve ser maior que zero.";
        header("Location: ../views/produtos.php");
        exit;
    }

    try {
        // 7. Prepara a consulta SQL parametrizada para evitar SQL Injection
        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, unidade_medida) VALUES (:nome, :preco, :unidade_medida)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':preco', $preco);
        $stmt->bindParam(':unidade_medida', $unidadeMedida);
        
        // 8. Executa a inserção no banco de dados
        $stmt->execute();

        $_SESSION['sucesso_produto'] = "Produto '" . htmlspecialchars($nome) . "' cadastrado com sucesso!";
        header("Location: ../views/produtos.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['erro_produto'] = "Erro ao cadastrar produto no banco: " . $e->getMessage();
        header("Location: ../views/produtos.php");
        exit;
    }
} else {
    // Redireciona se houver tentativa de acesso direto via URL
    header("Location: ../views/produtos.php");
    exit;
}
?>