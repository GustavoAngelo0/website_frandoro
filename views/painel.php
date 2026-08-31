<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - PAINEL DE CONTROLE PRINCIPAL
 * ============================================================================
 * Arquivo: views/painel.php
 * Propósito: Centralizar o acesso aos módulos do sistema (PDV, Produtos,
 *            Caixa Diário, Encomendas e Histórico).
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

session_start();
require_once '../config/autenticacao.php';
require_once '../config/conexao.php';

$nomeUsuario = $_SESSION['usuario_nome'] ?? $_SESSION['nome'] ?? 'Operador';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - PontoCaixa</title>

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
            border-radius: 14px;
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.04);
        }

        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background-color: #FDF5EF;
            color: var(--brand-orange);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-brand {
            background-color: var(--brand-orange);
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-weight: 600;
        }

        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #FFFFFF;
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
    <nav class="navbar-minimal mb-5">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="brand-logo" href="painel.php">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa-regular fa-user me-1 text-muted"></i> <?= htmlspecialchars($nomeUsuario) ?>
                </span>
                <a href="../controllers/logout.php" class="btn btn-sm btn-outline-danger rounded-2 fw-semibold">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container mb-5 flex-grow-1" style="max-width: 1100px;">
        <div class="mb-4 text-center text-md-start">
            <h2 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Painel de Controle</h2>
            <p class="text-muted small mb-0">Selecione o módulo que deseja operar no momento.</p>
        </div>

        <div class="row g-4 justify-content-center">
            
            <!-- Card 1: PDV / Vendas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box me-3">
                                <i class="fa-solid fa-cash-register fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">PDV / Vendas</h5>
                        </div>
                        <p class="text-muted small mb-4">
                            Registro rápido de pedidos, balança por peso ou unidade e emissão de comanda.
                        </p>
                    </div>
                    <a href="pdv.php" class="btn btn-brand w-100 text-center text-decoration-none">Acessar PDV</a>
                </div>
            </div>

            <!-- Card 2: Caixa Diário -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box me-3">
                                <i class="fa-solid fa-wallet fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Caixa Diário</h5>
                        </div>
                        <p class="text-muted small mb-4">
                            Abertura e fechamento de turno, sangrias e conferência de valores (Dinheiro, Pix, Cartão).
                        </p>
                    </div>
                    <a href="caixa.php" class="btn btn-brand w-100 text-center text-decoration-none">Abrir Caixa</a>
                </div>
            </div>

            <!-- Card 3: Encomendas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box me-3">
                                <i class="fa-solid fa-calendar-check fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Encomendas</h5>
                        </div>
                        <p class="text-muted small mb-4">
                            Agendamento de pedidos, horários de retirada e controle de status do fim de semana.
                        </p>
                    </div>
                    <a href="encomendas.php" class="btn btn-brand w-100 text-center text-decoration-none">Acessar Agenda</a>
                </div>
            </div>

            <!-- Card 4: Produtos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box me-3">
                                <i class="fa-solid fa-boxes-stacked fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Produtos</h5>
                        </div>
                        <p class="text-muted small mb-4">
                            Cadastro de assados, acompanhamentos, preços e formas de venda (Kg ou Unidade).
                        </p>
                    </div>
                    <a href="produtos.php" class="btn btn-brand w-100 text-center text-decoration-none">Gerenciar</a>
                </div>
            </div>

            <!-- Card 5: Histórico de Vendas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box me-3">
                                <i class="fa-solid fa-chart-line fa-lg"></i>
                            </div>
                            <h5 class="fw-bold mb-0">Histórico & Relatórios</h5>
                        </div>
                        <p class="text-muted small mb-4">
                            Consulte faturamento do dia, vendas por período e relatório detalhado dos pedidos.
                        </p>
                    </div>
                    <a href="historico.php" class="btn btn-brand w-100 text-center text-decoration-none">Acessar Relatório</a>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/cookie_banner.php'; ?>
</body>
</html>