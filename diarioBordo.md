```markdown
# 📖 Diário de Bordo — Evolução do Projeto

Este documento registra cronologicamente as etapas, desafios e refatorações que moldaram o desenvolvimento do **PontoCaixa** até a sua versão de produção.

---

### 🟢 Fase 1: Fundação e Estrutura de Dados
* **Objetivo:** Criar a base lógica do sistema e o ambiente de testes.
* **Ações:** 
  * Configuração do XAMPP local.
  * Criação do banco de dados `frandoro`.
  * Escrita dos scripts DDL iniciais para as tabelas de usuários, sessões de caixa, produtos e itens.
* **Desafio:** Garantir que o sistema de vendas aceitasse números decimais (para pesos em Kg). 
* **Solução:** O campo de quantidade na tabela `itens_venda` foi definido como `DECIMAL(10,3)`, permitindo precisão de gramas (ex: 1.250 kg).

### 🟡 Fase 2: Interface e Lógica de Frente de Caixa (PDV)
* **Objetivo:** Desenvolver uma tela de PDV rápida e intuitiva.
* **Ações:**
  * Uso do Bootstrap 5 para criação de uma grade responsiva de produtos.
  * Desenvolvimento da lógica JavaScript de carrinho em memória (Array de objetos).
  * Criação da comunicação assíncrona entre o front-end e o `venda_controller.php` via `fetch()`.
* **Desafio:** Como enviar arrays complexos do JavaScript para o PHP sem submeter um formulário tradicional?
* **Solução:** Serialização do carrinho via `JSON.stringify()` e decodificação no backend usando `json_decode(file_get_contents('php://input'))`.

### 🟠 Fase 3: Auditoria, Histórico e Adequação Legal
* **Objetivo:** Permitir consulta ao passado do sistema e respeitar leis de privacidade.
* **Ações:**
  * Construção da página `historico.php` utilizando funções avançadas de agrupamento por datas no SQL (`GROUP BY DATE()`).
  * Criação do componente dinâmico `cookie_banner.php` (Aviso LGPD).
* **Desafio:** O banner de cookies estava quebrando o layout ao ser incluído no topo do arquivo.
* **Solução:** Correção arquitetural movendo o `include 'includes/cookie_banner.php';` estritamente para o rodapé da página, imediatamente antes do fechamento da tag `</body>`, permitindo o carregamento perfeito do DOM.

### 🔴 Fase 4: Blindagem de Segurança (Hardening) e Deploy
* **Objetivo:** Preparar o código local para ser exposto à internet de forma segura.
* **Ações:**
  * **Sessão:** Refatoração completa do `config/autenticacao.php` para incluir `session.cookie_httponly = 1` e `session.cookie_samesite = 'Lax'` antes de invocar o `session_start()`.
  * **Conexão Inteligente:** Reescrita do `config/conexao.php` implementando a lógica de `$isLocalhost`. Correção de um erro de digitação no IP de verificação local (de `127.0.01` para o correto `127.0.0.1`).
  * **Proteção de Pastas:** Criação do arquivo `.htaccess` na raiz com o comando `Options -Indexes`, impedindo cibercriminosos de listar os diretórios de `controllers` e `config`.
* **Resultado:** O sistema atingiu um padrão elevado de segurança e organização, pronto para deploy na Hostinger e para compor o portfólio no GitHub.
