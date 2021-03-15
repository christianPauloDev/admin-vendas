<?php

namespace Service;

use InvalidArgumentException;
use Repository\{
    EmpresasRepository,
    EnderecosEmpresasRepository,
    InformacoesEmpresasRepository,
};
use Util\{
    ConstantesGenericasUtil,
    FuncoesUtil
};

class EmpresasService 
{
    
    public const TABELA = 'empresas';
    public const USER = 'empresa';
    public const USER_FK = ['proprietario', 'cliente'];
    public const FK = 'id_proprietario_fk';

    public const RECURSOS_GET = ['listar', 'consultar'];
    public const RECURSOS_DELETE = ['deletar'];
    public const RECURSOS_POST = ['cadastrar'];
    //public const RECURSOS_PUT = ['atualizar', 'atualizar_dados', 'atualizar_senha'];
    public const RECURSOS_PUT = ['atualizar'];

    private array $dados = [];
    private array $dados_corpo_request = [];
    private object $EmpresasRepository;
    private object $InformacoesEmpresasRepository;
    private object $EnderecosEmpresasRepository;
    public function __construct($dados)
    {
        $this->dados = $dados;
        $this->EmpresasRepository = new EmpresasRepository();
        $this->InformacoesEmpresasRepository = new InformacoesEmpresasRepository();
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
        if ($this->dados['id_usuario'] > 0 && in_array($this->dados['tipo_usuario'], self::USER_FK) ) {
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
            return $this->EmpresasRepository->getOneByKey($this->dados['id'], self::FK, $this->dados['id_usuario']);

        return $this->EmpresasRepository->getOneByKey($this->dados['id']);
    }

    /*===============================================================*/

    private function listar() 
    {
        if ($this->dados['tipo_usuario'] == 'usuario')
            return $this->EmpresasRepository->empresasUsuarios($this->dados['id_usuario'], $this->dados['id_fk']);

        $this->validarOutroUsuario();

        return $this->EmpresasRepository->getAll($this->dados['id_usuario']);
    }

    /*===============================================================*/

    private function consultar() 
    {
        if ($this->dados['tipo_usuario'] == 'usuario')
            return $this->EmpresasRepository->empresasUsuariosConsulta($this->dados['id_usuario'], $this->dados['id']);
    }

    /*===============================================================*/

    private function deletar()
    {
        $this->validarOutroUsuario();

        return $this->EmpresasRepository->getMySQL()->delete(self::TABELA, $this->dados['id']);
    }

    /*===============================================================*/

    private function atualizar()
    {
        $this->validarOutroUsuario();

        FuncoesUtil::verficarCamposObrigatórios(['razao_social', 'email', 'login', 'status', 'id_especialidade', 'nome_fantasia', 'fone', 'cnpj', 'id_bairro', 'rua', 'numero'], $this->dados_corpo_request, 16);

        if ($this->EmpresasRepository->atualizar($this->dados['id'], $this->dados_corpo_request) > 0) {
            $this->EmpresasRepository->getMySQL()->getDb()->commit();

            return ConstantesGenericasUtil::MSG_ATUALIZADO_SUCESSO;
        }

        $this->EmpresasRepository->getMySQL()->getDb()->rollBack();

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_NAO_AFETADO);
    }
}