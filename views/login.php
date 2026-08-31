<?php
/**
 * ============================================================================
 * SISTEMA DE GESTÃO E CAIXA DIÁRIO - TELA DE LOGIN
 * ============================================================================
 * Arquivo: views/login.php
 * Descrição: Interface de autenticação de usuários para acesso ao sistema.
 * Autor: Gustavo Angelo (https://github.com/GustavoAngelo0)
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Se o usuário já possui sessão válida, envia para o Painel
if (isset($_SESSION['usuario_id'])) {
    header("Location: painel.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PontoCaixa - Entrar</title>
    
    <!-- Dependências Externas: Bootstrap 5.3, FontAwesome 6 e Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Estilos Personalizados mantendo a identidade visual do PontoCaixa -->
    <style>
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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

        /* CARD DE AUTENTICAÇÃO / LOGIN */
        .login-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 15px 35px rgba(217, 93, 30, 0.06);
            width: 100%;
            max-width: 440px;
            margin: auto;
            transition: transform 0.3s ease;
        }

        /* FORMULÁRIOS E CAMPOS DE ENTRADA */
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--texto-dark);
            margin-bottom: 0.4rem;
        }

        .input-group-text {
            background-color: #fffaf7;
            border-color: var(--border-color);
            color: var(--orange-primary);
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .form-control {
            border-color: var(--border-color);
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--orange-primary);
            box-shadow: 0 0 0 0.25rem rgba(217, 93, 30, 0.15);
        }

        .input-group .form-control:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .btn-eye {
            border-color: var(--border-color);
            background-color: #ffffff;
            color: #777777;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .btn-eye:hover {
            color: var(--orange-primary);
            background-color: #fffaf7;
            border-color: var(--border-color);
        }

        /* BOTÕES PERSONALIZADOS */
        .btn-orange {
            background-color: var(--orange-primary);
            border: 1px solid var(--orange-hover);
            color: #ffffff;
            font-weight: 700;
            border-radius: 12px;
            padding: 0.85rem 1.5rem;
            width: 100%;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(217, 93, 30, 0.2);
        }

        .btn-orange:hover {
            background-color: var(--orange-hover);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(217, 93, 30, 0.3);
        }

        /* LINKS AUXILIARES */
        .link-orange {
            color: var(--orange-primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .link-orange:hover {
            color: var(--orange-hover);
            text-decoration: underline;
        }

        .link-back {
            color: #666666;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .link-back:hover {
            color: var(--orange-primary);
        }

        /* RODAPÉ SIMPLIFICADO */
        footer {
            background-color: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 0;
            color: #777777;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

    <!-- BARRA SUPERIOR SIMPLIFICADA COM BOTÃO VOLTAR -->
    <header class="py-3 px-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../index.php" class="brand-logo">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <a href="../index.php" class="link-back">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao início
            </a>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL / CARD DE LOGIN -->
    <main class="container my-auto py-4">
        <div class="login-card">
            
            <!-- Cabeçalho do Card -->
            <div class="text-center mb-4">
                <h2 class="fw-bold fs-3 text-dark mb-1">Acessar Conta</h2>
                <p class="text-muted small">Digite suas credenciais de acesso ao sistema</p>
            </div>

            <!-- EXIBIÇÃO DE ALERTAS DE ERRO -->
            <?php if (isset($_SESSION['mensagem']) || isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 py-2 px-3 small" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <?php 
                        echo $_SESSION['mensagem'] ?? $_SESSION['erro']; 
                        unset($_SESSION['mensagem'], $_SESSION['erro']);
                    ?>
                    <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- FORMULÁRIO ENVIANDO PARA O CONTROLADOR AUTENTICAR.PHP -->
            <form action="../controllers/autenticar.php" method="POST">
                
                <!-- Campo E-mail -->
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required autofocus>
                    </div>
                </div>

                <!-- Campo Senha com Botão de Exibir/Ocultar -->
                <div class="mb-4">
                    <label for="senha" class="form-label">Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required>
                        <button class="btn btn-eye" type="button" id="toggleSenha" title="Exibir/Ocultar Senha">
                            <i class="fa-solid fa-eye" id="iconeOlho"></i>
                        </button>
                    </div>
                </div>

                <!-- Botão de Envio do Formulário -->
                <button type="submit" class="btn btn-orange btn-lg fs-6">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Entrar
                </button>
            </form>

            <!-- Rodapé do Card com Link para Cadastro -->
            <div class="text-center mt-4 pt-3 border-top">
                <p class="small text-muted mb-0">
                    Ainda não tem conta? 
                    <a href="cadastro.php" class="link-orange ms-1">Cadastre-se</a>
                </p>
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT JS PERSONALIZADO -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleSenhaBtn = document.getElementById('toggleSenha');
            const campoSenha = document.getElementById('senha');
            const iconeOlho = document.getElementById('iconeOlho');

            if (toggleSenhaBtn && campoSenha && iconeOlho) {
                toggleSenhaBtn.addEventListener('click', function() {
                    const tipoAtual = campoSenha.getAttribute('type');
                    
                    if (tipoAtual === 'password') {
                        campoSenha.setAttribute('type', 'text');
                        iconeOlho.classList.remove('fa-eye');
                        iconeOlho.classList.add('fa-eye-slash');
                    } else {
                        campoSenha.setAttribute('type', 'password');
                        iconeOlho.classList.remove('fa-eye-slash');
                        iconeOlho.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
<?php include 'includes/cookie_banner.php'; ?>
</body>
</html>