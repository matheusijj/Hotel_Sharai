# Hotel – Setup e Execução

## Visão Geral
- **Front-end**: `FRONT-END/index.html` (formulário de reserva com validações e modal)
- **Back-end (PHP)**: roteador, APIs públicas e área administrativa em `BACK-END/`
- **Banco (MySQL)**: script em `BACK-END/sql/hotel.sql` (DB `Hotel`, tabelas `quartos`, `reservas` e `usuarios`)


## Requisitos
- PHP 8+ (com `pdo_mysql` habilitado)
- MySQL 5.7+ ou MariaDB compatível
- Acesso a um terminal

## 1) Banco de Dados (MySQL) — Instalação simples
1. Abra seu MySQL (pode ser Workbench, phpMyAdmin ou o terminal).
2. Rode o arquivo `BACK-END/sql/hotel.sql`.
   - Terminal (Windows):
     ```bash
     mysql -u root -p < d:\HOTEL\BACK-END\sql\hotel.sql
     ```
3. O script cria automaticamente:
   - Banco: `Hotel`
   - Tabelas: `quartos` (com alguns quartos de exemplo), `reservas`, `usuarios`
   - Usuário admin padrão:
     - Email: `admin@hotel.com`
     - Senha: `admin123` (alterar depois em Produção)

## 2) Configurar Back-end (PHP)
1. Edite `BACK-END/config.php` com suas credenciais MySQL:
   ```php
   return [
     'db_host' => '127.0.0.1',
     'db_name' => 'Hotel',
     'db_user' => 'root',
     'db_pass' => 'SUA_SENHA_AQUI',
   ];
   ```
2. Inicie um servidor PHP servindo o **diretório do projeto** como raiz (importante para os caminhos do front):
   - Windows (PowerShell):
     ```powershell
     php -S 127.0.0.1:8080 -t d:\HOTEL
     ```
   - Isso disponibiliza:
     - Front-end: http://127.0.0.1:8080/FRONT-END/index.html
     - Back-end: http://127.0.0.1:8080/BACK-END/index.php

## 3) Área Administrativa — Acessar e usar
1. Com o servidor PHP rodando (acima), abra no navegador:
   - http://127.0.0.1:8080/BACK-END/admin/login.php
2. Faça login com o usuário padrão:
   - Email: `admin@hotel.com`
   - Senha: `admin123`
3. O Painel mostra dois blocos principais:
   - **Gerenciar Quartos**: cadastrar, listar, editar e excluir quartos.
   - **Gerenciar Reservas**: cadastrar, listar, editar e excluir reservas.
4. Após cadastrar um quarto, a listagem abre um **modal de sucesso** com os botões:
   - "Ver quartos" (vai para a listagem)
   - "Cadastrar novo" (abre novo cadastro)

## 4) Executar o Front-end
1. Com o servidor PHP iniciado (passo acima), abra no navegador:
   - http://127.0.0.1:8080/FRONT-END/index.html
2. Preencha o formulário (Passo 1), avance para o modal (Passo 2) e confirme.
3. Os dados serão enviados ao back-end, validados (incluindo disponibilidade por quarto) e salvos no MySQL.

## 5) Testar e Validar (APIs úteis)
- Verificar listagem de quartos (carregamento dinâmico no select):
  - Endpoint: `GET /BACK-END/index.php?controller=quarto&action=listarPublico`
- Verificar disponibilidade (não permitir sobreposição de datas por quarto):
  - Endpoint: `GET /BACK-END/index.php?controller=reserva&action=verificarDisponibilidade&quarto_id=1&entrada=YYYY-MM-DD&saida=YYYY-MM-DD`
- Criar reserva:
  - Endpoint: `POST /BACK-END/index.php?controller=reserva&action=criarPublica`
  - Após a criação, a reserva é salva no banco de dados.
- Conferir no MySQL:
  ```sql
  USE Hotel;
  SELECT * FROM reservas ORDER BY id DESC;
  ```
```


Observação: se o envio de e-mail não funcionar no ambiente local, configure SMTP ou peça a integração com PHPMailer.

## Envio de E-mail de Confirmação (PHPMailer + Gmail SMTP)

Após criar a reserva, o back-end envia um e-mail de confirmação para o hóspede usando PHPMailer com Gmail SMTP.

### Dependências
- Composer instalado no sistema.
- Dependências já estão descritas em `BACK-END/composer.json` (`phpmailer/phpmailer` e `vlucas/phpdotenv`).
- Caso necessário, instale-as no diretório BACK-END:
  ```powershell
  # dentro de d:\HOTEL\BACK-END
  composer install
  ```

### Configuração (.env em BACK-END)
Crie/edite `BACK-END/.env` com as variáveis:
```
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=sua_senha_de_app
MAIL_FROM=seuemail@gmail.com
MAIL_FROM_NAME="HOTEL SHARAI"
```
Notas importantes:
- Use senha de app do Gmail (não a senha normal). Em Conta Google > Segurança > Senhas de app.
- Porta 587 com STARTTLS.
- O `config.php` lê automaticamente essas variáveis via Dotenv.

### Fluxo de envio
- Endpoint utilizado ao confirmar a reserva: `POST /BACK-END/index.php?controller=reserva&action=criarPublica`
- O back-end cria a reserva e tenta enviar o e-mail via `services/Mailer.php`.
- A resposta JSON inclui:
  - `email.enviado` (true/false)
  - `email.erro` (mensagem de erro em caso de falha)

### Testes rápidos
1) Teste público de SMTP (apenas para depuração):
- `GET /BACK-END/admin/test_email.php?public=1` (envia para o e-mail padrão configurado no script)
- Parâmetros úteis:
  - `?to=destinatario@dominio.com` para definir o destino
  - `&debug=1` para ativar saída de debug SMTP

2) Fluxo real
- Faça uma reserva no front.
- Verifique a caixa de entrada do e-mail informado no formulário.