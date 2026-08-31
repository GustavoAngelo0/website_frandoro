<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - CONTROLLER DE ENCOMENDAS E AGENDAMENTOS
 * ============================================================================
 * Arquivo: controllers/encomenda_controller.php
 * Propósito: Receber requisições via JSON para criar, atualizar status ou
 *            excluir agendamentos de encomendas de cada usuário.
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

session_start();

// Garante acesso apenas para operadores autenticados
require_once '../config/autenticacao.php';

// Conexão PDO com o MySQL
require_once '../config/conexao.php';

header('Content-Type: application/json');

// Recebe o corpo da requisição enviada via fetch (JS)
$dadosEntrada = json_decode(file_get_contents('php://input'), true);

if (!$dadosEntrada) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados de entrada inválidos.']);
    exit;
}

$acao = $dadosEntrada['acao'] ?? '';
$usuarioId = $_SESSION['usuario_id'] ?? null;

if (!$usuarioId) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

try {
    // ------------------------------------------------------------------------
    // AÇÃO 1: CRIAR NOVA ENCOMENDA AGENDADA
    // ------------------------------------------------------------------------
    if ($acao === 'criar') {
        $clienteNome     = trim($dadosEntrada['cliente_nome'] ?? '');
        $clienteTelefone = trim($dadosEntrada['cliente_telefone'] ?? '');
        $dataRetirada    = $dadosEntrada['data_retirada'] ?? '';
        $horaRetirada    = $dadosEntrada['hora_retirada'] ?? '';
        $itens           = $dadosEntrada['itens'] ?? [];

        if (empty($clienteNome) || empty($dataRetirada) || empty($horaRetirada) || empty($itens)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha o nome, data, horário e inclua ao menos um produto.']);
            exit;
        }

        // Calcula o valor total da encomenda
        $valorTotal = 0.00;
        foreach ($itens as $item) {
            $valorTotal += floatval($item['preco']) * floatval($item['quantidade']);
        }

        $pdo->beginTransaction();

        // Grava o cabeçalho do pedido agendado vinculado ao usuario_id logado
        $stmtPedido = $pdo->prepare("
            INSERT INTO pedidos (usuario_id, cliente_nome, cliente_telefone, data_retirada, hora_retirada, status_encomenda, valor_total, criado_em)
            VALUES (:usuario_id, :cliente_nome, :cliente_telefone, :data_retirada, :hora_retirada, 'pendente', :valor_total, NOW())
        ");
        $stmtPedido->execute([
            ':usuario_id'       => $usuarioId,
            ':cliente_nome'     => $clienteNome,
            ':cliente_telefone' => $clienteTelefone,
            ':data_retirada'    => $dataRetirada,
            ':hora_retirada'    => $horaRetirada,
            ':valor_total'      => $valorTotal
        ]);

        $pedidoId = $pdo->lastInsertId();

        // Grava os itens vinculados ao pedido
        $stmtItem = $pdo->prepare("
            INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
            VALUES (:pedido_id, :produto_id, :quantidade, :preco_unitario)
        ");

        foreach ($itens as $item) {
            $stmtItem->execute([
                ':pedido_id'      => $pedidoId,
                ':produto_id'     => intval($item['id']),
                ':quantidade'     => floatval($item['quantidade']),
                ':preco_unitario' => floatval($item['preco'])
            ]);
        }

        $pdo->commit();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Encomenda agendada com sucesso!']);

    // ------------------------------------------------------------------------
    // AÇÃO 2: ATUALIZAR STATUS DA ENCOMENDA
    // ------------------------------------------------------------------------
    } elseif ($acao === 'atualizar_status') {
        $pedidoId   = intval($dadosEntrada['pedido_id'] ?? 0);
        $novoStatus = $dadosEntrada['status'] ?? '';

        $statusValidos = ['pendente', 'em_preparo', 'pronto', 'concluido', 'cancelado'];

        if ($pedidoId <= 0 || !in_array($novoStatus, $statusValidos)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Status ou ID do pedido é inválido.']);
            exit;
        }

        // Atualiza apenas se a encomenda pertencer ao usuário logado
        $stmtStatus = $pdo->prepare("UPDATE pedidos SET status_encomenda = :status WHERE id = :id AND usuario_id = :usuario_id");
        $stmtStatus->execute([
            ':status'     => $novoStatus,
            ':id'         => $pedidoId,
            ':usuario_id' => $usuarioId
        ]);

        echo json_encode(['sucesso' => true, 'mensagem' => 'Status atualizado com sucesso!']);

    // ------------------------------------------------------------------------
    // AÇÃO 3: EXCLUIR ENCOMENDA
    // ------------------------------------------------------------------------
    } elseif ($acao === 'excluir') {
        $pedidoId = intval($dadosEntrada['pedido_id'] ?? 0);

        if ($pedidoId <= 0) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do pedido é inválido.']);
            exit;
        }

        $pdo->beginTransaction();

        // Deleta os itens da encomenda primeiro
        $stmtItens = $pdo->prepare("
            DELETE ip FROM itens_pedido ip 
            INNER JOIN pedidos p ON ip.pedido_id = p.id 
            WHERE ip.pedido_id = :pedido_id AND p.usuario_id = :usuario_id
        ");
        $stmtItens->execute([
            ':pedido_id'  => $pedidoId,
            ':usuario_id' => $usuarioId
        ]);

        // Deleta o pedido garantindo o vínculo com o usuário logado
        $stmtPedido = $pdo->prepare("DELETE FROM pedidos WHERE id = :id AND usuario_id = :usuario_id");
        $stmtPedido->execute([
            ':id'         => $pedidoId,
            ':usuario_id' => $usuarioId
        ]);

        $pdo->commit();

        echo json_encode(['sucesso' => true, 'mensagem' => 'Encomenda excluída com sucesso!']);

    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação desconhecida.']);
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno no banco de dados: ' . $e->getMessage()]);
}