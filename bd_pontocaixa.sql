-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/08/2026 às 19:22
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `frandoro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `caixa`
--

CREATE TABLE `caixa` (
  `id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_abertura` datetime NOT NULL,
  `data_fechamento` datetime DEFAULT NULL,
  `valor_inicial` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_final_dinheiro` decimal(10,2) DEFAULT NULL,
  `valor_total_pix` decimal(10,2) DEFAULT NULL,
  `valor_total_cartao` decimal(10,2) DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto',
  `observacoes` text DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tipo` varchar(20) DEFAULT 'entrada',
  `data_movimentacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `caixa`
--

INSERT INTO `caixa` (`id`, `descricao`, `usuario_id`, `data_abertura`, `data_fechamento`, `valor_inicial`, `valor_final_dinheiro`, `valor_total_pix`, `valor_total_cartao`, `status`, `observacoes`, `valor`, `tipo`, `data_movimentacao`) VALUES
(2, 'Venda PDV #6 (dinheiro)', 2, '0000-00-00 00:00:00', '2026-08-27 14:25:04', 0.00, NULL, NULL, NULL, 'fechado', NULL, 79.45, 'entrada', '2026-08-26 13:40:30'),
(3, 'Venda PDV #7 (dinheiro)', 2, '0000-00-00 00:00:00', '2026-08-27 14:22:25', 0.00, NULL, NULL, NULL, 'fechado', NULL, 200.31, 'entrada', '2026-08-26 13:46:52'),
(4, 'Venda PDV #8 (cartao_debito)', 2, '0000-00-00 00:00:00', '2026-08-27 14:22:24', 0.00, NULL, NULL, NULL, 'fechado', NULL, 125.50, 'entrada', '2026-08-26 16:55:52'),
(5, 'Abertura de Caixa (Troco Inicial)', 2, '0000-00-00 00:00:00', '2026-08-27 14:22:23', 0.00, NULL, NULL, NULL, 'fechado', NULL, 200.00, 'entrada', '2026-08-27 14:18:36'),
(6, 'Venda PDV (DINHEIRO)', 2, '0000-00-00 00:00:00', '2026-08-27 14:22:21', 0.00, NULL, NULL, NULL, 'fechado', NULL, 99.00, 'entrada', '2026-08-27 14:19:09'),
(7, 'Venda PDV (DINHEIRO)', 2, '0000-00-00 00:00:00', '2026-08-27 14:24:54', 0.00, NULL, NULL, NULL, 'fechado', NULL, 65.00, 'entrada', '2026-08-27 14:22:32'),
(8, '', 2, '2026-08-27 14:25:13', '2026-08-28 15:13:22', 250.00, NULL, NULL, NULL, 'fechado', NULL, 0.00, 'entrada', '2026-08-27 14:25:13'),
(9, 'Venda PDV (DINHEIRO)', 2, '0000-00-00 00:00:00', '2026-08-28 15:13:05', 0.00, NULL, NULL, NULL, 'fechado', NULL, 158.00, 'entrada', '2026-08-27 14:25:25'),
(10, 'Venda PDV (DINHEIRO)', 2, '0000-00-00 00:00:00', '2026-08-27 14:48:33', 0.00, NULL, NULL, NULL, 'fechado', NULL, 35.00, 'entrada', '2026-08-27 14:26:31'),
(11, '', 2, '2026-08-28 15:13:44', '2026-08-28 19:14:55', 250.00, NULL, NULL, NULL, 'fechado', NULL, 0.00, 'entrada', '2026-08-28 15:13:44'),
(12, '', 2, '2026-08-28 19:25:04', NULL, 100.00, NULL, NULL, NULL, 'aberto', NULL, 0.00, 'entrada', '2026-08-28 19:25:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `encomendas`
--

CREATE TABLE `encomendas` (
  `id` int(11) NOT NULL,
  `cliente_nome` varchar(100) NOT NULL,
  `cliente_telefone` varchar(20) DEFAULT NULL,
  `data_retirada` date NOT NULL,
  `horario_retirada` time NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `valor_sinal` decimal(10,2) DEFAULT 0.00,
  `status` enum('pendente','em_preparo','pronto','entregue','cancelado') DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_pedido`
--

CREATE TABLE `itens_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `itens_pedido`
--

INSERT INTO `itens_pedido` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`, `subtotal`) VALUES
(11, 6, 1, 1.000, 70.00, 70.00),
(12, 6, 2, 0.270, 35.00, 9.45),
(13, 7, 1, 2.000, 70.00, 140.00),
(14, 7, 2, 1.723, 35.00, 60.31),
(15, 8, 4, 1.200, 90.00, 108.00),
(16, 8, 2, 0.500, 35.00, 17.50),
(19, 10, 4, 0.050, 90.00, 0.00),
(20, 11, 10, 1.000, 90.00, 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_venda`
--

CREATE TABLE `itens_venda` (
  `id` int(11) NOT NULL,
  `venda_id` int(11) NOT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `nome_produto` varchar(100) NOT NULL,
  `quantidade` decimal(10,3) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `itens_venda`
--

INSERT INTO `itens_venda` (`id`, `venda_id`, `produto_id`, `nome_produto`, `quantidade`, `preco_unitario`, `subtotal`) VALUES
(1, 1, 1, 'Frango Recheado', 1.000, 70.00, 70.00),
(2, 2, 1, 'Frango Recheado', 1.000, 70.00, 70.00),
(3, 3, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(4, 4, 8, 'Frango Assado', 2.000, 65.00, 130.00),
(5, 5, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(6, 6, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(7, 7, 8, 'Frango Assado', 2.000, 65.00, 130.00),
(8, 8, 10, 'Costela Minga', 1.000, 90.00, 90.00),
(9, 9, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(10, 10, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(11, 11, 10, 'Costela Minga', 2.700, 90.00, 243.00),
(12, 12, 8, 'Frango Assado', 1.000, 65.00, 65.00),
(13, 13, 8, 'Frango Assado', 1.000, 65.00, 65.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `caixa_id` int(11) DEFAULT NULL,
  `nome_cliente` varchar(100) NOT NULL,
  `telefone_cliente` varchar(20) DEFAULT NULL,
  `tipo_entrega` enum('retirada','entrega') NOT NULL,
  `endereco_entrega` text DEFAULT NULL,
  `data_horario_agendado` datetime NOT NULL,
  `status_pedido` enum('pendente','em_preparo','pronto','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `status_pagamento` enum('pendente','pago') NOT NULL DEFAULT 'pendente',
  `metodo_pagamento` enum('dinheiro','pix','cartao_credito','cartao_debito') DEFAULT NULL,
  `valor_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `observacoes` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cliente_nome` varchar(100) DEFAULT NULL,
  `cliente_telefone` varchar(20) DEFAULT NULL,
  `data_retirada` date DEFAULT NULL,
  `hora_retirada` time DEFAULT NULL,
  `status_encomenda` enum('pendente','em_preparo','pronto','concluido','cancelado') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `caixa_id`, `nome_cliente`, `telefone_cliente`, `tipo_entrega`, `endereco_entrega`, `data_horario_agendado`, `status_pedido`, `status_pagamento`, `metodo_pagamento`, `valor_total`, `observacoes`, `criado_em`, `atualizado_em`, `cliente_nome`, `cliente_telefone`, `data_retirada`, `hora_retirada`, `status_encomenda`) VALUES
(6, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', '', 'pago', 'dinheiro', 79.45, NULL, '2026-08-26 16:40:30', '2026-08-26 16:40:30', NULL, NULL, NULL, NULL, 'pendente'),
(7, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', '', 'pago', 'dinheiro', 200.31, NULL, '2026-08-26 16:46:52', '2026-08-26 16:46:52', NULL, NULL, NULL, NULL, 'pendente'),
(8, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', '', 'pago', 'cartao_debito', 125.50, NULL, '2026-08-26 19:55:52', '2026-08-26 19:55:52', NULL, NULL, NULL, NULL, 'pendente'),
(10, 7, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', 'pendente', 'pendente', NULL, 4.50, NULL, '2026-08-28 17:55:44', '2026-08-28 17:55:44', 'aaaa', '19999999999', '2026-08-28', '12:00:00', 'pendente'),
(11, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', 'pendente', 'pendente', NULL, 90.00, NULL, '2026-08-28 22:15:43', '2026-08-28 22:15:43', 'Gustavo Pereira', '19997031712', '2026-08-29', '12:00:00', 'pendente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `tipo_unidade` enum('kg','unidade') NOT NULL DEFAULT 'kg',
  `quantidade_estoque` decimal(10,3) NOT NULL DEFAULT 0.000,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `destaque_pdv` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `unidade_medida` varchar(20) NOT NULL DEFAULT 'unidade',
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `tipo_unidade`, `quantidade_estoque`, `ativo`, `destaque_pdv`, `criado_em`, `unidade_medida`, `usuario_id`) VALUES
(1, 'Frango Recheado', 70.00, 'unidade', 0.000, 1, 0, '2026-08-25 18:51:53', 'unidade', NULL),
(2, 'Maionese', 35.00, 'kg', -1.000, 1, 0, '2026-08-26 16:04:18', 'kg', NULL),
(3, 'Frango Assado', 65.00, 'unidade', -2.000, 1, 0, '2026-08-26 18:33:09', 'unidade', NULL),
(4, 'Costela Minga', 90.00, 'kg', 1.500, 1, 0, '2026-08-26 18:36:43', 'kg', NULL),
(5, 'Farofa', 68.00, 'kg', 0.500, 1, 0, '2026-08-26 19:57:01', 'kg', NULL),
(6, 'Costelinha', 120.00, 'unidade', 2.000, 1, 0, '2026-08-27 17:27:40', 'unidade', NULL),
(8, 'Frango Assado', 65.00, 'unidade', 1.000, 1, 0, '2026-08-27 19:05:06', 'unidade', 2),
(10, 'Costela Minga', 90.00, 'kg', 2.500, 1, 0, '2026-08-28 18:29:48', 'kg', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sessoes_caixa`
--

CREATE TABLE `sessoes_caixa` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor_abertura` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valor_fechamento_calculado` decimal(10,2) DEFAULT NULL,
  `valor_fechamento_informado` decimal(10,2) DEFAULT NULL,
  `diferenca` decimal(10,2) DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto',
  `data_abertura` datetime NOT NULL DEFAULT current_timestamp(),
  `data_fechamento` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `sessoes_caixa`
--

INSERT INTO `sessoes_caixa` (`id`, `usuario_id`, `valor_abertura`, `valor_fechamento_calculado`, `valor_fechamento_informado`, `diferenca`, `status`, `data_abertura`, `data_fechamento`) VALUES
(1, 2, 200.00, NULL, NULL, NULL, 'aberto', '2026-08-27 14:18:36', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `perfil` enum('admin','operador') DEFAULT 'operador'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `caixa_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor_total` decimal(10,2) NOT NULL,
  `forma_pagamento` varchar(50) NOT NULL,
  `data_venda` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `caixa_id`, `usuario_id`, `valor_total`, `forma_pagamento`, `data_venda`) VALUES
(1, 10, 2, 70.00, 'dinheiro', '2026-08-27 14:36:23'),
(2, 10, 2, 70.00, 'dinheiro', '2026-08-27 14:48:23'),
(3, 9, 2, 65.00, 'pix', '2026-08-27 16:06:19'),
(4, 9, 2, 130.00, 'pix', '2026-08-28 15:04:21'),
(5, 9, 2, 65.00, 'dinheiro', '2026-08-28 15:12:11'),
(6, 11, 2, 65.00, 'dinheiro', '2026-08-28 15:28:08'),
(7, 11, 2, 130.00, 'dinheiro', '2026-08-28 15:28:38'),
(8, 11, 2, 90.00, 'dinheiro', '2026-08-28 15:29:56'),
(9, 11, 2, 65.00, 'dinheiro', '2026-08-28 15:39:08'),
(10, 11, 2, 65.00, 'dinheiro', '2026-08-28 15:41:19'),
(11, 11, 2, 243.00, 'dinheiro', '2026-08-28 15:44:36'),
(12, 11, 2, 65.00, 'dinheiro', '2026-08-28 19:14:18'),
(13, 12, 2, 65.00, 'dinheiro', '2026-08-28 19:38:37');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `caixa`
--
ALTER TABLE `caixa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_caixa_usuarios` (`usuario_id`);

--
-- Índices de tabela `encomendas`
--
ALTER TABLE `encomendas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_pedidos` (`pedido_id`),
  ADD KEY `fk_itens_produtos` (`produto_id`);

--
-- Índices de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venda_id` (`venda_id`);

--
-- Índices de tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedidos_usuarios` (`usuario_id`),
  ADD KEY `fk_pedidos_caixa` (`caixa_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `sessoes_caixa`
--
ALTER TABLE `sessoes_caixa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caixa_id` (`caixa_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `caixa`
--
ALTER TABLE `caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `encomendas`
--
ALTER TABLE `encomendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `itens_venda`
--
ALTER TABLE `itens_venda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `sessoes_caixa`
--
ALTER TABLE `sessoes_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `caixa`
--
ALTER TABLE `caixa`
  ADD CONSTRAINT `fk_caixa_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD CONSTRAINT `fk_itens_pedidos` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_itens_produtos` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);

--
-- Restrições para tabelas `itens_venda`
--
ALTER TABLE `itens_venda`
  ADD CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedidos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `sessoes_caixa`
--
ALTER TABLE `sessoes_caixa`
  ADD CONSTRAINT `sessoes_caixa_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
