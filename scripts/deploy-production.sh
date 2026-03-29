#!/usr/bin/env bash
set -euo pipefail

WITH_SEED=false
SKIP_BUILD=false

for arg in "$@"; do
  case "$arg" in
    --with-seed)
      WITH_SEED=true
      ;;
    --skip-build)
      SKIP_BUILD=true
      ;;
    *)
      echo "Unknown option: $arg"
      exit 1
      ;;
  esac
done

echo "== ScanHadir Phase 10 Production Deploy =="

echo "1) Install/update PHP dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader

if [ "$SKIP_BUILD" = false ]; then
  echo "2) Install/build frontend assets..."
  npm ci
  npm run build
fi

echo "3) Run production preparation command..."
CMD=(php artisan app:prepare-production --with-migrate --force)
if [ "$WITH_SEED" = true ]; then
  CMD+=(--with-seed)
fi
"${CMD[@]}"

echo "4) Verify health endpoint..."
if command -v curl >/dev/null 2>&1; then
  HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1/up || true)
  if [ "$HTTP_CODE" = "200" ]; then
    echo "Health check status: 200"
  else
    echo "Health endpoint check returned: $HTTP_CODE (validate on deployed host)"
  fi
else
  echo "curl not found; validate /up manually"
fi

echo "Deploy routine finished."
