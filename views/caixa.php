<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - MÓDULO COMPLETO DE CAIXA DIÁRIO
 * ============================================================================
 * Arquivo: views/caixa.php
 * Propósito: Gerenciamento completo do caixa físico do dia:
 *            - Abertura e Fechamento de Turno
 *            - Suprimento (Entrada de dinheiro) e Sangria (Retirada)
 *            - Resumo detalhado por forma de pagamento (Dinheiro, Pix, Cartão)
 *            - Histórico de movimentações do caixa atual
 */

// 1. Trava de segurança da sessão
require_once '../config/autenticacao.php';

// 2. Conexão com o banco de dados
require_once '../config/conexao.php';

// Variáveis de estado do caixa
$caixaAberto = false;
$dadosCaixa  = null;

// Totais do Turno
$totalDinheiro   = 0.00;
$totalPix        = 0.00;
$totalCartao     = 0.00;
$totalSangrias   = 0.00;
$totalSuprimentos= 0.00;
$faturamentoTotal= 0.00; // Soma de Dinheiro + Pix + Cartão
$saldoEmCaixa    = 0.00; // Troco Inicial + Suprimentos + Dinheiro Vendas - Sangrias
$movimentacoes   = [];

try {
    // Busca o caixa atualmente aberto
    $stmt = $pdo->prepare("SELECT * FROM caixa WHERE status = 'aberto' ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $dadosCaixa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dadosCaixa) {
        $caixaAberto = true;
        $caixaId = $dadosCaixa['id'];

        // Somar Vendas por Método de Pagamento do Caixa Atual (se a tabela vendas existir)
        try {
            $stmtVendas = $pdo->prepare("
                SELECT forma_pagamento, SUM(valor_total) as total 
                FROM vendas 
                WHERE caixa_id = :caixa_id 
                GROUP BY forma_pagamento
            ");
            $stmtVendas->execute([':caixa_id' => $caixaId]);
            while ($row = $stmtVendas->fetch(PDO::FETCH_ASSOC)) {
                $forma = strtolower($row['forma_pagamento']);
                if ($forma === 'dinheiro') $totalDinheiro = floatval($row['total']);
                if ($forma === 'pix')      $totalPix      = floatval($row['total']);
                if ($forma === 'cartao' || $forma === 'cartão') $totalCartao = floatval($row['total']);
            }
        } catch (PDOException $e) { /* Tabela vendas ainda vazia ou ausente */ }

        // Cálculo do Faturamento Total do Turno
        $faturamentoTotal = $totalDinheiro + $totalPix + $totalCartao;

        // Somar Sangrias e Suprimentos
        try {
            $stmtMov = $pdo->prepare("SELECT * FROM movimentacoes_caixa WHERE caixa_id = :caixa_id ORDER BY id DESC");
            $stmtMov->execute([':caixa_id' => $caixaId]);
            $movimentacoes = $stmtMov->fetchAll(PDO::FETCH_ASSOC);

            foreach ($movimentacoes as $mov) {
                if ($mov['tipo'] === 'sangria') {
                    $totalSangrias += floatval($mov['valor']);
                } elseif ($mov['tipo'] === 'suprimento') {
                    $totalSuprimentos += floatval($mov['valor']);
                }
            }
        } catch (PDOException $e) { /* Tabela movimentacoes_caixa ainda ausente */ }

        // Cálculo do saldo em espécie disponível na gaveta
        $valorInicial = floatval($dadosCaixa['valor_inicial']);
        $saldoEmCaixa = ($valorInicial + $totalDinheiro + $totalSuprimentos) - $totalSangrias;
    }
} catch (PDOException $e) {
    $caixaAberto = false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PontoCaixa - Caixa Diário</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #fbf9f5;
            min-height: 100vh;
            color: #2d2926;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            flex-direction: column;
        }
        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #ebd3c2;
            padding: 0.85rem 1.5rem;
        }
        .brand-logo {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            color: #2d2926;
            text-decoration: none;
        }
        .text-orange {
            color: #d95d1e;
        }
        .card-custom {
            background: #ffffff;
            border: 1px solid #ebd3c2;
            border-radius: 18px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(217, 93, 30, 0.03);
        }
        .badge-status {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .badge-aberto { background-color: #e6f4ea; color: #137333; }
        .badge-fechado { background-color: #fce8e6; color: #c5221f; }
        .btn-orange {
            background-color: #d95d1e;
            border: 1px solid #c44e13;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-orange:hover { background-color: #c44e13; color: #ffffff; }
        .stat-box {
            background-color: #fffaf7;
            border: 1px solid #f2c4a7;
            border-radius: 12px;
            padding: 1rem;
        }
        .stat-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1c1917;
        }
        /* RODAPÉ CLEAN */
        footer {
            background-color: #ffffff;
            border-top: 1px solid #ebd3c2;
            padding: 1.25rem 0;
            color: #777777;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Topbar com Identidade PontoCaixa -->
    <nav class="navbar navbar-custom mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <a class="brand-logo" href="../index.php">
                    <span>PONTO<span class="text-orange">CAIXA</span></span>
                </a>
                <span class="text-muted opacity-50 d-none d-sm-inline">|</span>
                <span class="fw-semibold small text-dark d-none d-sm-inline">
                    <i class="fa-solid fa-wallet text-warning me-1"></i> Operação de Caixa
                </span>
            </div>
            <a href="painel.php" class="btn btn-outline-secondary btn-sm rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Painel
            </a>
        </div>
    </nav>

    <div class="container mb-5 flex-grow-1">
        
        <!-- Alertas de Erro ou Sucesso -->
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!$caixaAberto): ?>
            <!-- TELA DE ABERTURA DE CAIXA -->
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card-custom text-center">
                        <div class="mb-3">
                            <span class="badge-status badge-fechado"><i class="fa-solid fa-lock me-1"></i> CAIXA FECHADO</span>
                        </div>
                        <h4 class="fw-bold mb-3">Abrir Turno de Trabalho</h4>
                        <p class="text-muted small mb-4">Informe o saldo inicial em espécie (troco) para iniciar as vendas do dia.</p>
                        
                        <form action="../controllers/processar_caixa.php" method="POST">
                            <input type="hidden" name="acao" value="abrir">
                            <div class="mb-3 text-start">
                                <label for="valor_inicial" class="form-label small fw-semibold">Troco Inicial (R$)</label>
                                <input type="number" step="0.01" min="0" name="valor_inicial" id="valor_inicial" class="form-control form-control-lg" placeholder="0,00" required autofocus>
                            </div>
                            <button type="submit" class="btn btn-orange w-100 py-2 mt-2">
                                <i class="fa-solid fa-door-open me-2"></i> Abrir Caixa Agora
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- PAINEL COM CAIXA ABERTO -->
            <div class="row g-4">
                
                <!-- Coluna da Esquerda: Resumo do Saldo -->
                <div class="col-md-7">
                    <div class="card-custom h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <span class="badge-status badge-aberto me-2"><i class="fa-solid fa-circle-check me-1"></i> ABERTO</span>
                                <span>Desde <?php echo (!empty($dadosCaixa['data_abertura']) && $dadosCaixa['data_abertura'] !== '0000-00-00 00:00:00') ? date('d/m/Y H:i', strtotime($dadosCaixa['data_abertura'])) : date('d/m/Y H:i'); ?></span>
                            </div>
                            <button class="btn btn-outline-danger btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalFecharCaixa">
                                <i class="fa-solid fa-lock me-1"></i> Fechar Caixa
                            </button>
                        </div>

                        <!-- Destaque: Faturamento Total do Turno -->
                        <div class="p-3 bg-light border rounded-3 mb-4">
                            <span class="text-muted small d-block mb-1">Faturamento Total do Turno</span>
                            <div class="display-6 fw-bold text-success">R$ <?php echo number_format($faturamentoTotal, 2, ',', '.'); ?></div>
                            <small class="text-muted">(Soma de Dinheiro + Pix + Cartão)</small>
                        </div>

                        <!-- Resumo por Modalidade de Pagamento -->
                        <h6 class="fw-bold mb-3">Vendas e Entradas do Turno</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <span class="text-muted small d-block">Dinheiro</span>
                                    <span class="stat-value">R$ <?php echo number_format($totalDinheiro, 2, ',', '.'); ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <span class="text-muted small d-block">Pix</span>
                                    <span class="stat-value">R$ <?php echo number_format($totalPix, 2, ',', '.'); ?></span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <span class="text-muted small d-block">Cartão</span>
                                    <span class="stat-value">R$ <?php echo number_format($totalCartao, 2, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Sangria e Suprimento -->
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-danger w-50" data-bs-toggle="modal" data-bs-target="#modalSangria">
                                <i class="fa-solid fa-minus-circle me-1"></i> Sangria (Retirada)
                            </button>
                            <button class="btn btn-outline-success w-50" data-bs-toggle="modal" data-bs-target="#modalSuprimento">
                                <i class="fa-solid fa-plus-circle me-1"></i> Suprimento (Entrada)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Coluna da Direita: Histórico de Movimentações -->
                <div class="col-md-5">
                    <div class="card-custom h-100">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-list-check me-2"></i> Histórico do Turno</h6>
                        
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-sm align-middle small">
                                <thead>
                                    <tr>
                                        <th>Hora</th>
                                        <th>Tipo</th>
                                        <th>Descrição</th>
                                        <th class="text-end">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-light">
                                        <td><?php echo date('H:i', strtotime($dadosCaixa['data_abertura'])); ?></td>
                                        <td><span class="badge bg-secondary">Abertura</span></td>
                                        <td>Troco Inicial</td>
                                        <td class="text-end fw-semibold">R$ <?php echo number_format($dadosCaixa['valor_inicial'], 2, ',', '.'); ?></td>
                                    </tr>
                                    <?php foreach ($movimentacoes as $m): ?>
                                        <tr>
                                            <td><?php echo date('H:i', strtotime($m['created_at'] ?? 'now')); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $m['tipo'] === 'sangria' ? 'danger' : 'success'; ?>">
                                                    <?php echo ucfirst($m['tipo']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($m['descricao']); ?></td>
                                            <td class="text-end fw-semibold">
                                                R$ <?php echo number_format($m['valor'], 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MODAL: SANGRIA -->
            <div class="modal fade" id="modalSangria" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="../controllers/processar_caixa.php" method="POST">
                            <input type="hidden" name="acao" value="movimentacao">
                            <input type="hidden" name="tipo" value="sangria">
                            <input type="hidden" name="caixa_id" value="<?php echo $caixaId; ?>">
                            
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold text-danger"><i class="fa-solid fa-minus-circle me-1"></i> Registrar Sangria</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Valor da Retirada (R$)</label>
                                    <input type="number" step="0.01" min="0.01" name="valor" class="form-control" placeholder="0,00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Motivo / Descrição</label>
                                    <input type="text" name="descricao" class="form-control" placeholder="Ex: Pagamento fornecedor, sangria de segurança" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-danger">Confirmar Sangria</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL: SUPRIMENTO -->
            <div class="modal fade" id="modalSuprimento" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="../controllers/processar_caixa.php" method="POST">
                            <input type="hidden" name="acao" value="movimentacao">
                            <input type="hidden" name="tipo" value="suprimento">
                            <input type="hidden" name="caixa_id" value="<?php echo $caixaId; ?>">
                            
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold text-success"><i class="fa-solid fa-plus-circle me-1"></i> Registrar Suprimento</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Valor da Entrada (R$)</label>
                                    <input type="number" step="0.01" min="0.01" name="valor" class="form-control" placeholder="0,00" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Motivo / Origem</label>
                                    <input type="text" name="descricao" class="form-control" placeholder="Ex: Adicional de troco" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Confirmar Entrada</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- MODAL: FECHAR CAIXA -->
            <div class="modal fade" id="modalFecharCaixa" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="../controllers/processar_caixa.php" method="POST">
                            <input type="hidden" name="acao" value="fechar">
                            <input type="hidden" name="caixa_id" value="<?php echo $caixaId; ?>">
                            
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-lock me-1"></i> Encerrar Caixa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p class="small text-muted mb-3">Confirme os valores totais apurados antes de fechar o turno:</p>
                                <ul class="list-group list-group-flush small mb-3">
                                    <li class="list-group-item d-flex justify-content-between"><span>Saldo Estimado Gaveta:</span> <strong>R$ <?php echo number_format($saldoEmCaixa, 2, ',', '.'); ?></strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Total em Pix:</span> <strong>R$ <?php echo number_format($totalPix, 2, ',', '.'); ?></strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Total em Cartão:</span> <strong>R$ <?php echo number_format($totalCartao, 2, ',', '.'); ?></strong></li>
                                </ul>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Observações de Fechamento (Opcional)</label>
                                    <textarea name="observacao" class="form-control" rows="2" placeholder="Ex: Conferido sem divergências"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Voltar</button>
                                <button type="submit" class="btn btn-danger">Confirmar Fechamento</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        <?php endif; ?>

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

    <!-- Script Bootstrap JS para suporte aos Modais -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>