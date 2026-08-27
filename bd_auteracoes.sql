CREATE TABLE IF NOT EXISTS sessoes_caixa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    valor_abertura DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    valor_fechamento_calculado DECIMAL(10,2) DEFAULT NULL,
    valor_fechamento_informado DECIMAL(10,2) DEFAULT NULL,
    diferenca DECIMAL(10,2) DEFAULT NULL,
    status ENUM('aberto', 'fechado') NOT NULL DEFAULT 'aberto',
    data_abertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    data_fechamento DATETIME DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
