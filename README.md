API de Cadastro de Pedidos de compra - CodeIgniter 4

Este projeto é uma API REST desenvolvida em CodeIgniter 4 e MySQL, responsável pelo cadastro de pedidos de compra, clientes e produtos.

📌 Requisitos

Antes de rodar o projeto, certifique-se de ter instalado:

PHP (>= 8.1)

Composer

MySQL

Apache/Nginx (Servidor Web)

🚀 Instalação e Configuração

Clone o repositório

git clone https://github.com/oclecio94/teste-l5
cd teste-l5

Instale as dependências

composer install

Renomeie o arquivo de configuração .env.example pra .env

Crie o banco de dados teste-l5

Execute as migrations para criar as tabelas

php spark migrate

Inicie o servidor

php spark serve

A API estará disponível em: http://localhost:8080

📌 Endpoints

🔹 Clientes

Método

Endpoint

Descrição

GET

/clientes

Listar todos os clientes

GET

/clientes/{id}

Obter um cliente específico

POST

/clientes

Criar um novo cliente

PUT

/clientes/{id}

Atualizar um cliente

DELETE

/clientes/{id}

Remover um cliente

🔹 Produtos

Método

Endpoint

Descrição

GET

/produtos

Listar todos os produtos

GET

/produtos/{id}

Obter um produto específico

POST

/produtos

Criar um novo produto

PUT

/produtos/{id}

Atualizar um produto

DELETE

/produtos/{id}

Remover um produto

🔹 Pedidos

Método

Endpoint

Descrição

GET

/pedidos

Listar todos os pedidos

GET

/pedidos/{id}

Obter um pedido específico

POST

/pedidos

Criar um novo pedido

PUT

/pedidos/{id}

Atualizar um pedido

DELETE

/pedidos/{id}

Remover um pedido

🔐 Autenticação com JWT

A API utiliza JWT para autenticação. O token JWT deve ser enviado no header Authorization em cada requisição.

Gerar Token no site https://jwt.io/ com a chave JWT_SECRET que está no .env

Exemplo de uso do Token JWT

GET /clientes HTTP/1.1
Host: localhost:8080
Authorization: Bearer SEU_TOKEN_AQUI

🎯 Paginação e Filtros

Todos os endpoints de listagem suportam paginação e filtros.

Exemplo:

GET /clientes?page=1&limit=10&nome=João

Parâmetros:

page: Página atual

limit: Número de registros por página

Outros filtros podem ser aplicados com base nos campos disponíveis

🛠 Tecnologias Utilizadas

PHP 8.1+

CodeIgniter 4

MySQL

JWT (Json Web Token)

Composer

🚀 API desenvolvida para o teste técnico de Desenvolvedor Back-End Jr!
