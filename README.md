## Teste Técnico TROC
Fase técnica do processo seletivo para a vaga de Desenvolvedor Full Stack PL  
Candidato : Christian Paulo da Costa Almeida
## Instalação

#### Adicione o arquivo `bootstrap.php` na pasta `./api-backend/` como a demonstração abaixoo:

```php
<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ERROR);

/* CONSTANTES DO SEU BANCO */
define(HOST, 'localhost');
define(BANCO, 'projeto_vendas');
define(USUARIO, 'root');
define(SENHA, '123456');

/* OUTRAS CONSTANTES */
define(DS, DIRECTORY_SEPARATOR);
define(DIR_APP, __DIR__);

// COLOQUE O NOME DA PASTA DO PROJETO
define(DIR_PROJETO, 'ProjetoVendas/api-backend/');

// ___ATENÇÃO___
// PODEM OCORRER ERROS NOS CADASTROS SE AS PASTAS
// NÃO FOREM INSTALADAS CORRETAMENTE

if (file_exists('autoload.php')) {
    include 'autoload.php';
} else {
    die('Falha ao carregar autoload!');
}
```

## Login no [ADMIN](http://adminvendas.2dmedia.com.br/api-front/empresa/)

Usuário : empresa  
Senha :  123

## Funções ADMIN

* Login e Logout
* CRUD Categorias
* CRUD Produtos
* CRUD Cupons

## Funções [CLIENTE](http://adminvendas.2dmedia.com.br/usuariovendas/)

* Listagem das Categorias e Produtos
* Adicionar Cupom de Desconto
* Carrinho de Compras

