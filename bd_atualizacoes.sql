-- ===================================================
-- SCRIPT DE ATUALIZAÇÕES DO BANCO DE DADOS (FRANDORO)
-- ===================================================

-- 1. Atualização na tabela 'produtos' (Unidade de medida: kg ou unidade)
ALTER TABLE produtos ADD COLUMN unidade_medida VARCHAR(20) NOT NULL DEFAULT 'unidade';

-- 2. Atualizações na tabela 'pedidos' (Total, forma de pagamento e data)
ALTER TABLE pedidos ADD COLUMN total DECIMAL(10,2) NOT NULL DEFAULT 0.00;
ALTER TABLE pedidos ADD COLUMN forma_pagamento VARCHAR(50) NOT NULL DEFAULT 'dinheiro';
ALTER TABLE pedidos ADD COLUMN data_venda DATETIME DEFAULT CURRENT_TIMESTAMP;
