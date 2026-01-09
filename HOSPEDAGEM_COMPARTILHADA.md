# Guia de Hospedagem Compartilhada - CLIVUS

## ✅ É possível hospedar em hospedagem compartilhada?

**SIM**, mas com algumas considerações e adaptações necessárias.

## 📋 Requisitos Mínimos

### PHP
- **PHP 8.2 ou superior** (obrigatório - Laravel 12 requer PHP 8.2+)
- Extensões PHP necessárias:
  - `openssl`
  - `pdo`
  - `mbstring`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`
  - `bcmath`
  - `fileinfo`
  - `curl`

### Banco de Dados
- **MySQL 5.7+** ou **MariaDB 10.3+** (recomendado)
- Ou **PostgreSQL 10+**
- **SQLite** (não recomendado para produção em hospedagem compartilhada)

### Outros Requisitos
- Acesso SSH (recomendado, mas não obrigatório)
- Composer (geralmente disponível via SSH)
- Node.js/NPM (para build dos assets - pode fazer localmente)

## ⚠️ Limitações da Hospedagem Compartilhada

### 1. **Comandos Artisan via SSH**
- Alguns comandos precisam ser executados via SSH
- Se não tiver SSH, use o painel de controle da hospedagem

### 2. **Permissões de Arquivos**
- Pasta `storage/` e `bootstrap/cache/` precisam de permissão de escrita (755 ou 775)
- Pode precisar ajustar via FTP ou painel de controle

### 3. **Queue Workers**
- Hospedagem compartilhada geralmente não permite processos em background
- **Solução**: Usar `QUEUE_CONNECTION=sync` no `.env` (processa filas síncronamente)

### 4. **Webhooks do Asaas**
- Precisa de URL pública acessível
- Verifique se a hospedagem permite receber requisições POST externas
- **IMPORTANTE**: Configure a URL do webhook no painel do Asaas: `https://seudominio.com.br/webhook/asaas`
- A URL deve estar configurada nas informações comerciais da conta Asaas

### 5. **Cron Jobs**
- Algumas hospedagens compartilhadas permitem cron jobs
- Necessário para tarefas agendadas (se houver)

## 📦 Passos para Deploy

### 1. Preparar o Projeto Localmente

```bash
# 1. Instalar dependências
composer install --optimize-autoloader --no-dev

# 2. Build dos assets
npm install
npm run build

# 3. Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Upload dos Arquivos

**Estrutura de pastas na hospedagem:**
```
public_html/          (ou www/, htdocs/, etc)
├── index.php
├── .htaccess
└── assets/
```

**Pastas que NÃO vão para public_html:**
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `.env`

**Opções de estrutura:**

#### Opção A: Tudo na raiz (mais simples)
```
/
├── app/
├── bootstrap/
├── config/
├── database/
├── public_html/  (ou www/)
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── resources/
├── routes/
├── storage/
├── vendor/
└── .env
```

#### Opção B: Projeto em subpasta (mais organizado)
```
/
├── clivus/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   │   ├── index.php
│   │   ├── .htaccess
│   │   └── assets/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
└── public_html/  (apontar para clivus/public)
```

### 3. Configurar .htaccess na Raiz (se necessário)

Se o projeto estiver em subpasta, crie `.htaccess` na raiz:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ clivus/public/$1 [L]
</IfModule>
```

### 4. Configurar .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_banco
DB_PASSWORD=senha_banco

# Queue (usar sync em hospedagem compartilhada)
QUEUE_CONNECTION=sync

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file

# Mail (configurar SMTP da hospedagem)
MAIL_MAILER=smtp
MAIL_HOST=mail.seudominio.com.br
MAIL_PORT=587
MAIL_USERNAME=seu_email@seudominio.com.br
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu_email@seudominio.com.br
MAIL_FROM_NAME="${APP_NAME}"

# Asaas
ASAAS_API_KEY=sua_chave_api
ASAAS_SANDBOX=false

# URL pública para callbacks do Asaas (usar mesmo valor de APP_URL em produção)
# IMPORTANTE: Esta URL deve ser acessível publicamente e configurada no painel do Asaas
APP_PUBLIC_URL=https://seudominio.com.br
```

### 5. Ajustar index.php

Se o projeto estiver em subpasta, ajuste o `public/index.php`:

```php
// Antes
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Depois (se estiver em subpasta)
require __DIR__.'/../../clivus/vendor/autoload.php';
$app = require_once __DIR__.'/../../clivus/bootstrap/app.php';
```

### 6. Configurar Permissões

Via SSH ou FTP, ajuste permissões:
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework
```

### 7. Executar Migrations

Via SSH:
```bash
php artisan migrate --force
```

Ou via painel de controle (se disponível).

## 🔐 Configuração do Asaas para Produção

### 1. Configurar Webhook no Painel do Asaas

1. Acesse o painel do Asaas: https://www.asaas.com (ou sandbox.asaas.com para testes)
2. Vá em **Integrações → Webhooks**
3. Clique em **Adicionar Webhook** ou edite o existente
4. Configure:
   - **URL do Webhook**: `https://seudominio.com.br/webhook/asaas`
   - **Versão da API**: v3
   - **Fila de sincronização ativada**: Sim
   - **Tipo de envio**: Sequencial
   - **Eventos**: Marque pelo menos:
     - `PAYMENT_CONFIRMED` (obrigatório)
     - `PAYMENT_CREATED`
     - `PAYMENT_RECEIVED`
     - `PAYMENT_OVERDUE`
     - `SUBSCRIPTION_DELETED`

### 2. Configurar Domínio nas Informações Comerciais

Para que o redirecionamento automático funcione após o pagamento:

1. Acesse **Minha Conta → Informações Comerciais** no Asaas
2. Certifique-se de que o domínio `seudominio.com.br` está configurado
3. O Asaas só aceita redirecionamentos para domínios configurados por segurança

### 3. Verificar Filas de Webhook

1. Vá em **Integrações → Logs de Webhooks**
2. Verifique se há filas pausadas
3. Se houver, reative-as clicando em "Reativar Fila"
4. Monitore os logs para garantir que os webhooks estão sendo entregues (status 200)

### 4. Testar Webhook

Após configurar, teste enviando um POST manual para:
```
POST https://seudominio.com.br/webhook/asaas
Content-Type: application/json

{
  "event": "PAYMENT_CONFIRMED",
  "payment": {
    "id": "pay_test123",
    "status": "CONFIRMED",
    "customer": "cus_test123"
  }
}
```

Verifique os logs em `storage/logs/laravel.log` para confirmar que foi recebido.

## 🔧 Ajustes Necessários para Hospedagem Compartilhada

### 1. Desabilitar Queue Workers

No `.env`:
```env
QUEUE_CONNECTION=sync
```

### 2. Usar Cache de Arquivo

No `.env`:
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### 3. Ajustar Timeout (se necessário)

Criar `public/.user.ini` (se permitido):
```ini
max_execution_time = 300
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
```

## ✅ Checklist de Deploy

- [ ] PHP 8.2+ instalado
- [ ] Extensões PHP necessárias habilitadas
- [ ] Banco de dados criado e configurado
- [ ] Arquivos enviados via FTP/SFTP
- [ ] `.env` configurado corretamente
- [ ] Permissões de pastas ajustadas
- [ ] `APP_KEY` gerado (`php artisan key:generate`)
- [ ] Migrations executadas
- [ ] Assets compilados (`npm run build`)
- [ ] Cache otimizado (`php artisan config:cache`)
- [ ] Testar acesso ao site
- [ ] Testar webhook do Asaas (URL pública)
- [ ] Configurar webhook no painel do Asaas: `https://seudominio.com.br/webhook/asaas`
- [ ] Configurar domínio nas informações comerciais do Asaas (para callbacks funcionarem)
- [ ] Testar pagamento completo (criação → pagamento → webhook → ativação)
- [ ] Configurar cron jobs (se necessário)

## 🚨 Problemas Comuns

### Erro 500
- Verificar logs em `storage/logs/laravel.log`
- Verificar permissões de pastas
- Verificar se `.env` está configurado

### Erro de Permissão
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework
```

### Assets não carregam
- Verificar se `npm run build` foi executado
- Verificar se pasta `public/assets` existe
- Verificar permissões da pasta `public`

### Webhook não funciona
- Verificar se URL é acessível publicamente: `https://seudominio.com.br/webhook/asaas`
- Verificar se hospedagem permite POST externo
- Verificar se o domínio está configurado no painel do Asaas
- Verificar logs em `storage/logs/laravel.log` para ver se o webhook está sendo recebido
- Testar com ferramenta como Postman enviando POST para a URL do webhook
- Verificar se há filas de webhook pausadas no painel do Asaas

### Redirecionamento após pagamento não funciona
- Verificar se `APP_PUBLIC_URL` está configurado no `.env` com a URL correta
- Verificar se o domínio está configurado nas informações comerciais do Asaas
- O sistema funciona mesmo sem redirecionamento automático (webhook + polling no dashboard)
- Usuário pode clicar em "Ir para Dashboard" após pagar e receberá notificação quando confirmado

## 📞 Suporte

Se encontrar problemas, verifique:
1. Logs em `storage/logs/laravel.log`
2. Logs do servidor (via painel de controle)
3. Documentação da hospedagem sobre Laravel

## 📝 Script de Deploy Rápido

Crie um script `deploy.sh` para facilitar o deploy:

```bash
#!/bin/bash

echo "🚀 Iniciando deploy do CLIVUS..."

# 1. Instalar dependências
echo "📦 Instalando dependências..."
composer install --optimize-autoloader --no-dev --no-interaction

# 2. Build dos assets
echo "🎨 Compilando assets..."
npm install
npm run build

# 3. Otimizar para produção
echo "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Executar migrations (se necessário)
# php artisan migrate --force

echo "✅ Deploy concluído!"
```

**Uso:**
```bash
chmod +x deploy.sh
./deploy.sh
```

## 💡 Recomendações

Para melhor performance e menos problemas:
- **Hospedagem VPS** (mais controle)
- **Hospedagem Cloud** (escalável)
- **Hospedagem especializada em Laravel** (Hostinger, Laravel Forge, etc)

Mas hospedagem compartilhada **funciona** se seguir este guia!

## 🆘 Suporte e Troubleshooting

### Verificar se tudo está funcionando:

1. **Acesse o site**: `https://seudominio.com.br`
2. **Teste login**: Faça login com um usuário
3. **Teste criação de assinatura**: Crie uma assinatura de teste
4. **Teste webhook**: Verifique logs após criar pagamento
5. **Teste pagamento**: Faça um pagamento de teste no Asaas
6. **Verifique ativação**: Confirme que a assinatura foi ativada automaticamente

### Logs importantes:

- **Laravel**: `storage/logs/laravel.log`
- **Asaas Webhook**: Procure por "Asaas Webhook Received" nos logs
- **Erros**: Procure por "ERROR" ou "Exception" nos logs

### Comandos úteis via SSH:

```bash
# Ver logs em tempo real
tail -f storage/logs/laravel.log

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar permissões
ls -la storage/
ls -la bootstrap/cache/
```

