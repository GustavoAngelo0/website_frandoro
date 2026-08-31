<?php
/**
 * ============================================================================
 * SISTEMA DE GESTÃO E CAIXA DIÁRIO - PÁGINA INICIAL (INDEX)
 * ============================================================================
 * Arquivo: index.php
 * Descrição: Landing page institucional e portal de entrada para a plataforma.
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

// Inicia a sessão PHP caso ainda não esteja iniciada no ambiente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * LÓGICA DE SESSÃO DO USUÁRIO
 * Verifica se existe uma variável de sessão ativa para identificar o usuário.
 * Retorna true se estiver logado, permitindo adaptar o texto/links dos botões.
 */
$usuarioLogado = isset($_SESSION['usuario_id']) || isset($_SESSION['usuario']) || isset($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PontoCaixa - Controle Total do Seu Caixa Diário</title>
    
    <!-- Dependências Externas: Bootstrap 5.3, FontAwesome 6 e Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Personalizado / Sistema de Cores e Estilos da Interface -->
    <style>
        /* DEFINIÇÃO DA PALETA DE CORES E VARIÁVEIS GLOBAIS */
        :root {
            --bg-principal: #fbf9f5;      /* Fundo suave em tom bege/creme */
            --texto-dark: #2d2926;         /* Cor primária para textos e títulos */
            --orange-primary: #d95d1e;     /* Tom principal da marca */
            --orange-hover: #c44e13;       /* Cor de destaque ao passar o mouse */
            --border-color: #ebd3c2;       /* Cor de borda leve e elegante */
            --stat-bg: #fffaf7;            /* Fundo de elementos em destaque */
        }

        body {
            background-color: var(--bg-principal);
            color: var(--texto-dark);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        /* TIPOGRAFIA E ESTILO DO LOGOTIPO */
        .brand-logo {
            font-weight: 800;
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            color: var(--texto-dark);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: opacity 0.2s ease;
        }

        .brand-logo:hover {
            color: var(--texto-dark);
            opacity: 0.9;
        }

        .text-orange {
            color: var(--orange-primary);
        }

        /* NAVEGAÇÃO E BARRA SUPERIOR */
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1.1rem 0;
        }

        .nav-link {
            color: #555555;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: var(--orange-primary);
        }

        /* BOTÕES PERSONALIZADOS */
        .btn-orange {
            background-color: var(--orange-primary);
            border: 1px solid var(--orange-hover);
            color: #ffffff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.75rem 1.6rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(217, 93, 30, 0.2);
        }

        .btn-orange:hover {
            background-color: var(--orange-hover);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(217, 93, 30, 0.3);
        }

        .btn-outline-custom {
            border: 1.5px solid var(--border-color);
            color: var(--texto-dark);
            font-weight: 600;
            border-radius: 12px;
            padding: 0.75rem 1.6rem;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .btn-outline-custom:hover {
            border-color: var(--orange-primary);
            color: var(--orange-primary);
            background: #ffffff;
            transform: translateY(-2px);
        }

        /* SEÇÃO HERO DE ENTRADA */
        .hero-section {
            padding: 5rem 0 4rem 0;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1px;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #666666;
            margin-top: 1.2rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        /* CARDS E QUADROS VISUAIS */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(217, 93, 30, 0.08);
            border-color: var(--orange-primary);
        }

        /* DEMONSTRAÇÃO VISUAL / MOCKUP DE TELA */
        .mockup-container {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(217, 93, 30, 0.08);
            position: relative;
        }

        .mockup-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.2rem;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 0.8rem;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot-red { background-color: #ff5f56; }
        .dot-yellow { background-color: #ffbd2e; }
        .dot-green { background-color: #27c93f; }

        .stat-box {
            background-color: var(--stat-bg);
            border: 1px solid #f2c4a7;
            border-radius: 14px;
            padding: 1rem;
        }

        .badge-status {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 700;
        }
        .badge-aberto { background-color: #e6f4ea; color: #137333; }

        /* SEÇÕES DE CONTEÚDO */
        .section-padding {
            padding: 5rem 0;
        }

        .section-title {
            font-weight: 800;
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }

        .icon-circle {
            width: 55px;
            height: 55px;
            background-color: var(--stat-bg);
            border: 1px solid #f2c4a7;
            color: var(--orange-primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1.2rem;
        }

        /* RODAPÉ E CRÉDITOS */
        footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 2.5rem 0;
            color: #777777;
        }
    </style>
</head>
<body>

    <!-- ==================================================================== -->
    <!-- CABEÇALHO / NAVEGAÇÃO PRINCIPAL                                      -->
    <!-- ==================================================================== -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <!-- Logotipo em formato de texto moderno -->
            <a class="brand-logo" href="index.php">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>

            <!-- Botão Hamburguer para visualização Mobile -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fa-solid fa-bars fs-4 text-dark"></i>
            </button>

            <!-- Menu de Links e Botões de Ação -->
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3 text-center">
                    <li class="nav-item"><a class="nav-link" href="#inicio">Início</a></li>
                    <li class="nav-item"><a class="nav-link" href="#sobre">Sobre o Sistema</a></li>
                    <li class="nav-item"><a class="nav-link" href="#recursos">Recursos</a></li>
                </ul>

                <!-- Ações de Autenticação -->
                <div class="d-flex justify-content-center gap-2 mt-3 mt-lg-0">
                    <?php if ($usuarioLogado): ?>
                        <a href="views/painel.php" class="btn btn-orange">
                            <i class="fa-solid fa-gauge-high me-2"></i> Ir para o Painel
                        </a>
                    <?php else: ?>
                        <a href="views/login.php" class="btn btn-outline-custom">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar
                        </a>
                        <a href="views/cadastro.php" class="btn btn-orange">
                            <i class="fa-solid fa-user-plus me-1"></i> Criar Conta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- ==================================================================== -->
    <!-- HERO SECTION (CHAMADA PRINCIPAL E PREVIEW DO PAINEL)                 -->
    <!-- ==================================================================== -->
    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill mb-3 shadow-sm">
                        <span class="badge bg-success rounded-circle p-1"> </span>
                        <small class="fw-bold text-dark">Sistema Operacional de Caixa Diário</small>
                    </div>
                    <h1 class="hero-title">
                        Controle Total do Seu Caixa em <span class="text-orange">Tempo Real</span>.
                    </h1>
                    <p class="hero-subtitle">
                        Gerencie abertura e fechamento de turnos, sangrias, suprimentos e vendas por Dinheiro, Pix e Cartão em uma interface intuitiva, rápida e segura.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <?php if ($usuarioLogado): ?>
                            <a href="views/caixa.php" class="btn btn-orange btn-lg">
                                <i class="fa-solid fa-cash-register me-2"></i> Operar Caixa Agora
                            </a>
                        <?php else: ?>
                            <a href="views/cadastro.php" class="btn btn-orange btn-lg">
                                <i class="fa-solid fa-rocket me-2"></i> Começar Gratuitamente
                            </a>
                            <a href="views/login.php" class="btn btn-outline-custom btn-lg">
                                <i class="fa-solid fa-lock me-2"></i> Acessar Conta
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mt-4 pt-3 border-top">
                        <div class="col-4">
                            <div class="fw-bold text-dark fs-4">100%</div>
                            <small class="text-muted">Conferência Exata</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-dark fs-4">0%</div>
                            <small class="text-muted">Erros de Caixa</small>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold text-dark fs-4">24/7</div>
                            <small class="text-muted">Disponível</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mockup-container">
                        <div class="mockup-header">
                            <span class="dot dot-red"></span>
                            <span class="dot dot-yellow"></span>
                            <span class="dot dot-green"></span>
                            <small class="text-muted ms-2 fw-semibold"><i class="fa-solid fa-shield-halved me-1 text-success"></i> PontoCaixa - Painel Operacional</small>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge-status badge-aberto"><i class="fa-solid fa-circle-check me-1"></i> TURNO ABERTO</span>
                                <small class="text-muted ms-2">Operador: Balcão Principal</small>
                            </div>
                            <span class="badge bg-light text-dark border">Hoje</span>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background-color: #fffaf7; border: 1px solid #f2c4a7;">
                            <span class="text-muted small d-block">Faturamento Total do Turno</span>
                            <div class="fs-2 fw-bold text-success">R$ 1.845,50</div>
                            <small class="text-muted">(Dinheiro + Pix + Cartão)</small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <small class="text-muted d-block">Dinheiro</small>
                                    <strong class="text-dark">R$ 540,00</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <small class="text-muted d-block">Pix</small>
                                    <strong class="text-dark">R$ 810,50</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box text-center">
                                    <small class="text-muted d-block">Cartão</small>
                                    <strong class="text-dark">R$ 495,00</strong>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-danger w-50" disabled><i class="fa-solid fa-minus-circle me-1"></i> Sangria</button>
                            <button class="btn btn-sm btn-outline-success w-50" disabled><i class="fa-solid fa-plus-circle me-1"></i> Suprimento</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================== -->
    <!-- SEÇÃO "SOBRE O SISTEMA"                                             -->
    <!-- ==================================================================== -->
    <section id="sobre" class="section-padding bg-white border-top border-bottom">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <span class="text-orange fw-bold text-uppercase small">Conheça a Plataforma</span>
                <h2 class="section-title mt-1">A solução simples e inteligente para o controle do seu balcão</h2>
                <p class="text-muted">
                    Desenvolvido para eliminar a complexidade dos processos manuais e planilhas confusas, permitindo total clareza no fluxo diário do seu negócio.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="icon-circle">
                            <i class="fa-solid fa-rocket"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Quem Somos</h5>
                        <p class="text-muted small mb-0">
                            Uma plataforma criada para transformar a rotina financeira de estabelecimentos comerciais, unindo agilidade na operação do balcão e segurança no fechamento dos turnos.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="icon-circle">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <h5 class="fw-bold mb-3">O Que Fazemos</h5>
                        <p class="text-muted small mb-0">
                            Centralizamos o movimento financeiro do seu balcão: abertura de turno, sangrias de segurança, suprimentos de troco, consolidação por modalidade e encerramento automatizado.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom h-100">
                        <div class="icon-circle">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Por Que Escolher</h5>
                        <p class="text-muted small mb-0">
                            Garantimos zero complicação para o operador e paz de espírito para o gestor, sabendo exatamente onde está cada centavo do faturamento ao final do expediente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================== -->
    <!-- SEÇÃO DE RECURSOS E FUNCIONALIDADES                                 -->
    <!-- ==================================================================== -->
    <section id="recursos" class="section-padding">
        <div class="container">
            <div class="text-center mb-5" style="max-width: 700px; margin: 0 auto;">
                <span class="text-orange fw-bold text-uppercase small">Funcionalidades do Caixa</span>
                <h2 class="section-title mt-1">Tudo o que seu balcão precisa em um só lugar</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card-custom text-center h-100">
                        <div class="icon-circle mx-auto">
                            <i class="fa-solid fa-door-open"></i>
                        </div>
                        <h6 class="fw-bold">Abertura de Turno</h6>
                        <p class="text-muted small mb-0">Definição rápida do troco inicial em espécie com registro de horário e responsável.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card-custom text-center h-100">
                        <div class="icon-circle mx-auto">
                            <i class="fa-solid fa-arrow-down-up-across-line"></i>
                        </div>
                        <h6 class="fw-bold">Sangria & Suprimento</h6>
                        <p class="text-muted small mb-0">Controle rigoroso de retiradas de gaveta e entradas adicionais com histórico auditável.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card-custom text-center h-100">
                        <div class="icon-circle mx-auto">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <h6 class="fw-bold">Múltiplos Pagamentos</h6>
                        <p class="text-muted small mb-0">Separação exata dos valores apurados em Dinheiro, Pix e Cartões de Crédito/Débito.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card-custom text-center h-100">
                        <div class="icon-circle mx-auto">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h6 class="fw-bold">Fechamento Seguro</h6>
                        <p class="text-muted small mb-0">Encerramento de caixa conferido com resumo detalhado de faturamento e observações.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==================================================================== -->
    <!-- CALL TO ACTION (CTA FINAL)                                           -->
    <!-- ==================================================================== -->
    <section class="container my-5">
        <div class="card-custom text-center py-5 px-4" style="background: linear-gradient(135deg, #ffffff, #fffaf7); border: 1.5px solid var(--border-color);">
            <h2 class="fw-bold mb-3">Pronto para profissionalizar a gestão do seu balcão?</h2>
            <p class="text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
                Comece a gerenciar seu caixa agora mesmo de forma simples, organizada e totalmente segura.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <?php if ($usuarioLogado): ?>
                    <a href="views/painel.php" class="btn btn-orange btn-lg">
                        <i class="fa-solid fa-gauge-high me-2"></i> Ir para o Painel
                    </a>
                <?php else: ?>
                    <a href="views/cadastro.php" class="btn btn-orange btn-lg">
                        <i class="fa-solid fa-user-plus me-2"></i> Criar Conta Grátis
                    </a>
                    <a href="views/login.php" class="btn btn-outline-custom btn-lg">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Fazer Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ==================================================================== -->
    <!-- RODAPÉ COM CRÉDITOS E LINK DO GITHUB                                 -->
    <!-- ==================================================================== -->
    <footer class="text-center">
        <div class="container">
            <div class="brand-logo mb-3 justify-content-center">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </div>
            
            <p class="small text-muted mb-2">
                &copy; <?php echo date('Y'); ?> PontoCaixa - Sistema de Gestão e Operação de Caixa Diário. Todos os direitos reservados.
            </p>
            
            <!-- Crédito do Desenvolvedor com Link para o GitHub GustavoAngelo0 -->
            <p class="small text-muted mb-0">
                Desenvolvido por 
                <a href="https://github.com/GustavoAngelo0" target="_blank" class="text-orange fw-semibold text-decoration-none ms-1">
                    <i class="fa-brands fa-github me-1"></i>Gustavo Angelo
                </a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>