#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
LOG_DIR="$ROOT_DIR/logs"
mkdir -p "$LOG_DIR"

is_port_listening() {
  local port="$1"
  ss -ltn 2>/dev/null | awk '{print $4}' | grep -Eq ":${port}$"
}

start_backend() {
  if is_port_listening 8081; then
    echo "Backend already listening on 8081"
    return 0
  fi

  echo "Starting backend on 8081"
  nohup java -jar "$ROOT_DIR/combined.jar" \
    --server.port=8081 \
    --spring.datasource.url=jdbc:mysql://127.0.0.1:3306/kefu2?useSSL=false&allowPublicKeyRetrieval=true&characterEncoding=utf8&serverTimezone=Asia/Shanghai \
    --spring.datasource.username=kefu2 \
    --spring.datasource.password=kefu2 \
    --spring.web.resources.static-locations=file:$ROOT_DIR/site/ \
    >"$LOG_DIR/backend.log" 2>&1 &
}

start_proxy() {
  if is_port_listening 8080; then
    echo "Proxy already listening on 8080"
    return 0
  fi

  echo "Starting proxy on 8080"
  nohup python3 "$ROOT_DIR/proxy_server.py" >"$LOG_DIR/proxy.log" 2>&1 &
}

start_backend
start_proxy

for _ in $(seq 1 20); do
  if curl -sf http://127.0.0.1:8080/healthz >/dev/null 2>&1 && \
     curl -sf -X POST http://127.0.0.1:8080/api/auth/login -H 'Content-Type: application/json' -d '{"account":"admin","password":"123456"}' >/dev/null 2>&1; then
    echo "Deployment is ready."
    echo "Open: http://127.0.0.1:8080"
    echo "Login: admin / 123456"
    exit 0
  fi
  sleep 1
done

echo "Startup completed, but readiness check did not pass yet."
echo "Logs: $LOG_DIR"
