#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

printf 'Port 8080: '
if ss -ltn 2>/dev/null | awk '{print $4}' | grep -Eq ':8080$'; then
  echo 'running'
else
  echo 'stopped'
fi

printf 'Port 8081: '
if ss -ltn 2>/dev/null | awk '{print $4}' | grep -Eq ':8081$'; then
  echo 'running'
else
  echo 'stopped'
fi

printf 'Backend health: '
if curl -sf -X POST http://127.0.0.1:8081/api/auth/login -H 'Content-Type: application/json' -d '{"account":"admin","password":"123456"}' >/dev/null 2>&1; then
  echo 'healthy'
else
  echo 'unknown'
fi

printf 'Frontend entry: '
if curl -sf http://127.0.0.1:8080/ >/dev/null 2>&1; then
  echo 'http://127.0.0.1:8080'
else
  echo 'not available'
fi
