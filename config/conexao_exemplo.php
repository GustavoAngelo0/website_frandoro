<?php
/**
 * Arquivo de Exemplo de Conexão com o Banco de Dados (PontoCaixa / Frand'oro)
 * 
 * INSTRUÇÕES DE USO:
 * 1. Copie este arquivo e renomeie a cópia para "conexao.php" na mesma pasta (config/).
 * 2. Preencha os dados abaixo com as credenciais do seu servidor MySQL local.
 * 3. O arquivo "conexao.php" real está no .gitignore e não subirá para o GitHub.
 */

// Parâmetros de Conexão com o Banco de Dados
$host    = 'localhost';    // Host do servidor (Ex: localhost ou 127.0.0.1)
$usuario = 'root';         // Usuário do MySQL (No XAMPP o padrão é 'root')
$senha   = '';             // Senha do MySQL (No XAMPP o padrão é vazio '')
$banco   = 'frandoro';     // Nome do banco de dados (Importado via bd_frandoro.sql)
$porta   = 3306;           // Porta padrão do MySQL (Normalmente 3306)

// Exemplo de Conexão usando PDO (Recomendado)
try {
    $pdo = new PDO("mysql:host={$host};port={$porta};dbname={$banco};charset=utf8mb4", $usuario, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
}

/* 
// Caso seu projeto utilize MySQLi em vez de PDO, descomente o trecho abaixo:
$conexao = mysqli_connect($host, $usuario, $senha, $banco, $porta);

if (!$conexao) {
    die("Falha na Conexão: " . mysqli_connect_error());
}
mysqli_set_charset($conexao, "utf8mb4");
*/
?>
