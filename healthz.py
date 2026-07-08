import http.server
import os

class HealthHandler(http.server.BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path in {'/healthz', '/health'}:
            body = b'{"status":"ok"}'
            self.send_response(200)
            self.send_header('Content-Type', 'application/json')
            self.send_header('Content-Length', str(len(body)))
            self.end_headers()
            self.wfile.write(body)
        else:
            self.send_response(404)
            self.end_headers()

    def log_message(self, format, *args):
        return

if __name__ == '__main__':
    port = int(os.environ.get('PORT', '8080'))
    with http.server.ThreadingHTTPServer(('0.0.0.0', port), HealthHandler) as httpd:
        httpd.serve_forever()
