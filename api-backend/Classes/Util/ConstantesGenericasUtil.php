<?php

namespace Util;

abstract class ConstantesGenericasUtil
{
    /* REQUESTS */
    public const TIPO_REQUEST = ['GET', 'POST', 'DELETE', 'PUT'];

    public const TIPO_GET = [
        'EMPRESAS', 'INFORMACOES_EMPRESAS', 'ENDERECOS_EMPRESAS', 'CATEGORIAS', 'PRODUTOS', 'CUPONS'
    ];
    public const TIPO_POST = [
        'EMPRESAS', 'ENDERECOS_EMPRESAS', 'CATEGORIAS', 'PRODUTOS', 'CUPONS'
    ];
    public const TIPO_DELETE = [
        'EMPRESAS', 'ENDERECOS_EMPRESAS', 'CATEGORIAS', 'PRODUTOS', 'CUPONS'
    ];
    public const TIPO_PUT = [
        'EMPRESAS', 'INFORMACOES_EMPRESAS', 'ENDERECOS_EMPRESAS', 'CATEGORIAS', 'PRODUTOS', 'CUPONS'
    ];

    public const USUARIOS = ['empresa'];

    /* ERROS */
    public const MSG_ERRO_TIPO_ROTA = 'Rota não permitida!';
    public const MSG_ERRO_RECURSO_INEXISTENTE = 'Recurso inexistente!';
    public const MSG_ERRO_GENERICO = 'Algum erro ocorreu na requisição!';
    public const MSG_ERRO_SEM_RETORNO = 'Nenhum registro encontrado!';
    public const MSG_ERRO_NAO_AFETADO = 'Nenhum registro afetado!';
    public const MSG_ERRO_TOKEN_NAO_AUTORIZADO = 'Sessão Expirada! Realize o login novamente!';
    public const MSG_ERR0_JSON_VAZIO = 'O Corpo da requisição não pode ser vazio!';

    /* SUCESSO */
    public const MSG_DELETADO_SUCESSO = 'Registro deletado com Sucesso!';
    public const MSG_ATUALIZADO_SUCESSO = 'Registro atualizado com Sucesso!';

    /* RECURSO USUARIOS */
    public const MSG_ERRO_ID_OBRIGATORIO = 'ID é obrigatário!';
    public const MSG_ERRO_DADOS_INVALIDOS = 'Login ou Senha Incorreto(s)!';
    public const MSG_ERRO_LOGIN_SENHA_OBRIGATORIO = 'Login e Senha são obrigatórios!';
    public const MSG_ERRO_DADOS_OBRIGATORIO = 'Preencha todos os campos obrigatórios!';
    public const MSG_ERRO_URL_OBRIGATORIA = 'Informe a rota correta!';

    /* RETORNO JSON */
    const TIPO_SUCESSO = 'sucesso';
    const TIPO_ERRO = 'erro';
    const TIPO_ALERTA = 'warning';

    /* OUTRAS */
    public const SIM = 'S';
    public const TIPO = 'tipo';
    public const RESPOSTA = 'resposta';

    public const MSG_ERRO_TITULO_OBRIGATORIO = "Informe o cupom!";
}
