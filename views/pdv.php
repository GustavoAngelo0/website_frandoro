<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - FRENTE DE CAIXA (PDV)
 * ============================================================================
 * Arquivo: views/pdv.php
 * Localização: /views/pdv.php
 * Propósito: Interface minimalista de vendas do balcão. Permite selecionar produtos,
 *            calcular subtotais por peso (kg) ou unidade (un) e enviar a venda.
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

session_start();

// Trava de segurança: obriga login ativo
require_once '../config/autenticacao.php';

// Conexão com o banco de dados
require_once '../config/conexao.php';

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Busca o catálogo de produtos cadastrados APENAS DO USUÁRIO LOGADO para montar a grade de itens
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = :usuario_id ORDER BY nome ASC");
$stmt->execute([':usuario_id' => $usuario_id]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDV - PontoCaixa</title>

    <!-- Bootstrap 5, FontAwesome 6 & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-page: #FAF8F5;
            --bg-card: #FFFFFF;
            --border-subtle: #ECE8E1;
            --border-hover: #DCD6CC;
            --text-main: #2D2B2A;
            --text-muted: #7E7A75;
            --brand-orange: #d95d1e;
            --brand-orange-hover: #c44e13;
            --brand-orange-light: #fdf2eb;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-minimal {
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-subtle);
            padding: 1rem 0;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: var(--text-main);
            text-decoration: none;
        }

        .text-orange {
            color: var(--brand-orange);
        }

        .card-custom {
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            padding: 1.25rem;
        }

        .product-item {
            border: 1px solid var(--border-subtle);
            border-radius: 10px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.15s ease;
            background-color: #FFFFFF;
        }

        .product-item:hover {
            border-color: var(--brand-orange);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .btn-brand {
            background-color: var(--brand-orange);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.75rem 1.2rem;
            width: 100%;
            transition: background-color 0.15s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #FFFFFF;
        }

        .form-select, .form-control {
            background-color: var(--bg-page);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.6rem;
            font-size: 0.9rem;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 0.25rem rgba(217, 93, 30, 0.15);
        }

        .badge-unit {
            background-color: var(--brand-orange-light);
            color: var(--brand-orange);
            font-weight: 700;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
        }

        .cart-table td, .cart-table th {
            padding: 0.6rem 0.4rem;
            font-size: 0.9rem;
        }

        /* RODAPÉ SIMPLIFICADO */
        footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-subtle);
            padding: 1.25rem 0;
            color: #777777;
            margin-top: auto;
        }
    </style>
</head>

<body>

    <!-- Navegação -->
    <nav class="navbar-minimal mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="brand-logo" href="painel.php">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <a href="painel.php" class="btn btn-sm btn-outline-secondary rounded-2 fw-semibold">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container py-2 flex-grow-1" style="max-width: 1100px;">
        <div class="row g-4">
            
            <!-- Coluna da Esquerda: Catálogo de Produtos para seleção -->
            <div class="col-md-7">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0" style="letter-spacing: -0.02em;">
                        <i class="fa-solid fa-boxes-stacked me-2 text-orange"></i>Catálogo de Produtos
                    </h5>
                    <span class="badge bg-light text-muted border fw-semibold"><?= count($produtos) ?> item(ns)</span>
                </div>

                <?php if (empty($produtos)): ?>
                    <div class="card-custom text-center py-5">
                        <i class="fa-solid fa-box-open fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Nenhum produto cadastrado no catálogo.</p>
                        <a href="produtos.php" class="btn btn-sm btn-brand mt-3 d-inline-block" style="width: auto;">Cadastrar Produtos</a>
                    </div>
                <?php else: ?>
                    <div class="row g-2">
                        <?php foreach ($produtos as $prod): ?>
                            <div class="col-6 col-md-4">
                                <!-- Ao clicar no card, adiciona o produto no carrinho JS -->
                                <div class="product-item h-100 d-flex flex-column justify-content-between" onclick="adicionarAoCarrinho(<?= $prod['id'] ?>, '<?= htmlspecialchars(addslashes($prod['nome'])) ?>', <?= $prod['preco'] ?>, '<?= $prod['tipo_unidade'] ?>')">
                                    <div>
                                        <span class="badge-unit me-1"><?= strtoupper($prod['tipo_unidade']) ?></span>
                                        <div class="fw-bold mt-2 mb-1 text-truncate" title="<?= htmlspecialchars($prod['nome']) ?>"><?= htmlspecialchars($prod['nome']) ?></div>
                                    </div>
                                    <div class="text-orange fw-bold small mt-2">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Coluna da Direita: Carrinho e Finalização -->
            <div class="col-md-5">
                <div class="card-custom">
                    <h5 class="fw-bold mb-3" style="letter-spacing: -0.02em;">
                        <i class="fa-solid fa-cart-shopping me-2 text-orange"></i>Resumo da Venda
                    </h5>

                    <div class="table-responsive mb-3" style="max-height: 320px; overflow-y: auto;">
                        <table class="table cart-table align-middle">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Item</th>
                                    <th style="width: 85px;">Qtd/Kg</th>
                                    <th class="text-end">Total</th>
                                    <th style="width: 30px;"></th>
                                </tr>
                            </thead>
                            <!-- Tabela montada dinamicamente via JavaScript -->
                            <tbody id="listaCarrinho">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4 small">
                                        <i class="fa-solid fa-basket-shopping d-block fa-2x mb-2 opacity-50"></i>
                                        Nenhum item adicionado.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Forma de Pagamento</span>
                            <select id="formaPagamento" class="form-select w-auto form-select-sm fw-semibold">
                                <option value="dinheiro">Dinheiro</option>
                                <option value="pix">PIX</option>
                                <option value="cartao">Cartão</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-4 text-orange" id="valorTotalTexto">R$ 0,00</span>
                        </div>
                    </div>

                    <button class="btn btn-brand" onclick="finalizarVenda()">
                        <i class="fa-solid fa-check me-1"></i> Concluir Venda
                    </button>
                </div>
            </div>

        </div>
    </main>

    <!-- RODAPÉ CLEAN -->
    <footer class="text-center">
        <div class="container">
            <p class="small text-muted mb-0">
                Desenvolvido por 
                <a href="https://github.com/GustavoAngelo0" target="_blank" class="text-orange fw-semibold text-decoration-none ms-1">
                    <i class="fa-brands fa-github me-1"></i>GustavoAngelo0
                </a>
            </p>
        </div>
    </footer>

    <!-- Script de manipulação do carrinho e AJAX -->
    <script>
        // Array global que armazena os itens selecionados
        let carrinho = [];

        // Adiciona um item ao carrinho ou incrementa sua quantidade
        function adicionarAoCarrinho(id, nome, preco, tipo) {
            const itemExistente = carrinho.find(item => item.id === id);
            
            if (itemExistente) {
                itemExistente.quantidade += (tipo === 'kg' ? 0.5 : 1);
            } else {
                carrinho.push({
                    id: id,
                    nome: nome,
                    preco: preco,
                    quantidade: (tipo === 'kg' ? 1.0 : 1),
                    tipo: tipo
                });
            }
            atualizarCarrinho();
        }

        // Remove um item do carrinho pelo índice
        function removerItem(index) {
            carrinho.splice(index, 1);
            atualizarCarrinho();
        }

        // Altera a quantidade de um item diretamente pelo input
        function alterarQuantidade(index, valor) {
            const novaQtd = parseFloat(valor);
            if (novaQtd > 0) {
                carrinho[index].quantidade = novaQtd;
            }
            atualizarCarrinho();
        }

        // Renderiza a tabela do carrinho e calcula o total acumulado
        function atualizarCarrinho() {
            const tbody = document.getElementById('listaCarrinho');
            const totalTexto = document.getElementById('valorTotalTexto');
            
            if (carrinho.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4 small">
                            <i class="fa-solid fa-basket-shopping d-block fa-2x mb-2 opacity-50"></i>
                            Nenhum item adicionado.
                        </td>
                    </tr>`;
                totalTexto.innerText = 'R$ 0,00';
                return;
            }

            let html = '';
            let totalGeral = 0;

            carrinho.forEach((item, index) => {
                const subtotal = item.preco * item.quantidade;
                totalGeral += subtotal;

                html += `
                    <tr>
                        <td class="fw-semibold small">${item.nome}</td>
                        <td>
                            <input type="number" step="${item.tipo === 'kg' ? '0.05' : '1'}" min="0.05" class="form-control form-control-sm p-1 text-center" value="${item.quantidade}" onchange="alterarQuantidade(${index}, this.value)">
                        </td>
                        <td class="text-end fw-bold small">R$ ${subtotal.toFixed(2).replace('.', ',')}</td>
                        <td class="text-end">
                            <button class="btn btn-sm text-danger p-0 border-0 ms-1" onclick="removerItem(${index})" title="Remover item">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;
            totalTexto.innerText = `R$ ${totalGeral.toFixed(2).replace('.', ',')}`;
        }

        // Envia o carrinho para o controller via Fetch (AJAX/JSON)
        function finalizarVenda() {
            if (carrinho.length === 0) {
                alert('Adicione ao menos um produto no carrinho.');
                return;
            }

            const formaPagamento = document.getElementById('formaPagamento').value;

            fetch('../controllers/venda_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    itens: carrinho,
                    forma_pagamento: formaPagamento
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    alert('Venda realizada e estoque atualizado com sucesso!');
                    carrinho = [];
                    atualizarCarrinho();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(() => alert('Erro de comunicação com o servidor.'));
        }
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/cookie_banner.php'; ?>
</body>
</html>