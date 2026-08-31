<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - CONTROLADOR MULTI-AÇÕES DO CAIXA
 * ============================================================================
 * Arquivo: controllers/processar_caixa.php
 * Propósito: Processar Abertura, Fechamento, Sangrias e Suprimentos de Caixa.
 */

session_start();
require_once '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    // 1. ABERTURA DE CAIXA
    if ($acao === 'abrir') {
        $valorInicial = floatval($_POST['valor_inicial'] ?? 0);
        $usuarioId    = $_SESSION['usuario_id'] ?? $_SESSION['id'];

        try {
            $stmt = $pdo->prepare("INSERT INTO caixa (usuario_id, valor_inicial, status, data_abertura) VALUES (:user, :valor, 'aberto', NOW())");
            $stmt->execute([':user' => $usuarioId, ':valor' => $valorInicial]);
            $_SESSION['mensagem'] = "Caixa aberto com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao abrir caixa: " . $e->getMessage();
        }
        header("Location: ../views/caixa.php");
        exit();
    }

    // 2. REGISTRO DE SANGRIA / SUPRIMENTO
    if ($acao === 'movimentacao') {
        $caixaId   = intval($_POST['caixa_id'] ?? 0);
        $tipo      = $_POST['tipo'] ?? 'sangria';
        $valor     = floatval($_POST['valor'] ?? 0);
        $descricao = trim($_POST['descricao'] ?? '');

        try {
            // Cria a tabela de movimentações caso não exista no banco
            $pdo->exec("CREATE TABLE IF NOT EXISTS movimentacoes_caixa (
                id INT AUTO_INCREMENT PRIMARY KEY,
                caixa_id INT NOT NULL,
                tipo ENUM('sangria', 'suprimento') NOT NULL,
                valor DECIMAL(10,2) NOT NULL,
                descricao VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            $stmt = $pdo->prepare("INSERT INTO movimentacoes_caixa (caixa_id, tipo, valor, descricao) VALUES (:caixa, :tipo, :valor, :desc)");
            $stmt->execute([
                ':caixa' => $caixaId,
                ':tipo'  => $tipo,
                ':valor' => $valor,
                ':desc'  => $descricao
            ]);
            $_SESSION['mensagem'] = ucfirst($tipo) . " registrada com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao registrar movimentação: " . $e->getMessage();
        }
        header("Location: ../views/caixa.php");
        exit();
    }

    // 3. FECHAMENTO DE CAIXA
    if ($acao === 'fechar') {
        $caixaId    = intval($_POST['caixa_id'] ?? 0);
        $observacao = trim($_POST['observacao'] ?? '');

        try {
            $stmt = $pdo->prepare("UPDATE caixa SET status = 'fechado', data_fechamento = NOW() WHERE id = :id");
            $stmt->execute([':id' => $caixaId]);
            $_SESSION['mensagem'] = "Caixa encerrado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem'] = "Erro ao fechar caixa: " . $e->getMessage();
        }
        header("Location: ../views/caixa.php");
        exit();
    }
}