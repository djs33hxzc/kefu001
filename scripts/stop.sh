#!/usr/bin/env bash
set -euo pipefail
cd /workspaces/kefu001
for pid in $(pgrep -f '/workspaces/kefu001/combined.jar' || true); do
  kill "$pid" 2>/dev/null || true
done
for pid in $(pgrep -f '/workspaces/kefu001/proxy_server.py' || true); do
  kill "$pid" 2>/dev/null || true
done
sleep 2
ss -ltnp | grep -E ':8080|:8081' || true
