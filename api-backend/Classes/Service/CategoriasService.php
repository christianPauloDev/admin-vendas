<?php

namespace Service;

use InvalidArgumentException;
use Repository\CategoriasRepository;
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};

class CategoriasService 
{
    
    public const TABELA = 'categorias';
    public const USER = 'categoria';
    public const USER_FK = 'informacoes_empresa';
    public const FK = 'id_informacoes_empresa_fk';

    public const RECURSOS_GET = ['listar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $CategoriasRepository;

    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->CategoriasRepository = new CategoriasRepository();
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

    public function validarPost() 
    {
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
        return $this->CategoriasRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);    
    }

    /*===============================================================*/

    private function listar() 
    {
        return $this->CategoriasRepository->getMySQL()->getAll(self::TABELA, self::FK, $this->dados['id_usuario']);
    }

    /*===============================================================*/

    private function deletar()
    {
        $this->validarOutroUsuario();

        return $this->CategoriasRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function cadastrar() 
    {        
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['titulo'], $this->dados_corpo_request);
        
        if ($this->CategoriasRepository->cadastrar($this->dados_corpo_request, $this->dados['id_usuario'])) {
            $id_inserido = $this->CategoriasRepository->getMySQL()->getDb()->lastInsertId();
            
            $this->CategoriasRepository->getMySQL()->getDb()->commit();

            return [ 'id_inserido' => $id_inserido ];
        }

        $this->CategoriasRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /*===============================================================*/

    private function atualizar()
    {
        $this->validarOutroUsuario();
        
        FuncoesUtil::verficarCamposObrigatórios(['titulo'], $this->dados_corpo_request);

        if ($this->CategoriasRepository->atualizar($this->dados['id'], $this->dados_corpo_request) > 0) {
            $this->CategoriasRepository->getMySQL()->getDb()->commit();

            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->CategoriasRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}