```toml
name = 'chat'
method = 'POST'
url = 'http://localhost/api/chat/webhook'
sortWeight = 1000000
id = '7145b967-494d-45ab-9b7e-9cd649d4c7e2'

[[headers]]
key = 'Content-Type'
value = 'application/json'

[body]
type = 'JSON'
raw = '''
{
           "message": {
             "chat": { "id": 12345678 },
             "text": "/nfce"
           }
         }'''
```
