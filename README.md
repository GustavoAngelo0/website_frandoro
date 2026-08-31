#  PontoCaixa (Frandoro) — Sistema de Frente de Caixa (PDV)

> Uma solução completa, responsiva e segura para gestão de vendas de balcão, controle de fluxo de caixa e histórico de operações, desenvolvida sob medida para pequenos e médios estabelecimentos.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

---

##  Visão Geral do Projeto

O **PontoCaixa** nasceu da necessidade de modernizar o atendimento no balcão, substituindo anotações manuais e calculadoras por uma interface web ágil. O sistema permite que operadores registrem produtos rapidamente, calculem valores baseados em peso ou unidade, e finalizem vendas com baixa automática no registro financeiro da sessão ativa.

###  Principais Funcionalidades

* **Ponto de Venda (PDV) Dinâmico:** Interface assíncrona alimentada por Fetch API, permitindo adição e remoção de itens do carrinho sem recarregar a página.
* **Cálculo de Frações (Peso x Unidade):** Suporte nativo a produtos vendidos por quilo (ex: 1.250kg) ou por unidade inteira.
* **Gestão de Sessões de Caixa:** Controle rigoroso de turnos. O sistema exige a abertura do caixa com valor inicial (fundo de troco) e registra todas as operações financeiras vinculadas àquela sessão.
* **Histórico Analítico:** Visão consolidada de todas as vendas, agrupadas por data e operador, com expansão de detalhes (itens comprados, quantidades e subtotal).
* **Conformidade LGPD:** Módulo nativo de consentimento de cookies via `localStorage`, garantindo que o aviso não seja intrusivo após o aceite.
* **Segurança e Autenticação:** Sistema de login com validação estrita e bloqueio de rotas protegidas.

---

##  Arquitetura de Segurança Aplicada

A segurança foi uma premissa desde a linha de base do projeto. As seguintes barreiras foram implementadas para garantir a integridade dos dados e a proteção do servidor:

1. **Prevenção contra SQL Injection (SQLi):** Uso absoluto de `PDO` (PHP Data Objects) com *Prepared Statements*. Nenhum dado de usuário é concatenado diretamente em strings SQL.
2. **Defesa contra Cross-Site Scripting (XSS):** Todas as saídas de dados no front-end são sanitizadas utilizando `htmlspecialchars($dado, ENT_QUOTES, 'UTF-8')`.
3. **Hardening de Cookies e Sessão:**
   * `session.cookie_httponly = 1`: Impede roubo de sessão via JavaScript.
   * `session.cookie_samesite = 'Lax'`: Mitiga ataques de falsificação de solicitação (CSRF).
   * `session.cookie_secure = 1`: Habilitado em ambiente de produção (HTTPS).
4. **Proteção de Diretórios Internos:** Uso de `.htaccess` (`Options -Indexes`) na raiz do servidor para impedir a listagem de arquivos estruturais.
5. **Ocultação de Credenciais e Erros:** O arquivo `conexao.php` possui detecção automática de ambiente. Em produção, os erros do PHP são desativados (`display_errors = 0`) para evitar o vazamento da topologia do banco.

---

##  Estrutura de Diretórios (Padrão MVC Simplificado)

```text
/
├── config/
│   ├── autenticacao.php       # Middleware de validação de sessão e segurança de cookies
│   └── conexao.php            # Driver PDO com swap automático Localhost/Produção
├── controllers/
│   ├── autenticar.php         # Valida credenciais e gera a sessão do operador
│   ├── processar_caixa.php    # Abertura, fechamento e sangria
│   └── venda_controller.php   # Recebe payload JSON do carrinho e persiste no banco
├── views/
│   ├── includes/
│   │   └── cookie_banner.php  # Componente UI para adequação à LGPD
│   ├── historico.php          # Interface de listagem de transações passadas
│   ├── login.php              # Ponto de entrada do sistema
│   ├── painel.php             # Dashboard gerencial e resumo de caixa
│   └── pdv.php                # Frente de caixa assíncrona
├── public/                    # (Assets estáticos, CSS, JS, Imagens)
├── .htaccess                  # Bloqueio de indexação Apache
└── frandoro.sql               # Dump estrutural do banco de dados

---

##  Como Executar o Projeto Localmente

### Pré-requisitos
* Servidor local (ex: **XAMPP**, **WAMP** ou **Laragon**) com suporte a **PHP 8.x** e **MySQL**.
* **Git** instalado na máquina.

### Passo a Passo

1. **Clonar o repositório:**
   Abra o terminal e navegue até a pasta `htdocs` do seu XAMPP:
   ```bash
   cd c:\xampp\htdocs
   git clone [https://github.com/GustavoAngelo0/website_frandoro.git](https://github.com/GustavoAngelo0/website_frandoro.git)
