<?php
/**
 * ============================================================================
 * SISTEMA PONTOCAIXA - GESTÃO DE PRODUTOS (CRUD COMPLETO)
 * ============================================================================
 * Arquivo: views/produtos.php
 * Descrição: Permite cadastrar, listar, editar e excluir produtos do catálogo.
 */

session_start();

// Trava de segurança: impede acesso direto de usuários não autenticados
require_once '../config/autenticacao.php';

// Conexão com o banco de dados via PDO
require_once '../config/conexao.php';

// [SEGURANÇA / MULTI-TENANT] Pega a ID do usuário logado na sessão
$usuarioId = $_SESSION['usuario_id'] ?? 0;

$mensagemSucesso = null;
$mensagemErro = null;

// ============================================================================
// PROCESSAMENTO: EXCLUSÃO DE PRODUTO (GET)
// ============================================================================
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    
    if ($idExcluir > 0) {
        try {
            // [SEGURANÇA / MULTI-TENANT] Impede que um usuário exclua o produto de outro adicionando "AND usuario_id = :usuario_id"
            $stmtDel = $pdo->prepare("DELETE FROM produtos WHERE id = :id AND usuario_id = :usuario_id");
            $stmtDel->execute([
                ':id' => $idExcluir,
                ':usuario_id' => $usuarioId
            ]);
            
            header("Location: produtos.php?sucesso=excluido");
            exit;
        } catch (PDOException $e) {
            $mensagemErro = "Não foi possível excluir o produto. Ele pode estar vinculado a pedidos existentes.";
        }
    }
}

// ============================================================================
// PROCESSAMENTO: CADASTRO E EDIÇÃO (POST)
// ============================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // ------------------------------------------------------------------------
    // AÇÃO 1: CADASTRO DE NOVO PRODUTO
    // ------------------------------------------------------------------------
    if ($_POST['acao'] === 'cadastrar') {
        $nome = trim($_POST['nome']);
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $tipoUnidade = $_POST['tipo_unidade'] ?? 'unidade';
        $estoque = floatval(str_replace(',', '.', $_POST['quantidade_estoque'] ?? 0));

        if (!empty($nome) && $preco > 0) {
            // [SEGURANÇA / MULTI-TENANT] Salva a ID da conta junto ao produto (coluna usuario_id)
            $stmt = $pdo->prepare("
                INSERT INTO produtos (nome, preco, tipo_unidade, unidade_medida, quantidade_estoque, usuario_id) 
                VALUES (:nome, :preco, :tipo, :unidade, :estoque, :usuario_id)
            ");
            $stmt->execute([
                ':nome' => $nome,
                ':preco' => $preco,
                ':tipo' => $tipoUnidade,
                ':unidade' => $tipoUnidade,
                ':estoque' => $estoque,
                ':usuario_id' => $usuarioId
            ]);
            header("Location: produtos.php?sucesso=cadastrado");
            exit;
        } else {
            $mensagemErro = "Preencha o nome e um preço válido para cadastrar.";
        }
    }

    // ------------------------------------------------------------------------
    // AÇÃO 2: EDIÇÃO DE PRODUTO EXISTENTE
    // ------------------------------------------------------------------------
    if ($_POST['acao'] === 'editar') {
        $idEdit = intval($_POST['id']);
        $nome = trim($_POST['nome']);
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $tipoUnidade = $_POST['tipo_unidade'] ?? 'unidade';
        $estoque = floatval(str_replace(',', '.', $_POST['quantidade_estoque'] ?? 0));

        if ($idEdit > 0 && !empty($nome) && $preco > 0) {
            // [SEGURANÇA / MULTI-TENANT] Garante que a edição só ocorra no produto pertencente à conta logada
            $stmtEdit = $pdo->prepare("
                UPDATE produtos 
                SET nome = :nome, preco = :preco, tipo_unidade = :tipo, unidade_medida = :unidade, quantidade_estoque = :estoque 
                WHERE id = :id AND usuario_id = :usuario_id
            ");
            $stmtEdit->execute([
                ':nome' => $nome,
                ':preco' => $preco,
                ':tipo' => $tipoUnidade,
                ':unidade' => $tipoUnidade,
                ':estoque' => $estoque,
                ':id' => $idEdit,
                ':usuario_id' => $usuarioId
            ]);
            header("Location: produtos.php?sucesso=editado");
            exit;
        } else {
            $mensagemErro = "Dados inválidos para atualização do produto.";
        }
    }
}

// ============================================================================
// CONSULTA: LISTAGEM DOS PRODUTOS
// ============================================================================
// [SEGURANÇA / MULTI-TENANT] Traz apenas os produtos da conta que está logada atualmente
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE usuario_id = :usuario_id ORDER BY nome ASC");
$stmt->execute([':usuario_id' => $usuarioId]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - PontoCaixa</title>

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
            --brand-orange-light: #FDF5EF;
            --tag-green-bg: #EBF6ED;
            --tag-green-text: #276738;
            --tag-red-bg: #FCEBEA;
            --tag-red-text: #A62D2D;
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
            padding: 1.5rem;
        }

        .form-control, .form-select {
            background-color: var(--bg-page);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
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
            padding: 0.65rem 1.2rem;
            transition: background-color 0.15s ease;
        }

        .btn-brand:hover {
            background-color: var(--brand-orange-hover);
            color: #FFFFFF;
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
            font-size: 0.95rem;
            vertical-align: middle;
        }

        .badge-unit {
            background-color: var(--brand-orange-light);
            color: var(--brand-orange);
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
        }

        .btn-action-edit {
            color: var(--text-main);
            background-color: var(--bg-page);
            border: 1px solid var(--border-subtle);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-action-edit:hover {
            border-color: var(--border-hover);
            background-color: #F1ECE4;
        }

        .btn-action-delete {
            color: var(--tag-red-text);
            background-color: var(--tag-red-bg);
            border: 1px solid transparent;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-action-delete:hover {
            opacity: 0.85;
        }

        /* RODAPÉ CLEAN */
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
            <a class="brand-logo" href="../index.php">
                <span>PONTO<span class="text-orange">CAIXA</span></span>
            </a>
            <a href="painel.php" class="btn btn-sm btn-outline-secondary rounded-2">Voltar ao Painel</a>
        </div>
    </nav>

    <div class="container py-2 flex-grow-1" style="max-width: 920px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1" style="letter-spacing: -0.02em;">Produtos</h3>
                <p class="text-muted small mb-0">Gerencie preços, estoque e tipos de cobrança.</p>
            </div>
        </div>

        <!-- Feedback de Notificações -->
        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert mb-4" style="background: var(--tag-green-bg); color: var(--tag-green-text); border-radius: 8px; font-weight: 500;">
                <?php 
                    if ($_GET['sucesso'] === 'cadastrado') echo "Produto cadastrado com sucesso!";
                    if ($_GET['sucesso'] === 'editado') echo "Produto atualizado com sucesso!";
                    if ($_GET['sucesso'] === 'excluido') echo "Produto excluído com sucesso!";
                ?>
            </div>
        <?php endif; ?>

        <?php if ($mensagemErro): ?>
            <div class="alert mb-4" style="background: var(--tag-red-bg); color: var(--tag-red-text); border-radius: 8px; font-weight: 500;">
                <?= htmlspecialchars($mensagemErro) ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Cadastro de Novo Produto -->
        <div class="card-custom mb-4">
            <h6 class="fw-bold mb-3">Cadastrar Novo Item</h6>
            <form action="produtos.php" method="POST" class="row g-3">
                <input type="hidden" name="acao" value="cadastrar">
                
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Nome do Produto</label>
                    <input type="text" name="nome" class="form-control" placeholder="Ex: Frango Assado" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Preço (R$)</label>
                    <input type="text" name="preco" class="form-control" placeholder="00,00" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Cobrança Por</label>
                    <select name="tipo_unidade" class="form-select">
                        <option value="unidade">Unidade (un)</option>
                        <option value="kg">Quilo (kg)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Estoque Inicial</label>
                    <input type="text" name="quantidade_estoque" class="form-control" placeholder="0">
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-brand">Salvar Produto</button>
                </div>
            </form>
        </div>

        <!-- Tabela de Produtos Cadastrados -->
        <div class="card-custom p-0 overflow-hidden mb-5">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Estoque</th>
                        <th>Preço</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtos)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Nenhum produto cadastrado para sua conta.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtos as $prod): ?>
                            <tr>
                                <td class="fw-semibold"><?= htmlspecialchars($prod['nome']) ?></td>
                                <td>
                                    <span class="badge-unit">
                                        <?= $prod['tipo_unidade'] === 'kg' ? 'Kg' : 'Un' ?>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?= number_format($prod['quantidade_estoque'], 2, ',', '.') ?> <?= $prod['tipo_unidade'] ?>
                                </td>
                                <td class="fw-bold">
                                    R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                                </td>
                                <td class="text-end">
                                    <button 
                                        type="button" 
                                        class="btn-action-edit me-1 border-0" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditar"
                                        onclick="prepararEdicao(
                                            <?= $prod['id'] ?>, 
                                            '<?= htmlspecialchars(addslashes($prod['nome'])) ?>', 
                                            '<?= number_format($prod['preco'], 2, ',', '.') ?>', 
                                            '<?= $prod['tipo_unidade'] ?>', 
                                            '<?= number_format($prod['quantidade_estoque'], 2, ',', '.') ?>'
                                        )">
                                        Editar
                                    </button>
                                    
                                    <a 
                                        href="produtos.php?excluir=<?= $prod['id'] ?>" 
                                        class="btn-action-delete"
                                        onclick="return confirm('Tem certeza que deseja remover \'<?= htmlspecialchars(addslashes($prod['nome'])) ?>\' do catálogo?');">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Modal para Edição de Produto -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm" style="border-radius: 12px; background-color: var(--bg-card);">
                <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
                    <h5 class="modal-title fw-bold">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form action="produtos.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="editar">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nome do Produto</label>
                            <input type="text" name="nome" id="edit_nome" class="form-control" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Preço (R$)</label>
                                <input type="text" name="preco" id="edit_preco" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Cobrança Por</label>
                                <select name="tipo_unidade" id="edit_tipo_unidade" class="form-select">
                                    <option value="unidade">Unidade (un)</option>
                                    <option value="kg">Quilo (kg)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold">Quantidade em Estoque</label>
                            <input type="text" name="quantidade_estoque" id="edit_estoque" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--border-subtle);">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-2" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-brand">Salvar Alterações</button>
                    </div>
                </form>
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
        function prepararEdicao(id, nome, preco, tipo, estoque) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_preco').value = preco;
            document.getElementById('edit_tipo_unidade').value = tipo;
            document.getElementById('edit_estoque').value = estoque;
        }
    </script>
</body>
</html>