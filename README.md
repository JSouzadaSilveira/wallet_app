# Wallet App

## Descrição

Wallet App é uma aplicação desenvolvida em Laravel que permite gerenciar usuários, contas e transações financeiras. A aplicação oferece funcionalidades como cadastro de usuários, criação de contas, registro de transações e validação de dados através de APIs externas.

## Requisitos

- PHP >= 8.0
- Composer
- MySQL
- Laravel >= 9.x

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/JSouzadaSilveira/wallet-app.git
cd wallet-app
```

### 2. Instale as dependências
```bash
composer install
```

### 3. Configure o ambiente
```bash
cp .env.example .env
```

### 4. Gere a chave de aplicativo
```bash
php artisan key:generate
```

### 4. Execute as migrações e popule o banco
```bash
php artisan migrate
php artisan db:seed
```

## Estrutura do Projeto

app/: Contém a lógica do aplicativo, incluindo modelos, controladores e requisições.
database/: Contém as migrações e seeders.
routes/: Contém as definições das rotas da aplicação.

## Funcionalidades

Cadastro de usuários
Criação de contas para usuários
Registro de transações entre contas
Validação de CEP usando a API ViaCEP
Captura de localização usando a API de geolocalização
Registro de logs de transações
Uso da API
Rotas

### Usuários
POST /api/users: Criar um novo usuário
GET /api/users: Listar todos os usuários

### Contas
POST /api/accounts: Criar uma nova conta
GET /api/accounts: Listar todas as contas

### Transações
POST /api/transactions: Registrar uma nova transação
GET /api/transactions: Listar todas as transações (com paginação e filtros)
GET /api/transactions/{id}: Recuperar uma transação por ID