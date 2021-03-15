<?php

namespace Service;

use InvalidArgumentException;
use Repository\ProdutosRepository;
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};

class ProdutosService 
{
    
    public const TABELA = 'produtos';
    public const USER = 'produto';
    public const USER_FK = 'informacoes_empresa';
    public const FK = 'id_informacoes_empresa_fk';

    public const RECURSOS_GET = ['listar', 'listarPelaCategoria'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $ProdutosRepository;

    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->ProdutosRepository = new ProdutosRepository();
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
            $retorno = $this->dados['id'] > 0 ? $this->consultarPeloID() : $this->$recurso();
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
        return $this->ProdutosRepository->consultar($this->dados['id']);    
    }

    /*===============================================================*/

    private function listar() 
    {
        if ($this->dados['tipo_usuario'] == 'usuario')
            return $this->ProdutosRepository->produtosUsuarios($this->dados['id_fk']);

        return $this->ProdutosRepository->listar(self::FK, $this->dados['id_usuario'], $this->dados['id']);
    }

    /*===============================================================*/

    private function listarPelaCategoria() 
    {
        if ($this->dados['id_fk']) 
            return $this->ProdutosRepository->listar(self::FK, $this->dados['id_usuario'], $this->dados['id'], $this->dados['id_fk']);
        else    
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_URL_OBRIGATORIA);
    }

    /*===============================================================*/

    private function deletar()
    {
        $this->validarOutroUsuario();

        return $this->ProdutosRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function cadastrar() 
    {        
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['nome_produto', 'preco', 'id_categoria', 'status'], $this->dados_corpo_request, 6);

        if ($this->ProdutosRepository->cadastrar($this->dados_corpo_request) > 0) {
            $id_inserido = $this->ProdutosRepository->getMySQL()->getDb()->lastInsertId();
            
            $this->ProdutosRepository->getMySQL()->getDb()->commit();

            return [ 'id_inserido' => $id_inserido ];
        }

        $this->ProdutosRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /*===============================================================*/

    private function atualizar()
    {
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['nome_produto', 'preco'], $this->dados_corpo_request, 5);

        if ($this->ProdutosRepository->atualizar($this->dados['id'], $this->dados_corpo_request) > 0) {
            $this->ProdutosRepository->getMySQL()->getDb()->commit();

            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->ProdutosRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}