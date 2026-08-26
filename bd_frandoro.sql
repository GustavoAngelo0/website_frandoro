-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/08/2026 às 18:50
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
(2, 'Venda PDV #6 (dinheiro)', 2, '0000-00-00 00:00:00', NULL, 0.00, NULL, NULL, NULL, 'aberto', NULL, 79.45, 'entrada', '2026-08-26 13:40:30'),
(3, 'Venda PDV #7 (dinheiro)', 2, '0000-00-00 00:00:00', NULL, 0.00, NULL, NULL, NULL, 'aberto', NULL, 200.31, 'entrada', '2026-08-26 13:46:52');

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
(14, 7, 2, 1.723, 35.00, 60.31);

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
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `caixa_id`, `nome_cliente`, `telefone_cliente`, `tipo_entrega`, `endereco_entrega`, `data_horario_agendado`, `status_pedido`, `status_pagamento`, `metodo_pagamento`, `valor_total`, `observacoes`, `criado_em`, `atualizado_em`) VALUES
(6, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', '', 'pago', 'dinheiro', 79.45, NULL, '2026-08-26 16:40:30', '2026-08-26 16:40:30'),
(7, 2, NULL, '', NULL, 'retirada', NULL, '0000-00-00 00:00:00', '', 'pago', 'dinheiro', 200.31, NULL, '2026-08-26 16:46:52', '2026-08-26 16:46:52');

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
  `unidade_medida` varchar(20) NOT NULL DEFAULT 'unidade'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `tipo_unidade`, `quantidade_estoque`, `ativo`, `destaque_pdv`, `criado_em`, `unidade_medida`) VALUES
(1, 'Frango Recheado', 70.00, 'kg', 0.000, 1, 0, '2026-08-25 18:51:53', 'unidade'),
(2, 'Maionese', 35.00, 'kg', 0.000, 1, 0, '2026-08-26 16:04:18', 'kg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `criado_em`) VALUES
(1, 'aaaa12', '123@gmail.cpm', '$2y$10$IzlkhJOWvqZSLFgd/TEnsuPE2BnQcAxr01IvJsIrK6ebaj8egBl9e', '2026-08-25 18:05:54'),
(2, 'Gustavo Angelo', 'w0w.gupereira@gmail.com', '$2y$10$y.zVnIgodzLt6RFtJf5.ZuSQmd2gSgcG0S8eT36GtJx5blaDscFJ2', '2026-08-25 18:37:37');

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
-- Índices de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_pedidos` (`pedido_id`),
  ADD KEY `fk_itens_produtos` (`produto_id`);

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
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `caixa`
--
ALTER TABLE `caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `itens_pedido`
--
ALTER TABLE `itens_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Restrições para tabelas `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_caixa` FOREIGN KEY (`caixa_id`) REFERENCES `caixa` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedidos_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


