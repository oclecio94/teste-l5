# 📚 API de Cadastro de Pedidos de Compra - CodeIgniter 4

Este projeto é uma API REST desenvolvida em **CodeIgniter 4** e **MySQL**, responsável pelo gerenciamento de **pedidos de compra, clientes e produtos**.

---

## 📌 Requisitos

Antes de rodar o projeto, certifique-se de ter instalado:

- **PHP** (>= 8.1)
- **Composer**
- **MySQL**
- **Apache/Nginx** (Servidor Web)

---

## 🚀 Instalação e Configuração

### 1️⃣ Clone o repositório:

```bash
git clone https://github.com/oclecio94/teste-l5
cd teste-l5
```

### 2️⃣ Instale as dependências:

```bash
composer install
```

### 3️⃣ Configure o ambiente:

Renomeie o arquivo `.env.example` para `.env` e configure as credenciais do banco de dados.

### 4️⃣ Crie o banco de dados:

No MySQL, crie um banco de dados com o nome **teste-l5**.

### 5️⃣ Execute as migrations:

```bash
php spark migrate
```

### 6️⃣ Inicie o servidor:

```bash
php spark serve
```

A API estará disponível em: **[http://localhost:8080](http://localhost:8080)**

---

## 📌 Endpoints

### 🔹 Clientes

| Método | Endpoint         | Descrição                   |
| ------ | ---------------- | --------------------------- |
| GET    | `/clientes`      | Listar todos os clientes    |
| GET    | `/clientes/{id}` | Obter um cliente específico |
| POST   | `/clientes`      | Criar um novo cliente       |
| PUT    | `/clientes/{id}` | Atualizar um cliente        |
| DELETE | `/clientes/{id}` | Remover um cliente          |

**Exemplo de corpo da requisição para criação de cliente:**

```http
{
  "parametros": {
    "cpf_cnpj": "12345678901",
    "nome_razao_social": "Cliente Exemplo"
  }
}
```

### 🔹 Produtos

| Método | Endpoint         | Descrição                   |
| ------ | ---------------- | --------------------------- |
| GET    | `/produtos`      | Listar todos os produtos    |
| GET    | `/produtos/{id}` | Obter um produto específico |
| POST   | `/produtos`      | Criar um novo produto       |
| PUT    | `/produtos/{id}` | Atualizar um produto        |
| DELETE | `/produtos/{id}` | Remover um produto          |

**Exemplo de corpo da requisição para criação de produto:**

```http
{
  "parametros": {
    "nome": "Produto Exemplo",
    "preco": 100.50,
    "quantidade": 20,
    "descricao": "Descrição Exemplo"
  }
}
```

### 🔹 Pedidos

| Método | Endpoint        | Descrição                  |
| ------ | --------------- | -------------------------- |
| GET    | `/pedidos`      | Listar todos os pedidos    |
| GET    | `/pedidos/{id}` | Obter um pedido específico |
| POST   | `/pedidos`      | Criar um novo pedido       |
| PUT    | `/pedidos/{id}` | Atualizar um pedido        |
| DELETE | `/pedidos/{id}` | Remover um pedido          |

**Exemplo de corpo da requisição para criação de pedido:**

```http
{
	"parametros": {
  "cliente_id": 1,
  "status": "Em Aberto",
	"total" : 10.0,
  "produtos": [
    {
      "produto_id": 1,
      "quantidade": 1,
			"preco_unitario": 5
    },
    {
      "produto_id": 2,
      "quantidade": 1,
			"preco_unitario": 5
    }
  ]
 }
}
```

---

## 🔐 Autenticação com JWT

A API utiliza **JWT** para autenticação. O **token JWT** deve ser enviado no header `Authorization` em cada requisição.

### 🛠️ Gerando um Token JWT:

1. Utilize o site [jwt.io](https://jwt.io/)
2. Utilize a chave **JWT_SECRET** disponível no arquivo `.env`
3. O token gerado deve ser incluído no cabeçalho das requisições.

**Exemplo de uso do Token JWT:**

```http
GET /clientes HTTP/1.1
Host: localhost:8080
Authorization: Bearer SEU_TOKEN_AQUI
```

---

## 🎯 Paginação e Filtros

Todos os endpoints de listagem suportam **paginação e filtros**.

**Exemplo de requisição:**

```http
GET /clientes?page=1&per_page=10&search=João
```

**Parâmetros:**

- `page` → Página atual
- `per_page` → Número de registros por página
- `search` → Busca por nome, cpf_cnpj, nome_razao_social e status

---

## 🛠️ Tecnologias Utilizadas

- **PHP 8.1+**
- **CodeIgniter 4**
- **MySQL**
- **JWT (Json Web Token)**
- **Composer**

---

🚀 **API desenvolvida para o teste técnico de Desenvolvedor Back-End Jr!**
