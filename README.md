<p align="center">
  <a href="https://skillicons.dev">
    <img src="https://skillicons.dev/icons?i=docker,php,mysql,laravel" alt="tecnologias"/>
  </a>
</p>

---
[![wakatime](https://wakatime.com/badge/user/0a37bb0e-06f5-473c-8296-dc600e1c0d35/project/e85780b8-3e2d-40ef-966b-29e871fabd76.svg)](https://wakatime.com/badge/user/0a37bb0e-06f5-473c-8296-dc600e1c0d35/project/e85780b8-3e2d-40ef-966b-29e871fabd76)

---

# Market Control

A ideia desse projeto é criar um sistema para controlar os gastos de uma família com o mercado. Basta ter o QR code do 
NFCe e o sistema irá buscar os dados e salvar no banco de dados para posteriores consultas e relatórios.

## Iniciando o Projeto
- Rodar os seguintes comandos:
    ```bash
      docker-compose up
      make bakcnd-bash
      chown www-data:www-data -R storage/framework
      chown www-data:www-data -R storage/logs/
      cp .env.example .env
      php artisan key:generate
    ```
- Popular as variáveis de ambiente do arquivo `.env`.
  - Caso tenha mais de um username permitido a acessar o app via telegram, adicione os usernames (sem o @ na frente) separados por vírgula na variável `APP_TELEGRAM_ALLOWED_USERNAMES`.
  - Popular a variável `APP_TELEGRAM_BOT_TOKEN` com o token do bot criado no telegram.

## Comandos Make
- `make bakcnd-bash` Inicia os containers e entra no bash do backend

## Comandos para interagir com o bot
- `/nfce` - Inicia o processo de salvar uma NFCe no banco de dados

## Acessos 
- [PHP My Admin](http://localhost:8080) > Usuário `root` Senha `123`