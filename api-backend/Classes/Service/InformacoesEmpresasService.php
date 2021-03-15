<?php

namespace Service;

use InvalidArgumentException;
use Repository\InformacoesEmpresasRepository;
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};
use Validator\DataValidator;

class InformacoesEmpresasService 
{
    
    public const TABELA = 'informacoes_empresas';
    public const USER = 'informacoes_empresa';
    public const USER_FK = ['empresa', 'informacoes_empresa'];
    public const FK = 'id_empresa_fk';

    public const RECURSOS_GET = ['listar', 'consultar_status'];
    public const RECURSOS_PUT = ['atualizar_status'];
    // public const RECURSOS_PUT = ['atualizar'];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $InformacoesEmpresasRepository;
    private object $DataValidator;

    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->InformacoesEmpresasRepository = new InformacoesEmpresasRepository();
        $this->DataValidator = new DataValidator();
    }

    /*===============================================================*/

    public function setDadosCorpoRequest($dados_request) 
    {
        $this->DataValidator->validarInformacoes(['cnpj', 'fone'], $dados_request);
        $this->dados_corpo_request = $dados_request;
    }

    /*===============================================================*/

    public function validarGet() 
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_GET, true)) 
            $retorno = ($recurso == 'consultar_status') ? $this->$recurso() : $this->consultarPeloID();
        else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);

        if ($retorno === null) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

        return $retorno;
    }

    /*===============================================================*/

    public function validarPut() 
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_PUT, true)) {
            $retorno = $this->$recurso();
        } else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);

        if ($retorno === null) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

        return $retorno;
    }

    /*===============================================================*/

    private function validarOutroUsuario($permissao = false)
    {   
        if ($this->dados['id_usuario'] > 0 && in_array($this->dados['tipo_usuario'], self::USER_FK)) {
            return true;
        } else {
            if (!$permissao)
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_URL_OBRIGATORIA); 
        }
        
        return true;
    }

    /*===============================================================*/

    private function consultarPeloID()
    {
        $validacao_user = $this->validarOutroUsuario(true);
        
        if ($validacao_user)      
            return $this->InformacoesEmpresasRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id'], self::FK, $this->dados['id_usuario']);

        return $this->InformacoesEmpresasRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function consultar_status()
    {
        $validacao_user = $this->validarOutroUsuario(true);
        
        return $this->InformacoesEmpresasRepository->consultarStatus($this->dados['id_usuario']);
    }

    /*===============================================================*/

    private function atualizar_status()
    {        
        FuncoesUtil::verficarCamposObrigatórios(['status', 'confirmacao'], $this->dados_corpo_request);

        $status = $this->dados_corpo_request['status'];
        $confirmacao = $this->dados_corpo_request['confirmacao'];

        if ($this->InformacoesEmpresasRepository->atualizarStatus($this->dados['id_usuario'], $status) > 0) {
            $this->InformacoesEmpresasRepository->getMySQL()->getDb()->commit();

            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->InformacoesEmpresasRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}