<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - PAINEL DE ENCOMENDAS E AGENDAMENTOS
 * ============================================================================
 * Arquivo: views/encomendas.php
 * Propósito: Exibir as encomendas agendadas do usuário logado ordenadas por horário,
 *            permitir alteração de status, exclusão e criação de novos agendamentos.
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

session_start();
require_once '../config/autenticacao.php';
require_once '../config/conexao.php';

$usuarioId = $_SESSION['usuario_id'] ?? 0;

// Busca EXCLUSIVAMENTE os produtos pertencentes à conta logada
$stmtProdutos = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = :usuario_id ORDER BY nome ASC");
$stmtProdutos->execute([':usuario_id' => $usuarioId]);
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);

// Busca APENAS as encomendas do usuário logado
$stmtEncomendas = $pdo->prepare("
    SELECT p.*, 
           GROUP_CONCAT(CONCAT(ip.quantidade, 'x ', prod.nome) SEPARATOR ' + ') AS descricao_itens
    FROM pedidos p
    LEFT JOIN itens_pedido ip ON p.id = ip.pedido_id
    LEFT JOIN produtos prod ON ip.produto_id = prod.id
    WHERE p.usuario_id = :usuario_id AND p.data_retirada IS NOT NULL
    GROUP BY p.id
    ORDER BY p.data_retirada ASC, p.hora_retirada ASC
");
$stmtEncomendas->execute([':usuario_id' => $usuarioId]);
$encomendas = $stmtEncomendas->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomendas - PontoCaixa</title>
    
    <!-- Bootstrap 5 CSS, FontAwesome 6 e Google Fonts -->
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
            --text-main: #2D2B2A;
            --text-muted: #7E7A75;
            --brand-orange: #d95d1e;
            --brand-orange-hover: #c44e13;
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
            border-radius: 12px;
            padding: 1.25rem;
        }

        .btn-brand {
            background-color: var(--brand-orange);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #FFFFFF;
        }

        /* Tags de Status Personalizadas */
        .status-badge {
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .status-pendente  { background-color: #FEF3C7; color: #92400E; }
        .status-em_preparo { background-color: #E0F2FE; color: #075985; }
        .status-pronto     { background-color: #DCFCE7; color: #166534; }
        .status-concluido  { background-color: #F3F4F6; color: #4B5563; }
        .status-cancelado  { background-color: #FEE2E2; color: #991B1B; }

        .time-chip {
            background-color: #FDF5EF;
            color: var(--brand-orange);
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.75rem 1rem;
        }

        .table-custom td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
            vertical-align: middle;
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

    <!-- Navegação Superior -->
    <nav class="navbar-minimal mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="painel.php" class="brand-logo">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <a href="painel.php" class="btn btn-sm btn-outline-secondary rounded-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
        </div>
    </nav>

    <main class="container py-2 flex-grow-1" style="max-width: 1000px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Agenda de Encomendas</h3>
                <p class="text-muted small mb-0">Gerencie a grade de pedidos e os horários de retirada do final de semana.</p>
            </div>
            <button class="btn btn-brand px-3 fw-semibold" onclick="abrirModalNovaEncomenda()">+ Nova Encomenda</button>
        </div>

        <!-- Tabela de Encomendas -->
        <div class="card-custom p-0 overflow-hidden mb-5">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Horário / Data</th>
                        <th>Cliente</th>
                        <th>Itens Encomendados</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($encomendas)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                Nenhuma encomenda agendada até o momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($encomendas as $enc): ?>
                            <tr>
                                <td>
                                    <span class="time-chip d-inline-block mb-1">
                                        <?= date('H:i', strtotime($enc['hora_retirada'])) ?>
                                    </span>
                                    <div class="text-muted small">
                                        <?= date('d/m/Y', strtotime($enc['data_retirada'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($enc['cliente_nome']) ?></div>
                                    <?php if (!empty($enc['cliente_telefone'])): ?>
                                        <?php 
                                            $telLimpo = preg_replace('/[^0-9]/', '', $enc['cliente_telefone']);
                                        ?>
                                        <a href="https://wa.me/55<?= $telLimpo ?>" target="_blank" class="text-success small text-decoration-none">
                                            <i class="fa-brands fa-whatsapp me-1"></i><?= htmlspecialchars($enc['cliente_telefone']) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td style="max-width: 250px;">
                                    <span class="small text-secondary">
                                        <?= htmlspecialchars($enc['descricao_itens'] ?? 'Nenhum item') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $enc['status_encomenda'] ?>">
                                        <?= str_replace('_', ' ', $enc['status_encomenda']) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold" style="color: var(--brand-orange);">
                                    R$ <?= number_format($enc['valor_total'], 2, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <select class="form-select form-select-sm d-inline-block w-auto" onchange="alterarStatus(<?= $enc['id'] ?>, this.value)">
                                            <option value="pendente" <?= $enc['status_encomenda'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                            <option value="em_preparo" <?= $enc['status_encomenda'] === 'em_preparo' ? 'selected' : '' ?>>Em Preparo</option>
                                            <option value="pronto" <?= $enc['status_encomenda'] === 'pronto' ? 'selected' : '' ?>>Pronto</option>
                                            <option value="concluido" <?= $enc['status_encomenda'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                            <option value="cancelado" <?= $enc['status_encomenda'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="excluirEncomenda(<?= $enc['id'] ?>)" title="Excluir Encomenda">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

    <!-- Modal para Cadastro de Nova Encomenda -->
    <div class="modal fade" id="modalEncomenda" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-sm" style="border-radius: 12px;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Nova Encomenda Agendada</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEncomenda">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Nome do Cliente *</label>
                                <input type="text" id="clienteNome" class="form-control" required placeholder="Ex: João Silva">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">WhatsApp / Telefone</label>
                                <input type="text" id="clienteTelefone" class="form-control" placeholder="(19) 99999-9999">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Data da Retirada *</label>
                                <input type="date" id="dataRetirada" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-semibold">Horário da Retirada *</label>
                                <input type="time" id="horaRetirada" class="form-control" required value="12:00">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 border-top pt-3">Selecione os Produtos</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Produto</th>
                                        <th style="width: 100px;">Preço Unit.</th>
                                        <th style="width: 110px;">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($produtos)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                Nenhum produto cadastrado nesta conta. Cadastre seus produtos na aba Produtos primeiro.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($produtos as $prod): ?>
                                            <tr>
                                                <td class="fw-semibold small"><?= htmlspecialchars($prod['nome']) ?></td>
                                                <td class="small text-muted">R$ <?= number_format($prod['preco'], 2, ',', '.') ?></td>
                                                <td>
                                                    <input type="number" 
                                                           step="<?= $prod['tipo_unidade'] === 'kg' ? '0.05' : '1' ?>" 
                                                           min="0" 
                                                           value="0" 
                                                           class="form-control form-control-sm qtd-produto text-center" 
                                                           data-id="<?= $prod['id'] ?>" 
                                                           data-nome="<?= htmlspecialchars($prod['nome']) ?>" 
                                                           data-preco="<?= $prod['preco'] ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-sm btn-brand px-4" onclick="salvarEncomenda()">Agendar Encomenda</button>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const modalEncomenda = new bootstrap.Modal(document.getElementById('modalEncomenda'));

        function abrirModalNovaEncomenda() {
            modalEncomenda.show();
        }

        function alterarStatus(pedidoId, novoStatus) {
            fetch('../controllers/encomenda_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    acao: 'atualizar_status',
                    pedido_id: pedidoId,
                    status: novoStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    location.reload();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(() => alert('Erro na requisição.'));
        }

        function excluirEncomenda(pedidoId) {
            if (!confirm('Tem certeza que deseja excluir esta encomenda?')) {
                return;
            }

            fetch('../controllers/encomenda_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    acao: 'excluir',
                    pedido_id: pedidoId
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    location.reload();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(() => alert('Erro de comunicação com o servidor.'));
        }

        function salvarEncomenda() {
            const clienteNome = document.getElementById('clienteNome').value.trim();
            const clienteTelefone = document.getElementById('clienteTelefone').value.trim();
            const dataRetirada = document.getElementById('dataRetirada').value;
            const horaRetirada = document.getElementById('horaRetirada').value;

            if (!clienteNome || !dataRetirada || !horaRetirada) {
                alert('Preencha os campos obrigatórios (Nome, Data e Horário).');
                return;
            }

            const inputs = document.querySelectorAll('.qtd-produto');
            const itens = [];

            inputs.forEach(input => {
                const qtd = parseFloat(input.value);
                if (qtd > 0) {
                    itens.push({
                        id: input.getAttribute('data-id'),
                        nome: input.getAttribute('data-nome'),
                        preco: parseFloat(input.getAttribute('data-preco')),
                        quantidade: qtd
                    });
                }
            });

            if (itens.length === 0) {
                alert('Informe a quantidade de pelo menos um produto.');
                return;
            }

            fetch('../controllers/encomenda_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    acao: 'criar',
                    cliente_nome: clienteNome,
                    cliente_telefone: clienteTelefone,
                    data_retirada: dataRetirada,
                    hora_retirada: horaRetirada,
                    itens: itens
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    alert(data.mensagem);
                    location.reload();
                } else {
                    alert('Erro: ' + data.mensagem);
                }
            })
            .catch(() => alert('Erro de comunicação com o servidor.'));
        }
    </script>
</body>
</html>