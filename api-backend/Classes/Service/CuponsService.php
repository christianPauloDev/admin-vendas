<?php

namespace Service;

use InvalidArgumentException;
use Repository\CuponsRepository;
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};

class CuponsService 
{
    
    public const TABELA = 'cupons';
    public const USER = 'cupons';
    public const USER_FK = ['informacoes_empresa', 'usuario'];
    public const FK = 'id_informacoes_empresa_fk';

    public const RECURSOS_GET = ['listar', 'validarParaUsuario'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $CuponsRepository;

    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->CuponsRepository = new CuponsRepository();
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
            $retorno = ($this->dados['id'] > 0  && $this->dados['recurso'] != 'validarParaUsuario') ? $this->consultarPeloID() : $this->$recurso();
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
        if ($this->dados['id_usuario'] > 0 && in_array($this->dados['tipo_usuario'], self::USER_FK)) {
            return true;
        } else {
            if (!$permissao) {
                if ($this->dados['tipo_usuario'] == 'usuario' && $this->dados['recurso'] == 'validarParaUsuario') {
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TOKEN_NAO_AUTORIZADO); 
                } else
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_URL_OBRIGATORIA); 
            }
        }
        
        return true;
    }

    /*===============================================================*/

    private function consultarPeloID()
    {
        $this->validarOutroUsuario();

        return $this->CuponsRepository->getMySQL()->getOneByKey(self::TABELA, $this->dados['id']);    
    }

    /*===============================================================*/
    
    private function validarParaUsuario()
    {
        return $this->CuponsRepository->dadosCupomUsuario($this->dados['id'], $this->dados['id_fk']);    
    }

    /*===============================================================*/
    
    private function listar() 
    {
        $this->validarOutroUsuario();

        if ($this->dados['id_fk'])
            return $this->CuponsRepository->getMySQL()->getAll(self::TABELA, 'tipo_desconto', $this->dados['id_fk']);

        $retorno = $this->CuponsRepository->getMySQL()->getAll(self::TABELA, self::FK, $this->dados['id_usuario']);
        
        return $retorno;
    }

    /*===============================================================*/

    private function deletar()
    {
        $this->validarOutroUsuario();

        return $this->CuponsRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function cadastrar() 
    {        
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['titulo', 'tipo_desconto', 'valor_desconto', 'data_validade'], $this->dados_corpo_request, 5);
        
        if ($this->CuponsRepository->cadastrar($this->dados_corpo_request, $this->dados['id_usuario'])) {
            $id_inserido = $this->CuponsRepository->getMySQL()->getDb()->lastInsertId();
            
            $this->CuponsRepository->getMySQL()->getDb()->commit();

            return [ 'id_inserido' => $id_inserido ];
        }

        $this->CuponsRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /*===============================================================*/

    private function atualizar()
    {
        $this->validarOutroUsuario();
        
        FuncoesUtil::verficarCamposObrigatórios(['titulo', 'tipo_desconto', 'valor_desconto', 'data_validade', 'status'], $this->dados_corpo_request, 6);

        if ($this->CuponsRepository->atualizar($this->dados['id'], $this->dados_corpo_request) > 0) {
            $this->CuponsRepository->getMySQL()->getDb()->commit();

            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->CuponsRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}