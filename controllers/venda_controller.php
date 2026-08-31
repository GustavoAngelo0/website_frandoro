<?php
/**
 * ============================================================================
 * SISTEMA FRANDORO - CONTROLADOR DE VENDAS (PDV)
 * ============================================================================
 * Arquivo: controllers/venda_controller.php
 * Propósito: Recebe o carrinho e forma de pagamento via JSON do PDV, valida
 *            o caixa aberto e grava a venda e seus itens no banco.
 */

session_start();
header('Content-Type: application/json');

require_once '../config/conexao.php';

// 1. Valida a sessão do operador
$usuarioId = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if (!$usuarioId) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit();
}

// 2. Captura os dados JSON enviados pelo fetch()
$input = json_decode(file_get_contents('php://input'), true);

$itens          = $input['itens'] ?? [];
$formaPagamento = trim($input['forma_pagamento'] ?? 'Dinheiro');

if (empty($itens)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O carrinho está vazio.']);
    exit();
}

try {
    // 3. Localiza o caixa atualmente aberto
    $stmtCaixa = $pdo->query("SELECT id FROM caixa WHERE status = 'aberto' ORDER BY id DESC LIMIT 1");
    $caixa = $stmtCaixa->fetch(PDO::FETCH_ASSOC);

    if (!$caixa) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhum caixa aberto! Abra o caixa antes de realizar vendas.']);
        exit();
    }

    $caixaId = $caixa['id'];

    // 4. Calcula o total geral da venda somando os subtotais do carrinho
    $valorTotal = 0;
    foreach ($itens as $item) {
        $qtd   = floatval($item['quantidade'] ?? $item['qtd'] ?? 1);
        $preco = floatval($item['preco'] ?? $item['preco_unitario'] ?? 0);
        $sub   = floatval($item['subtotal'] ?? ($qtd * $preco));
        $valorTotal += $sub;
    }

    // 5. Inicia a transação no banco de dados
    $pdo->beginTransaction();

    // Insere o cabeçalho da venda
    $stmtVenda = $pdo->prepare("
        INSERT INTO vendas (caixa_id, usuario_id, valor_total, forma_pagamento, data_venda) 
        VALUES (:caixa_id, :usuario_id, :valor_total, :forma_pagamento, NOW())
    ");
    $stmtVenda->execute([
        ':caixa_id'        => $caixaId,
        ':usuario_id'      => $usuarioId,
        ':valor_total'     => $valorTotal,
        ':forma_pagamento' => $formaPagamento
    ]);

    $vendaId = $pdo->lastInsertId();

    // Insere os itens detalhados da comanda
    $stmtItem = $pdo->prepare("
        INSERT INTO itens_venda (venda_id, produto_id, nome_produto, quantidade, preco_unitario, subtotal) 
        VALUES (:venda_id, :produto_id, :nome_produto, :quantidade, :preco_unitario, :subtotal)
    ");

    foreach ($itens as $item) {
        $qtd   = floatval($item['quantidade'] ?? $item['qtd'] ?? 1);
        $preco = floatval($item['preco'] ?? $item['preco_unitario'] ?? 0);
        $sub   = floatval($item['subtotal'] ?? ($qtd * $preco));

        $stmtItem->execute([
            ':venda_id'       => $vendaId,
            ':produto_id'     => $item['id'] ?? $item['produto_id'] ?? null,
            ':nome_produto'   => $item['nome'] ?? $item['titulo'] ?? 'Produto',
            ':quantidade'     => $qtd,
            ':preco_unitario' => $preco,
            ':subtotal'       => $sub
        ]);
    }

    $pdo->commit();

    echo json_encode(['sucesso' => true, 'mensagem' => 'Venda realizada e registrada com sucesso!']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
}