import urllib.request, json, sys

url = 'http://127.0.0.1:8001/embed'
payload = {'text': 'hello world'}
req = urllib.request.Request(url, data=json.dumps(payload).encode('utf-8'), headers={'Content-Type': 'application/json'})
try:
    with urllib.request.urlopen(req, timeout=10) as resp:
        body = resp.read().decode('utf-8')
        obj = json.loads(body)
        emb = obj.get('embedding', [])
        print('status: OK')
        print('embedding_length:', len(emb))
        print('sample[0:10]:', emb[:10])
except Exception as e:
    print('error:', e)
    sys.exit(1)
