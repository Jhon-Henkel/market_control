```toml
name = 'insert'
method = 'POST'
url = 'http://localhost/api/nfce'
sortWeight = 1000000
id = '2ffd704b-3ff4-4015-8c63-1743833c11fa'

[body]
type = 'JSON'
raw = '''
{
  nfce_url: '{{nfce_link}}'
}'''
```
