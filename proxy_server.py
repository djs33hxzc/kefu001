import http.server
import json
import mimetypes
import os
import socketserver
import urllib.parse
import urllib.request
from pathlib import Path

HOST = "0.0.0.0"
PORT = 8080
STATIC_ROOT = Path("/workspaces/kefu001/site")
BACKEND_URL = "http://127.0.0.1:8081"


class ProxyHandler(http.server.BaseHTTPRequestHandler):
    protocol_version = "HTTP/1.1"

    def do_GET(self):
        self._handle_request(send_body=True)

    def do_HEAD(self):
        self._handle_request(send_body=False)

    def do_POST(self):
        self._handle_request(send_body=True)

    def do_PUT(self):
        self._handle_request(send_body=True)

    def do_DELETE(self):
        self._handle_request(send_body=True)

    def do_OPTIONS(self):
        self._handle_request(send_body=True)

    def _handle_request(self, send_body=True):
        path = urllib.parse.urlparse(self.path).path
        if path in {"/healthz", "/health", "/actuator/health"}:
            self._send_json({"status": "ok"}, send_body=send_body)
            return

        if path.startswith("/api/") or path.startswith("/ws") or path.startswith("/h2-console"):
            self._proxy_to_backend(send_body=send_body)
            return

        self._serve_static(path, send_body=send_body)

    def _serve_static(self, path, send_body=True):
        if path in ("", "/"):
            target = STATIC_ROOT / "index.html"
            self._send_file(target, "text/html; charset=utf-8", send_body=send_body)
            return

        normalized = path.lstrip("/")
        target = STATIC_ROOT / normalized
        if target.is_file():
            content_type, _ = mimetypes.guess_type(str(target))
            if content_type is None:
                content_type = "application/octet-stream"
            self._send_file(target, content_type, send_body=send_body)
            return

        if path.endswith("/") or not Path(normalized).suffix:
            index_file = STATIC_ROOT / "index.html"
            self._send_file(index_file, "text/html; charset=utf-8", send_body=send_body)
            return

        self.send_error(404, "Not Found")

    def _send_file(self, file_path: Path, content_type: str, send_body=True):
        if not file_path.exists():
            self.send_error(404, "Not Found")
            return

        data = file_path.read_bytes()
        self.send_response(200)
        self.send_header("Content-Type", content_type)
        self.send_header("Content-Length", str(len(data)))
        self.send_header("Cache-Control", "no-cache")
        self.end_headers()
        if send_body:
            self.wfile.write(data)

    def _send_json(self, payload, send_body=True):
        body = json.dumps(payload).encode("utf-8")
        self.send_response(200)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        if send_body:
            self.wfile.write(body)

    def _proxy_to_backend(self, send_body=True):
        parsed = urllib.parse.urlparse(self.path)
        target_url = BACKEND_URL + parsed.path
        if parsed.query:
            target_url += "?" + parsed.query

        headers = {}
        for key, value in self.headers.items():
            if key.lower() in {"host", "content-length"}:
                continue
            headers[key] = value

        body = None
        if self.command in {"POST", "PUT", "DELETE", "PATCH"}:
            length = int(self.headers.get("Content-Length", "0"))
            body = self.rfile.read(length) if length else b""

        req = urllib.request.Request(target_url, data=body, method=self.command, headers=headers)
        try:
            with urllib.request.urlopen(req, timeout=10) as resp:
                response_body = resp.read()
                self.send_response(resp.status)
                for key, value in resp.headers.items():
                    if key.lower() in {"transfer-encoding", "content-encoding", "content-length"}:
                        continue
                    self.send_header(key, value)
                self.send_header("Content-Length", str(len(response_body)))
                self.end_headers()
                if response_body:
                    self.wfile.write(response_body)
        except Exception as exc:
            self.send_response(502)
            self.send_header("Content-Type", "application/json; charset=utf-8")
            payload = f'{{"error":"proxy_error","detail":"{exc}"}}'.encode("utf-8")
            self.send_header("Content-Length", str(len(payload)))
            self.end_headers()
            self.wfile.write(payload)

    def log_message(self, format, *args):
        return


if __name__ == "__main__":
    os.makedirs(STATIC_ROOT, exist_ok=True)
    with socketserver.TCPServer((HOST, PORT), ProxyHandler) as httpd:
        print(f"Serving {STATIC_ROOT} on http://{HOST}:{PORT} -> {BACKEND_URL}")
        httpd.serve_forever()
