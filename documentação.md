Documentação da Fase 1: Configuração do Banco de Dados (frandoro)
1. Visão Geral do Projeto
Nome do Banco: frandoro

Plataforma/Servidor: XAMPP (Apache + MySQL/MariaDB)

Ferramenta de Gerenciamento: phpMyAdmin (http://localhost/phpmyadmin)

Tecnologia de Conexão (PHP): PDO (PHP Data Objects) com codificação utf8mb4

2. Estrutura de Tabelas Criadas
O banco de dados foi estruturado com um total de 5 tabelas relacionais para gerenciar a loja de assados:

usuarios: Armazena os dados de login e acesso dos administradores/operadores (com senhas protegidas por hash).

caixa: Controla a abertura e fechamento de caixa diário, separando os valores por forma de pagamento (Dinheiro, Pix, Cartão).

produtos: Gerencia o catálogo de carnes e acompanhamentos, permitindo vendas por peso (kg) ou unidade.

pedidos: Registra as vendas e encomendas, detalhando se é para retirada ou entrega, além do status do pedido e do pagamento.

itens_pedido: Relaciona quais produtos e quantidades compõem cada pedido específico.
