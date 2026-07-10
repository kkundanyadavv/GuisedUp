import json
from http.server import BaseHTTPRequestHandler, HTTPServer
import hashlib

HOST = '127.0.0.1'
PORT = 8001

class Handler(BaseHTTPRequestHandler):
    def do_POST(self):
        if self.path != '/embed':
            self.send_response(404)
            self.end_headers()
            return
        length = int(self.headers.get('content-length', 0))
        body = self.rfile.read(length).decode('utf-8') if length else ''
        try:
            data = json.loads(body)
            text = data.get('text', '')
        except Exception:
            text = ''
        digest = hashlib.sha256(text.lower().encode('utf-8')).hexdigest()
        seed = int(digest[:8], 16)
        vector = []
        for i in range(384):
            value = ((seed + i * 97) % 2001 - 1000) / 1000.0
            vector.append(round(value, 6))
        resp = {'embedding': vector}
        payload = json.dumps(resp).encode('utf-8')
        self.send_response(200)
        self.send_header('Content-Type', 'application/json')
        self.send_header('Content-Length', str(len(payload)))
        self.end_headers()
        self.wfile.write(payload)

if __name__ == '__main__':
    print(f'Serving fallback embedding service on http://{HOST}:{PORT}')
    server = HTTPServer((HOST, PORT), Handler)
    server.serve_forever()
