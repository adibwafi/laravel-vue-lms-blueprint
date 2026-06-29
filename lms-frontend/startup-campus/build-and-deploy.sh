#!/bin/bash
# =============================================================================
# FRONTEND DEPLOY SCRIPT — BLUEPRINT PLACEHOLDER
# =============================================================================
# Replace the variables below with your own server details.
#
# Usage:
#   export DEPLOY_HOST="your.server.ip"
#   export DEPLOY_USER="ubuntu"
#   export DEPLOY_KEY="~/.ssh/your-key.pem"
#   export DEPLOY_PATH="/var/www/lms-frontend"
#   ./build-and-deploy.sh
# =============================================================================

DEPLOY_HOST="${DEPLOY_HOST:-your.server.ip}"
DEPLOY_USER="${DEPLOY_USER:-ubuntu}"
DEPLOY_KEY="${DEPLOY_KEY:-~/.ssh/your-key.pem}"
DEPLOY_PATH="${DEPLOY_PATH:-/var/www/lms-frontend}"

npm run build

rsync -zrpvh dist/ -e "ssh -i ${DEPLOY_KEY}" \
  --include='**.gitignore' \
  --filter=':- .gitignore' \
  ${DEPLOY_USER}@${DEPLOY_HOST}:${DEPLOY_PATH} \
  --delete
