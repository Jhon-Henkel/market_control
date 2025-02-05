<p align="center">
  <a href="https://github.com/lelouchfr/skill-icons">
    <img src="https://go-skill-icons.vercel.app/api/icons?i=docker,php,mysql,laravel,telegram" alt="tecnologias"/>
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
      php artisan migrate
    ```
- Popular as variáveis de ambiente do arquivo `.env`.
  - Caso tenha mais de um username permitido a acessar o app via telegram, adicione os usernames (sem o @ na frente) separados por vírgula na variável `APP_TELEGRAM_ALLOWED_USERNAMES`.
  - Popular a variável `APP_TELEGRAM_BOT_TOKEN` com o token do bot criado no telegram.

## Comandos Make
- `make bakcnd-bash` Inicia os containers e entra no bash do backend

## Comandos para interagir com o bot
- `/start` - Inicia uma nova conversa com o chatbot
- `/nfce` - Inicia o processo de salvar uma NFCe no banco de dados, aqui temos a possibilidade de processar tanto um link quanto um QR-Code. No final, perguntamos se deseja salvar uma movimentação de despesa no Finanças na Mão.
- `/month` - Relatório do mês atual
- `/lastpurchase` - Relatório da última compra
- `/end` - Finaliza a conversa com o chatbot

## Acessos 
- [PHP My Admin](http://localhost:8080) > Usuário `root` Senha `123`

## Integração com o Finanças na Mão
- Popular as variáveis no .env
  - `MFP_URL` - URL do Finanças na Mão
  - `MFP_TOKEN` - Token de autenticação MFP do Finanças na Mão
- No .env do Finanças na mão, popular as variáveis
  - `MARKET_CONTROL_HASH` - Hash do usuário do Market Control no Finanças na Mão

Obs.: Se ambas estiverem em container Docker, o container `mfp_app` e `mfp_db` devem estar na mesma network que o container do Market Control.
Para colocar na mesma network, basta usar os comandos:
```bash
  docker network ls
  docker network connect nome-da-rede nome-do-container
```
Para checar se está tudo certo, pode usar:
```bash
  docker network inspect nome-da-rede-market-control
```