# LED FACTORY — SISTEMA DE ORÇAMENTOS
## Guia de Instalação

---

### PRÉ-REQUISITOS
- PHP 7.4+
- MySQL 5.7+
- Composer instalado
- Extensões PHP: pdo_mysql, gd, zip, mbstring

---

### PASSO 1 — Upload dos Arquivos

Suba a pasta `orcamento/` inteira para a raiz do site:

```
/home/USUARIO/public_html/orcamento/
```

A estrutura deve ficar:
```
orcamento/
├── index.php
├── composer.json
├── .htaccess
├── assets/
│   ├── css/style.css
│   ├── img/logo.png, fachada.jpg, piscina.jpg
├── includes/
│   ├── config.php      ← EDITAR CREDENCIAIS
│   ├── security.php
│   ├── database.php
│   ├── pdf_generator.php
├── logs/               ← Permissão 750
├── pages/
│   ├── login.php
│   ├── do_login.php
│   ├── dashboard.php
│   ├── novo.php
│   ├── salvar.php
│   ├── download.php
│   ├── historico.php
│   ├── deletar.php
├── templates/
│   ├── pdf_template.php
```

---

### PASSO 2 — Criar o Banco de Dados

No cPanel → MySQL Databases:
1. Crie um banco: `ledfactory_orcamentos`
2. Crie um usuário
3. Associe o usuário ao banco com TODAS as permissões

---

### PASSO 3 — Configurar Credenciais

Edite `includes/config.php` e altere:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'SEU_PREFIXO_ledfactory_orcamentos');
define('DB_USER', 'SEU_PREFIXO_usuario');
define('DB_PASS', 'SUA_SENHA_DO_BANCO');
```

---

### PASSO 4 — Instalar Dependências (Composer)

Via SSH, navegue até a pasta e rode:

```bash
cd /home/USUARIO/public_html/orcamento
composer install --no-dev --optimize-autoloader
```

---

### PASSO 5 — Permissões

```bash
chmod 750 logs/
chmod 644 .htaccess
chmod 644 includes/.htaccess
chmod 644 logs/.htaccess
chmod 644 templates/.htaccess
```

---

### PASSO 6 — Criar Tabelas

1. Acesse: `https://ledfactory.com.br/orcamento/?p=login`
2. Faça login com: `admin` / `Led@2026!`
3. Acesse: `https://ledfactory.com.br/orcamento/?p=setup`
4. Deve aparecer "Tabelas criadas com sucesso!"

---

### PASSO 7 — Testar

1. Vá em "Novo Orçamento"
2. Preencha os campos
3. Clique "Gerar Orçamento PDF"
4. Baixe o PDF e verifique se está correto

---

### CREDENCIAIS DE ACESSO

- **Usuário:** admin
- **Senha:** Led@2026!

Para alterar a senha, gere um novo hash:
```bash
php -r "echo password_hash('NOVA_SENHA', PASSWORD_BCRYPT, ['cost' => 12]);"
```
E substitua o valor de `AUTH_PASS_HASH` em `config.php`.

---

### SEGURANÇA IMPLEMENTADA

- ✅ Login com bcrypt (hash seguro)
- ✅ Proteção CSRF em todos os formulários
- ✅ Rate limiting (60 req/min por IP)
- ✅ Brute force lockout (5 tentativas → 15min bloqueio)
- ✅ Headers HTTP de segurança (X-Frame, XSS, CSP, etc.)
- ✅ Sessões seguras (HttpOnly, Secure, SameSite)
- ✅ Sanitização de todos os inputs
- ✅ Prepared statements (PDO) contra SQL injection
- ✅ .htaccess bloqueando acesso direto a pastas
- ✅ Bloqueio de bots/scanners por User-Agent
- ✅ Bloqueio de path traversal e XSS na URL
- ✅ Logs de acesso e operações
- ✅ PDFs salvos como ZIP para otimizar espaço

---

### LOGS

Todos os logs ficam em `logs/`:
- `access.log` — Logins (sucesso e falha)
- `orcamentos.log` — Criação e exclusão de orçamentos
- `logs/*.pdf.zip` — PDFs dos orçamentos comprimidos
