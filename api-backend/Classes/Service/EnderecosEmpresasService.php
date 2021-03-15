<?php

namespace Service;

use InvalidArgumentException;
use Repository\EnderecosEmpresasRepository;
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};

class EnderecosEmpresasService 
{
    
    public const TABELA = 'enderecos_empresas';
    public const USER = 'enderecos_empresas';
    public const USER_FK = 'informacoes_empresa';
    public const FK = 'id_informacoes_empresa_fk';

    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = [''];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = [''];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $EnderecosEmpresasRepository;

    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->EnderecosEmpresasRepository = new EnderecosEmpresasRepository();
    }

    /*===============================================================*/

    public function setDadosCorpoRequest($dados_request) 
    {
        $this->dados_corpo_request = $dados_request;
    }

    /*===============================================================*/

    public function validarGet() 
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_GET, true)) 
            $retorno = ($this->dados['id'] > 0) ? $this->consultarPeloID() : $this->$recurso();
        else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);

        if ($retorno === null) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

        return $retorno;
    }
    
    /*===============================================================*/

    public function validarDelete() 
    {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_DELETE, true)) 
            if ($this->dados['id'] > 0) {
                $retorno = $this->$recurso();
            } else 
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);

        if ($retorno === null) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

        return $retorno;
    }

    /*===============================================================*/

    public function validarPost() {
        $retorno = null;
        $recurso = $this->dados['recurso'];

        if (in_array($recurso, self::RECURSOS_POST, true)) {
            $retorno = $this->$recurso();
        } else
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
            if ($this->dados['id'] > 0) {
                $retorno = $this->$recurso();
            } else 
                throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
        } else
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);

        if ($retorno === null) 
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);

        return $retorno;
    }

    /*===============================================================*/

    private function validarOutroUsuario($permissao = false)
    {   
        if ($this->dados['id_usuario'] > 0 && $this->dados['tipo_usuario'] == self::USER_FK) {
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
        $this->validarOutroUsuario();

        return $this->EnderecosEmpresasRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);    
    }

    /*===============================================================*/

    private function listar() 
    {
        $this->validarOutroUsuario();

        return $this->EnderecosEmpresasRepository->getMySQL()->getAll(self::TABELA, self::FK, $this->dados['id_usuario']);
    }

    /*===============================================================*/

    private function deletar()
    {
        $this->validarOutroUsuario();

        return $this->EnderecosEmpresasRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function cadastrar() 
    {        
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['id_bairro', 'rua', 'numero'], $this->dados_corpo_request, 7);

        if ($this->EnderecosEmpresasRepository->cadastrar($this->dados_corpo_request, $this->dados['id_usuario'])) {
            $id_inserido = $this->EnderecosEmpresasRepository->getMySQL()->getDb()->lastInsertId();
            
            $this->EnderecosEmpresasRepository->getMySQL()->getDb()->commit();

            return [ 'id_inserido' => $id_inserido ];
        }

        $this->EnderecosEmpresasRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }
}