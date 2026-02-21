#!/usr/bin/env bash
set -euo pipefail

# deploy.sh
# Uso: ./deploy.sh [branch]
# Ex.: ./deploy.sh main
# Script simples para fazer pull, instalar dependências, build front e otimizar Laravel.

BRANCH="${1:-main}"
REMOTE="${2:-origin}"

WORKDIR="$(cd "$(dirname "$0")" && pwd)"
cd "$WORKDIR"

echo "[$(date +"%Y-%m-%d %H:%M:%S")] Iniciando deploy (branch=${BRANCH}) no diretório: $WORKDIR"

# 1) Atualizar código
echo "--- Atualizando código do git..."
git fetch "$REMOTE" --prune
git checkout "$BRANCH"
git pull "$REMOTE" "$BRANCH"

# 2) Determinar usuário web (www-data se existir)
if id -u www-data >/dev/null 2>&1; then
  WWWUSER=www-data
else
  WWWUSER="${SUDO_USER:-$(whoami)}"
fi

echo "--- Usuário web: $WWWUSER"

# 3) Composer (PHP)
if command -v composer >/dev/null 2>&1; then
  echo "--- Instalando dependências PHP (composer)"
  composer install --no-dev --optimize-autoloader --no-interaction
else
  echo "!!! composer não encontrado no PATH. Pule esta etapa ou instale o composer."
fi

# 4) Migrations (forçar em produção)
echo "--- Rodando migrations (force)"
php artisan migrate --force || true

# 5) Frontend: usa npm ci se existir lockfile, fallback para npm install
if [ -f package-lock.json ] || [ -f pnpm-lock.yaml ] || [ -f yarn.lock ]; then
  echo "--- Instalando dependências JS (npm ci)"
  npm ci
else
  echo "--- Instalando dependências JS (npm install)"
  npm install
fi

echo "--- Build frontend (Vite)"
npm run build

# 6) Caches e otimizações do Laravel
echo "--- Limpando e cacheando configurações rotas e views"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:clear || true
php artisan view:cache || true
php artisan cache:clear || true

# 7) Ajustar permissões (opcional)
if id -u "$WWWUSER" >/dev/null 2>&1; then
  echo "--- Ajustando dono e permissões (usuario: $WWWUSER)"
  sudo chown -R "$WWWUSER":"$WWWUSER" "$WORKDIR"
  sudo find "$WORKDIR" -type d -exec chmod 755 {} \;
  sudo find "$WORKDIR" -type f -exec chmod 644 {} \;
  # storage & bootstrap cache devem ser graváveis
  sudo chown -R "$WWWUSER":"$WWWUSER" "$WORKDIR/storage" "$WORKDIR/bootstrap/cache" || true
  sudo chmod -R ug+rwx "$WORKDIR/storage" "$WORKDIR/bootstrap/cache" || true
fi

# 8) Reiniciar php-fpm (tenta detectar versão)
echo "--- Reiniciando php-fpm (se existir)"
PHPFPM_SERVICE=""
for svc in php8.2-fpm php8.1-fpm php8.0-fpm php7.4-fpm php7.3-fpm php-fpm; do
  if systemctl list-units --type=service --all | grep -q "$svc"; then
    PHPFPM_SERVICE="$svc"
    break
  fi
done

if [ -n "$PHPFPM_SERVICE" ]; then
  echo "Restarting $PHPFPM_SERVICE"
  sudo systemctl restart "$PHPFPM_SERVICE" || sudo service "$PHPFPM_SERVICE" restart || echo "Falha ao reiniciar $PHPFPM_SERVICE"
else
  echo "Nenhum serviço php-fpm encontrado via systemctl. Pule restart."
fi

# 9) Recarregar nginx se existir
if systemctl list-units --type=service --all | grep -q nginx; then
  echo "--- Recarregando nginx"
  sudo systemctl reload nginx || sudo service nginx reload || echo "Falha ao recarregar nginx"
fi

echo "[$(date +"%Y-%m-%d %H:%M:%S")] Deploy finalizado com sucesso."

