#!/usr/bin/env bash
set -euo pipefail
cd /workspaces/kefu001
mkdir -p logs

if pgrep -f 'java -jar /workspaces/kefu001/combined.jar' >/dev/null 2>&1; then
  echo 'Backend already running.'
else
  nohup java -jar /workspaces/kefu001/combined.jar \
    --server.port=8081 \
    --spring.datasource.url=jdbc:mysql://127.0.0.1:3306/kefu2?useSSL=false&allowPublicKeyRetrieval=true&characterEncoding=utf8&serverTimezone=Asia/Shanghai \
    --spring.datasource.username=kefu2 \
    --spring.datasource.password=kefu2 \
    --spring.web.resources.static-locations=file:/workspaces/kefu001/site/ \
    > logs/backend.log 2>&1 &
fi

if pgrep -f 'proxy_server.py' >/dev/null 2>&1; then
  echo 'Proxy already running.'
else
  nohup python3 /workspaces/kefu001/proxy_server.py > logs/proxy_server.log 2>&1 &
fi

sleep 3
printf '\n== Status ==\n'
ss -ltnp | grep -E ':8080|:8081' || true
printf '\n== Health ==\n'
curl -sS http://127.0.0.1:8080/ >/dev/null && echo '8080 OK' || echo '8080 DOWN'
curl -sS http://127.0.0.1:8081/ >/dev/null && echo '8081 OK' || echo '8081 DOWN'
