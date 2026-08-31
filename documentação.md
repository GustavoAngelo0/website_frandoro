```markdown
#  Documentação Técnica — Arquitetura e Lógica

Esta documentação detalha as especificações do banco de dados, o fluxo de comunicação entre cliente e servidor e as decisões arquiteturais do sistema **PontoCaixa**.

---

##  Modelagem do Banco de Dados (Relacional)

O sistema opera com um modelo de dados normalizado para evitar redundâncias e garantir a integridade referencial através de chaves estrangeiras (`FOREIGN KEY`).

### Tabela `usuarios`
Responsável pela autenticação e vínculo de registros (Auditoria).
* `id` (INT, PK, Auto Increment)
* `nome` (VARCHAR 100)
* `email` (VARCHAR 100, UNIQUE)
* `senha` (VARCHAR 255) - *Armazena exclusivamente o hash gerado por `password_hash()`.*

### Tabela `produtos`
Catálogo de itens vinculados ao usuário logado, permitindo multi-tenancy básico.
* `id` (INT, PK)
* `usuario_id` (INT, FK -> usuarios.id)
* `nome` (VARCHAR 100)
* `preco` (DECIMAL 10,2)
* `tipo_unidade` (ENUM: 'un', 'kg') - *Define se o JS permitirá inputs fracionados na interface do PDV.*

### Tabela `sessoes_caixa`
Isola o fluxo financeiro. Nenhuma venda pode ser registrada se não houver uma sessão com status 'aberto'.
* `id` (INT, PK)
* `usuario_id` (INT, FK)
* `valor_inicial` (DECIMAL 10,2) - *Fundo de troco.*
* `status` (ENUM: 'aberto', 'fechado')
* `data_abertura` (DATETIME)
* `data_fechamento` (DATETIME, NULL)

### Tabelas Transacionais: `vendas` e `itens_venda`
Trabalham em conjunto (Relação 1:N) para registrar o cabeçalho do cupom e suas linhas de itens.
* **`vendas`**: `id`, `usuario_id`, `total` (DECIMAL 10,2), `forma_pagamento`, `data_venda` (TIMESTAMP).
* **`itens_venda`**: `id`, `venda_id` (FK), `produto_id` (FK), `quantidade` (DECIMAL 10,3), `preco_unitario` (DECIMAL 10,2).

---

##  Fluxo de Processamento de Vendas (API Interna)

Para garantir uma interface sem recarregamentos no arquivo `pdv.php`, o envio do carrinho foi construído utilizando a **Fetch API** no formato JSON.

### 1. Payload do Cliente (JavaScript)
Quando o botão "Finalizar Venda" é clicado, o JS constrói o seguinte objeto e dispara via `POST`:
```json
{
  "forma_pagamento": "PIX",
  "total": 145.50,
  "carrinho": [
    { "id_produto": 12, "quantidade": 2, "preco_unitario": 25.00 },
    { "id_produto": 8, "quantidade": 1.5, "preco_unitario": 63.66 }
  ]
}
