<?php

namespace Util;

use InvalidArgumentException;

class FuncoesUtil
{

    public static function verficarCamposObrigatórios($obrigatorios, $dados_corpo_request, $qtd_dados = 0) {
        $campos_obrigatorios = true;

        if (count($obrigatorios) != count($dados_corpo_request) && $qtd_dados == 0) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_DADOS_OBRIGATORIO);

        foreach ($obrigatorios as $idx => $value) {
            if ( ( !isset($dados_corpo_request[$value]) ) || strlen($dados_corpo_request[$value]) == 0 )
                $campos_obrigatorios = $campos_obrigatorios && false;
        }

        // echo'<pre>';var_dump($campos_obrigatorios);exit;

        if ($campos_obrigatorios) 
            return true;
        else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_DADOS_OBRIGATORIO);
    }

    /*=============================================*//*=============================================*/
    
    public static function compararSenhas($senha, $confirmar_senha) {

        if ($senha !== $confirmar_senha) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SENHA_CONFIRMAR);

        if (strlen($senha) < 8) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TAMANHO_SENHA);
    }

    /*=============================================*//*=============================================*/

}