# Sistema de Cadastro de Veiculos

Aplicacao web simples para cadastrar e listar veiculos, com frontend em HTML, CSS e JavaScript e backend em PHP com SQLite.

## Funcionalidades

- Cadastro de veiculos com validacao no frontend e no backend
- Persistencia local em banco SQLite
- Listagem de veiculos via endpoint JSON
- Tratamento de erros com mensagens amigaveis

## Estrutura do Projeto

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
	- pdo_sqlite

Para verificar modulos carregados:

php -m

## Como Executar

1. Abra terminal na raiz do projeto:

C:/Users/Joyce/Desktop/veiculos

2. Inicie servidor embutido do PHP apontando para a pasta veiculos:

php -S localhost:8001 -t veiculos

3. Abra no navegador:

- Cadastro: http://localhost:8001/index.html
- Lista: http://localhost:8001/lista.html

Observacao:
- Se preferir a porta 8000, verifique antes se ela esta livre.

## Banco de Dados

- O arquivo SQLite e criado automaticamente em [veiculos/php/veiculos.sqlite](veiculos/php/veiculos.sqlite) na primeira operacao.
- A tabela veiculos e criada automaticamente em [veiculos/php/conexao.php](veiculos/php/conexao.php).

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

### Erro interno ao cadastrar veiculo

Verifique:

1. Se PDO e pdo_sqlite estao habilitados
2. Permissao de escrita na pasta [veiculos/php](veiculos/php)
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