#!/bin/bash
# =============================================================================
# BACKEND DEPLOY SCRIPT — BLUEPRINT PLACEHOLDER
# =============================================================================
# This script has been sanitized for open-source release.
# Replace the variables below with your own server details.
#
# Usage:
#   export DEPLOY_HOST="your.server.ip"
#   export DEPLOY_USER="ubuntu"
#   export DEPLOY_KEY="~/.ssh/your-key.pem"
#   export DEPLOY_PATH="/var/www/lms-backend"
#   ./build-and-deploy.sh
# =============================================================================

DEPLOY_HOST="${DEPLOY_HOST:-your.server.ip}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
DEPLOY_KEY="${DEPLOY_KEY:-~/.ssh/your-key.pem}"
DEPLOY_PATH="${DEPLOY_PATH:-/var/www/lms-backend}"

rsync -zrpvh . -e "ssh -i ${DEPLOY_KEY}" \
  --include='**.gitignore' \
  --exclude='/.git' \
  --exclude='/vendor' \
  --filter=':- .gitignore' \
  ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH}

ssh -i "${DEPLOY_KEY}" ${DEPLOY_USER}@${DEPLOY_HOST} <<EOF
  cd ${DEPLOY_PATH}
  sudo php artisan backup:run
  composer install --no-dev --optimize-autoloader
  yes | php artisan migrate
  php artisan optimize:clear
  sudo chmod -R gu+w storage
  sudo chmod -R guo+w storage
  sudo php artisan cache:clear
EOF
