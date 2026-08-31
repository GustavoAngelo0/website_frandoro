<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - HISTÓRICO DE VENDAS AGRUPADO POR DIA
 * ============================================================================
 * Arquivo: views/historico.php
 */

session_start();

require_once '../config/autenticacao.php';
require_once '../config/conexao.php';

// Garante o ID do usuário logado na sessão
$usuarioId = $_SESSION['usuario_id'] ?? $_SESSION['id'] ?? null;

if (!$usuarioId) {
    header('Location: login.php');
    exit;
}

// ============================================================================
// FILTROS DE DATA (Padrão: Primeiro dia do mês atual até hoje)
// ============================================================================
$dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
$dataFim    = $_GET['data_fim']    ?? date('Y-m-d');

$dataInicioQuery = $dataInicio . ' 00:00:00';
$dataFimQuery    = $dataFim . ' 23:59:59';

// ============================================================================
// CONSULTA: RESUMO FINANCEIRO (KPIs do Usuário Logado)
// ============================================================================
$stmtKpi = $pdo->prepare("
    SELECT 
        COUNT(id) AS total_vendas,
        COALESCE(SUM(valor_total), 0) AS faturamento_total,
        COALESCE(AVG(valor_total), 0) AS ticket_medio
    FROM vendas 
    WHERE usuario_id = :usuario_id 
      AND data_venda BETWEEN :data_inicio AND :data_fim
");
$stmtKpi->execute([
    ':usuario_id' => $usuarioId,
    ':data_inicio' => $dataInicioQuery,
    ':data_fim' => $dataFimQuery
]);
$kpis = $stmtKpi->fetch(PDO::FETCH_ASSOC);

// KPI: Vendas de Hoje do Usuário Logado
$stmtHoje = $pdo->prepare("
    SELECT COALESCE(SUM(valor_total), 0) AS total_hoje 
    FROM vendas 
    WHERE usuario_id = :usuario_id 
      AND DATE(data_venda) = CURDATE()
");
$stmtHoje->execute([':usuario_id' => $usuarioId]);
$totalHoje = $stmtHoje->fetchColumn();

// ============================================================================
// CONSULTA: TODAS AS VENDAS DO USUÁRIO LOGADO
// ============================================================================
$stmtVendas = $pdo->prepare("
    SELECT v.*,
           v.data_venda AS data_venda_formatada,
           (SELECT COUNT(*) FROM itens_venda iv WHERE iv.venda_id = v.id) as total_itens
    FROM vendas v
    WHERE v.usuario_id = :usuario_id 
      AND v.data_venda BETWEEN :data_inicio AND :data_fim
    ORDER BY v.data_venda DESC
");
$stmtVendas->execute([
    ':usuario_id' => $usuarioId,
    ':data_inicio' => $dataInicioQuery,
    ':data_fim' => $dataFimQuery
]);
$vendas = $stmtVendas->fetchAll(PDO::FETCH_ASSOC);

// AGRUPAMENTO DAS VENDAS POR DIA
$vendasPorDia = [];
foreach ($vendas as $venda) {
    $dataChave = date('Y-m-d', strtotime($venda['data_venda']));
    if (!isset($vendasPorDia[$dataChave])) {
        $vendasPorDia[$dataChave] = [
            'data_formatada' => date('d/m/Y', strtotime($dataChave)),
            'total_dia' => 0,
            'qtd_vendas' => 0,
            'vendas' => []
        ];
    }
    $vendasPorDia[$dataChave]['total_dia'] += $venda['valor_total'];
    $vendasPorDia[$dataChave]['qtd_vendas']++;
    $vendasPorDia[$dataChave]['vendas'][] = $venda;
}

// Função auxiliar para buscar itens da venda
function buscarItensVenda($pdo, $vendaId) {
    $stmtItens = $pdo->prepare("
        SELECT iv.*, pr.tipo_unidade
        FROM itens_venda iv
        LEFT JOIN produtos pr ON iv.produto_id = pr.id
        WHERE iv.venda_id = :venda_id
    ");
    $stmtItens->execute([':venda_id' => $vendaId]);
    return $stmtItens->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Vendas - PontoCaixa</title>

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
            --brand-orange-light: #FDF5EF;
            --tag-blue-bg: #EBF3FA;
            --tag-blue-text: #1F5A92;
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

        .text-orange { color: var(--brand-orange); }

        .kpi-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 1.25rem;
            height: 100%;
        }

        .kpi-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .kpi-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0;
        }

        .form-control {
            background-color: var(--bg-page);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.5rem 0.8rem;
            font-size: 0.875rem;
        }

        .form-control:focus {
            background-color: #FFFFFF;
            border-color: var(--brand-orange);
            box-shadow: 0 0 0 3px rgba(217, 93, 30, 0.12);
        }

        .btn-brand {
            background-color: var(--brand-orange);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            font-size: 0.875rem;
        }

        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #FFFFFF;
        }

        /* Estilização da Sanfona (Accordion) */
        .accordion-item {
            border: 1px solid var(--border-subtle) !important;
            border-radius: 12px !important;
            margin-bottom: 1rem;
            overflow: hidden;
            background-color: var(--bg-card);
        }

        .accordion-button {
            background-color: var(--bg-card);
            box-shadow: none !important;
            padding: 1.1rem 1.25rem;
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--brand-orange-light);
            color: var(--text-main);
            border-bottom: 1px solid var(--border-subtle);
        }

        .accordion-button::after {
            margin-left: 0.5rem;
        }

        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-custom th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0.75rem 1rem;
            background-color: #FAF8F5;
        }

        .table-custom td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.88rem;
            vertical-align: middle;
        }

        .table-custom tr:last-child td {
            border-bottom: none;
        }

        .badge-payment {
            background-color: var(--tag-blue-bg);
            color: var(--tag-blue-text);
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            text-transform: capitalize;
        }

        .btn-action-view {
            color: var(--brand-orange);
            background-color: var(--brand-orange-light);
            border: 1px solid transparent;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-action-view:hover {
            background-color: var(--brand-orange);
            color: #FFFFFF;
        }

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

    <nav class="navbar-minimal mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="brand-logo" href="painel.php">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <a href="painel.php" class="btn btn-sm btn-outline-secondary rounded-2">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container py-2 flex-grow-1" style="max-width: 960px;">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Histórico de Vendas</h3>
                <p class="text-muted small mb-0">Acompanhe relatórios, faturamento e detalhes de cada pedido agrupados por dia.</p>
            </div>

            <form action="historico.php" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <div>
                    <input type="date" name="data_inicio" class="form-control" value="<?= htmlspecialchars($dataInicio) ?>" required>
                </div>
                <span class="text-muted small">até</span>
                <div>
                    <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($dataFim) ?>" required>
                </div>
                <button type="submit" class="btn btn-brand">
                    <i class="fa-solid fa-filter me-1"></i> Filtrar
                </button>
            </form>
        </div>

        <!-- Cards de KPIs -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-title"><i class="fa-solid fa-wallet text-orange me-1"></i> Faturamento</div>
                    <p class="kpi-value text-orange">R$ <?= number_format($kpis['faturamento_total'], 2, ',', '.') ?></p>
                    <span class="text-muted small">no período selecionado</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-title"><i class="fa-solid fa-shopping-bag me-1"></i> Vendas Hoje</div>
                    <p class="kpi-value">R$ <?= number_format($totalHoje, 2, ',', '.') ?></p>
                    <span class="text-muted small">total bruto do dia</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-title"><i class="fa-solid fa-receipt me-1"></i> Pedidos</div>
                    <p class="kpi-value"><?= $kpis['total_vendas'] ?></p>
                    <span class="text-muted small">vendas realizadas</span>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="kpi-card">
                    <div class="kpi-title"><i class="fa-solid fa-chart-line me-1"></i> Ticket Médio</div>
                    <p class="kpi-value">R$ <?= number_format($kpis['ticket_medio'], 2, ',', '.') ?></p>
                    <span class="text-muted small">valor médio/pedido</span>
                </div>
            </div>
        </div>

        <!-- LISTA DE VENDAS AGRUPADAS POR DIA (ACCORDION) -->
        <?php if (empty($vendasPorDia)): ?>
            <div class="card border-0 p-5 text-center shadow-sm rounded-3 mb-5" style="background-color: var(--bg-card); border: 1px solid var(--border-subtle) !important;">
                <i class="fa-solid fa-calendar-xmark fa-3x mb-3 text-muted opacity-50"></i>
                <h5 class="fw-bold">Nenhuma venda encontrada</h5>
                <p class="text-muted small mb-0">
                    Não existem vendas registradas para o seu usuário entre 
                    <strong><?= date('d/m/Y', strtotime($dataInicio)) ?></strong> e 
                    <strong><?= date('d/m/Y', strtotime($dataFim)) ?></strong>.
                </p>
            </div>
        <?php else: ?>
            <div class="accordion mb-5" id="accordionVendas">
                <?php 
                $index = 0; 
                $dataHoje = date('Y-m-d');
                foreach ($vendasPorDia as $dataSql => $grupo): 
                    $index++;
                    $isHoje = ($dataSql === $dataHoje);
                    $isOpen = ($index === 1); // Abre apenas o primeiro/mais recente por padrão
                ?>
                    <div class="accordion-item shadow-sm">
                        <h2 class="accordion-header" id="heading_<?= $index ?>">
                            <button 
                                class="accordion-button <?= $isOpen ? '' : 'collapsed' ?>" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapse_<?= $index ?>" 
                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" 
                                aria-controls="collapse_<?= $index ?>">
                                
                                <div class="d-flex justify-content-between align-items-center w-100 me-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-regular fa-calendar-days text-orange fs-5"></i>
                                        <span class="fw-bold fs-6 text-dark"><?= $grupo['data_formatada'] ?></span>
                                        <?php if ($isHoje): ?>
                                            <span class="badge bg-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">Hoje</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fa-solid fa-receipt me-1 text-muted"></i><?= $grupo['qtd_vendas'] ?> venda(s)
                                        </span>
                                        <span class="fw-bold text-orange fs-6">
                                            R$ <?= number_format($grupo['total_dia'], 2, ',', '.') ?>
                                        </span>
                                    </div>
                                </div>

                            </button>
                        </h2>

                        <div 
                            id="collapse_<?= $index ?>" 
                            class="accordion-collapse collapse <?= $isOpen ? 'show' : '' ?>" 
                            aria-labelledby="heading_<?= $index ?>" 
                            data-bs-parent="#accordionVendas">
                            
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table-custom">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>Hora</th>
                                                <th>Pagamento</th>
                                                <th>Itens</th>
                                                <th>Valor Total</th>
                                                <th class="text-end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($grupo['vendas'] as $venda): 
                                                $itens = buscarItensVenda($pdo, $venda['id']);
                                                $itensJson = htmlspecialchars(json_encode($itens), ENT_QUOTES, 'UTF-8');
                                                $horaExibicao = !empty($venda['data_venda_formatada']) ? date('H:i', strtotime($venda['data_venda_formatada'])) : 'N/D';
                                                $dataModal = !empty($venda['data_venda_formatada']) ? date('d/m/Y H:i', strtotime($venda['data_venda_formatada'])) : 'N/D';
                                            ?>
                                                <tr>
                                                    <td class="fw-bold">#<?= str_pad($venda['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                                    <td>
                                                        <i class="fa-regular fa-clock text-muted me-1 small"></i>
                                                        <?= $horaExibicao ?> h
                                                    </td>
                                                    <td>
                                                        <span class="badge-payment">
                                                            <i class="fa-solid fa-credit-card me-1 small"></i>
                                                            <?= htmlspecialchars($venda['forma_pagamento'] ?? 'Não informada') ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-muted">
                                                        <?= $venda['total_itens'] ?> item(ns)
                                                    </td>
                                                    <td class="fw-bold text-dark">
                                                        R$ <?= number_format($venda['valor_total'], 2, ',', '.') ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <button 
                                                            type="button" 
                                                            class="btn-action-view border-0"
                                                            onclick='abrirModalDetalhes(<?= $venda['id'] ?>, "<?= $dataModal ?>", "<?= htmlspecialchars($venda['forma_pagamento'] ?? '') ?>", "<?= number_format($venda['valor_total'], 2, ',', '.') ?>", <?= $itensJson ?>)'>
                                                            <i class="fa-solid fa-eye me-1"></i> Ver Detalhes
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <!-- Modal de Detalhes da Venda -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; background-color: var(--bg-card);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalTituloPedido">Detalhes do Pedido</h5>
                        <p class="text-muted small mb-0" id="modalSubtituloPedido"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm align-middle">
                            <thead class="border-bottom">
                                <tr class="text-muted small">
                                    <th>Item</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Preço Un.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="modalTabelaItens"></tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top">
                        <span class="fw-bold text-muted">Forma de Pagamento:</span>
                        <span class="fw-semibold" id="modalFormaPagamento"></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="fw-bold fs-5">Total Pago:</span>
                        <span class="fw-bold fs-5 text-orange" id="modalValorTotal"></span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-subtle);">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function abrirModalDetalhes(id, data, formaPagamento, valorTotal, itens) {
            document.getElementById('modalTituloPedido').innerText = 'Pedido #' + String(id).padStart(5, '0');
            document.getElementById('modalSubtituloPedido').innerText = 'Realizado em ' + data;
            document.getElementById('modalFormaPagamento').innerText = formaPagamento || 'Não informada';
            document.getElementById('modalValorTotal').innerText = 'R$ ' + valorTotal;

            const tbody = document.getElementById('modalTabelaItens');
            tbody.innerHTML = '';

            if (!itens || itens.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Nenhum detalhe de item encontrado.</td></tr>';
            } else {
                itens.forEach(item => {
                    const tr = document.createElement('tr');
                    const preco = parseFloat(item.preco_unitario || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    const subtotal = parseFloat(item.subtotal || (item.quantidade * item.preco_unitario)).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    const tipoUnidade = item.tipo_unidade || 'un';
                    const qtd = parseFloat(item.quantidade).toLocaleString('pt-BR', {minimumFractionDigits: (tipoUnidade === 'kg' ? 3 : 0)});

                    tr.innerHTML = `
                        <td class="fw-semibold">${item.nome_produto || 'Produto Removido'}</td>
                        <td class="text-center">${qtd} ${tipoUnidade}</td>
                        <td class="text-end text-muted">R$ ${preco}</td>
                        <td class="text-end fw-bold">R$ ${subtotal}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
            modal.show();
        }
    </script>
<?php include 'includes/cookie_banner.php'; ?>
</body>
</html>