# Sistema de Cadastro de Veiculos

Aplicacao web simples para cadastrar e listar veiculos, com frontend em HTML, CSS e JavaScript e backend em PHP com SQLite.

## Funcionalidades

- Cadastro de veiculos com validacao no frontend e no backend
- Persistencia local em banco SQLite
- Listagem de veiculos via endpoint JSON
- Tratamento de erros com mensagens amigaveis

## Estrutura do Projeto

- [.env](.env): variáveis de ambiente (credenciais MySQL)
- [.env.example](.env.example): template de configuração
- [veiculos/index.html](veiculos/index.html): tela de cadastro
- [veiculos/lista.html](veiculos/lista.html): tela de listagem
- [veiculos/js/cadastro.js](veiculos/js/cadastro.js): logica de envio e validacao do formulario
- [veiculos/js/lista.js](veiculos/js/lista.js): carregamento e renderizacao da tabela
- [veiculos/php/conexao.php](veiculos/php/conexao.php): conexao PDO e criacao da tabela
- [veiculos/php/inserir.php](veiculos/php/inserir.php): endpoint de cadastro
- [veiculos/php/listar.php](veiculos/php/listar.php): endpoint de consulta
- [veiculos/style/style.css](veiculos/style/style.css): estilos da interface

## Requisitos

- PHP 8.0 ou superior
- Extensoes PHP habilitadas:
  - PDO
  - pdo_mysql
- MySQL 5.7 ou superior (ou MariaDB)

Para verificar modulos carregados:

php -m

### Instalacao do MySQL

#### Windows

1. Baixe o MySQL Community Server em https://dev.mysql.com/downloads/mysql/
2. Execute o instalador e siga as instrucoes
3. Configure a senha do usuario root
4. MySQL Server estara executando como servico do Windows

#### macOS

com Homebrew:

brew install mysql
brew services start mysql

#### Linux (Ubuntu/Debian)

sudo apt-get update
sudo apt-get install mysql-server

## Configuracao do Banco de Dados

1. Copie `.env.example` para `.env`:

cp .env.example .env

2. Edite `.env` com suas credenciais MySQL:

DB_HOST=localhost
DB_PORT=3306
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
DB_NAME=veiculos

3. Crie o banco de dados no MySQL:

mysql -u seu_usuario -p -e "CREATE DATABASE IF NOT EXISTS veiculos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

A tabela sera criada automaticamente na primeira execucao.

## Como Executar

<<<<<<< HEAD
1. Abra terminal na raiz do projeto:
=======
1. Abra terminal na raiz do projeto
>>>>>>> 23b37b45d8db0ce6fa06bc9515dd3dad8c10ff6a

2. Inicie servidor embutido do PHP apontando para a pasta veiculos:

php -S localhost:8001 -t veiculos

3. Abra no navegador:

- Cadastro: http://localhost:8001/index.html
- Lista: http://localhost:8001/lista.html

Observacao:
- Se preferir a porta 8000, verifique antes se ela esta livre.

## Banco de Dados

- Banco MySQL definido via variáveis de ambiente em [.env](.env)
- Tabela `veiculos` é criada automaticamente na primeira operação
- Credenciais editáveis em [.env](.env)

Configuração padrão (editar conforme necessário):

```
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=
DB_NAME=veiculos
```

Campos da tabela:

- id
- placa (unico)
- marca
- modelo
- ano_fabricacao
- ano_modelo
- cor
- combustivel
- quilometragem
- chassi (unico)
- renavam (unico)
- data_cadastro
- observacoes
- criado_em

## Endpoints

Documentacao OpenAPI disponivel em [openapi.yaml](openapi.yaml).

### Como visualizar no Swagger Editor

1. Acesse https://editor.swagger.io
2. Abra o conteudo de [openapi.yaml](openapi.yaml)
3. Ajuste o server para a porta que estiver usando localmente

### POST /php/inserir.php

Insere um novo veiculo.

Corpo JSON esperado:

{
	"placa": "ABC1D23",
	"marca": "Volkswagen",
	"modelo": "Gol",
	"ano_fabricacao": 2017,
	"ano_modelo": 2018,
	"cor": "Prata",
	"combustivel": "Flex",
	"quilometragem": 75300,
	"chassi": "9BWZZZ377VT004251",
	"renavam": "12345678901",
	"data_cadastro": "2026-04-23",
	"observacoes": "Revisado"
}

Respostas comuns:

- 201: cadastrado com sucesso
- 409: placa, chassi ou renavam ja existente
- 422: dados invalidos
- 500: erro interno no servidor

### GET /php/listar.php

Retorna lista de veiculos cadastrados em JSON.

Resposta de sucesso:

{
	"success": true,
	"message": "Consulta realizada com sucesso.",
	"data": []
}

## Validacoes Importantes

No cadastro sao validados:

- Placa: 7 a 8 caracteres (A-Z, 0-9 e hifen)
- Ano de fabricacao e modelo em intervalo valido
- Quilometragem nao negativa
- Chassi com 17 caracteres, sem I, O e Q
- Renavam com 11 digitos
- Data no formato AAAA-MM-DD

## Solucao de Problemas

### Erro de conexao ao banco de dados

Verifique:

1. Se o MySQL esta executando
2. Se as credenciais em [.env](.env) estao corretas
3. Se o banco `veiculos` foi criado:

mysql -u seu_usuario -p -e "SHOW DATABASES;"

4. Se PDO e pdo_mysql estao habilitados:

php -m | grep -i pdo

### Falha ao inicializar tabela

Se a tabela nao for criada automaticamente, execute manualmente:

mysql -u seu_usuario -p veiculos < schema.sql

(se um arquivo schema.sql for fornecido)

### Erro interno ao cadastrar veiculo

Verifique:

1. Se pdo_mysql esta habilitado
2. Permissao de escrita do usuario MySQL
3. Se o endpoint [veiculos/php/inserir.php](veiculos/php/inserir.php) esta sendo acessado pela URL correta

### Porta ja em uso

Se a porta 8000 estiver ocupada, rode em outra porta:

php -S localhost:8001 -t veiculos

Para diagnosticar processo na porta 8000 no PowerShell:

Get-NetTCPConnection -LocalPort 8000

## Validacao Rapida de Sintaxe

php -l veiculos/php/conexao.php
php -l veiculos/php/inserir.php
php -l veiculos/php/listar.php

## Melhorias Futuras

- Edicao e exclusao de registros
- Busca e paginacao na listagem
- Testes automatizados de API
- Autenticacao basica para proteger endpoints
