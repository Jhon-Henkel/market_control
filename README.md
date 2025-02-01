<p align="center">
  <a href="https://skillicons.dev">
    <img src="https://skillicons.dev/icons?i=docker,php,mysql,laravel" alt="tecnologias"/>
  </a>
</p>

# Market Control

A ideia desse projeto é criar um sistema para controlar os gastos de uma família com o mercado. Basta ter o QR code do 
NFCe e o sistema irá buscar os dados e salvar no banco de dados para posteriores consultas e relatórios.

## Iniciando o Projeto
Rodar:
```bash
  docker-compose up
  make bakcnd-bash
  chown www-data:www-data -R storage/framework
  chown www-data:www-data -R storage/logs/
```

## Comandos Make
- `make bakcnd-bash` Inicia os containers e entra no bash do backend

## Acessos 
- [PHP My Admin](http://localhost:8080) > Usuário `root` Senha `123`