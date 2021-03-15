<?php

use Util\{
    ConstantesGenericasUtil,
    JsonUtil,
    RotasUtil
};

use Validator\{
    RequestValidator,
    XSSSecurityAccessOrigin
};

include 'bootstrap.php';

try {

    $permission = new XSSSecurityAccessOrigin($_SERVER['SERVER_NAME']);
    $permission->accessControlAllowOrigin();

    $RequestValidator = new RequestValidator(RotasUtil::getRotas());
    $retorno = $RequestValidator->processarRequest();

    $JsonUtil = new JsonUtil();
    $JsonUtil->processarArrayParaRetornar($retorno);
} catch (Exception $exception) {
    $erro = ConstantesGenericasUtil::TIPO_ERRO;

    $mensagens_alertas = [
        ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO,
        ConstantesGenericasUtil::MSG_ERRO_DADOS_OBRIGATORIO,
        ConstantesGenericasUtil::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO,
        ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO,
        ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO,
        ConstantesGenericasUtil::MSG_ERRO_TITULO_OBRIGATORIO,
    ];

    if (in_array($exception->getMessage(), $mensagens_alertas))
        $erro = ConstantesGenericasUtil::TIPO_ALERTA;

    echo json_encode([
        ConstantesGenericasUtil::TIPO => $erro,
        ConstantesGenericasUtil::RESPOSTA => $exception->getMessage(),
    ]);
}
