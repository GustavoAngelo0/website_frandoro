CREATE DATABASE IF NOT EXISTS loja_assados
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE loja_assados;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS caixa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_abertura DATETIME NOT NULL,
    data_fechamento DATETIME NULL,
    valor_inicial DECIMAL(10, 2) DEFAULT 0.00 NOT NULL,
    valor_final_dinheiro DECIMAL(10, 2) NULL,
    valor_total_pix DECIMAL(10, 2) NULL,
    valor_total_cartao DECIMAL(10, 2) NULL,
    status ENUM('aberto', 'fechado') DEFAULT 'aberto' NOT NULL,
    observacoes TEXT NULL,
    CONSTRAINT fk_caixa_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10, 2) NOT NULL,
    tipo_unidade ENUM('kg', 'unidade') DEFAULT 'kg' NOT NULL,
    quantidade_estoque DECIMAL(10, 3) DEFAULT 0.000 NOT NULL,
    ativo TINYINT(1) DEFAULT 1 NOT NULL,
    destaque_pdv TINYINT(1) DEFAULT 0 NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    caixa_id INT NULL,
    nome_cliente VARCHAR(100) NOT NULL,
    telefone_cliente VARCHAR(20) NULL,
    tipo_entrega ENUM('retirada', 'entrega') NOT NULL,
    endereco_entrega TEXT NULL,
    data_horario_agendado DATETIME NOT NULL,
    status_pedido ENUM('pendente', 'em_preparo', 'pronto', 'entregue', 'cancelado') DEFAULT 'pendente' NOT NULL,
    status_pagamento ENUM('pendente', 'pago') DEFAULT 'pendente' NOT NULL,
    metodo_pagamento ENUM('dinheiro', 'pix', 'cartao_credito', 'cartao_debito') NULL,
    valor_total DECIMAL(10, 2) DEFAULT 0.00 NOT NULL,
    observacoes TEXT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuarios FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_pedidos_caixa FOREIGN KEY (caixa_id) REFERENCES caixa(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade DECIMAL(10, 3) NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_itens_pedidos FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_produtos FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
) ENGINE=InnoDB;