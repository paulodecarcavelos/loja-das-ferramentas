# Loja das Ferramentas

Este projeto é uma aplicação web desenvolvida em PHP e MySQL para gestão de uma loja online de ferramentas. Permite aos utilizadores registarem-se, iniciarem sessão, adicionarem produtos ao carrinho, efetuarem compras e aos administradores gerirem o catálogo de produtos.

## Funcionalidades

- Registo e autenticação de utilizadores
- Área de administração para gestão de produtos (inserir, editar, eliminar)
- Carrinho de compras com atualização dinâmica
- Integração com PayPal para pagamentos
- Envio de emails (PHPMailer)
- Interface responsiva com Bootstrap

## Estrutura de Pastas

- `index.php` — Página principal da loja
- `views/` — Páginas de frontend (login, registo, carrinho, área admin, etc.)
- `api/` — Endpoints PHP para operações AJAX e lógica de backend
  - `admin/` — Endpoints exclusivos para administração de produtos
  - `PHPMailer/` — Biblioteca para envio de emails
- `24198_Loja.sql` — Script SQL para criar e popular a base de dados

## Instalação

1. **Requisitos:**
   - PHP >= 7.0
   - MySQL/MariaDB
   - Servidor web (ex: Apache)
2. **Configuração da Base de Dados:**
   - Importe o ficheiro [`24198_Loja.sql`](24198_Loja.sql) para o seu servidor MySQL.
   - Atualize as credenciais de acesso à base de dados em [`api/db.php`](api/db.php) conforme necessário.
3. **Configuração do Email:**
   - Configure as definições de email em [`api/secrets.php`](api/secrets.php) para o envio de notificações.
4. **Executar:**
   - Coloque todos os ficheiros no diretório do seu servidor web.
   - Aceda a `index.php` através do browser.

## Tecnologias Utilizadas

- PHP
- MySQL/MariaDB
- Bootstrap 5
- PHPMailer

## Créditos

Desenvolvido por Paulo Frutuoso.

---

Este projeto utiliza a biblioteca [PHPMailer](api/PHPMailer/README.md) sob licença LGPL 2.1.