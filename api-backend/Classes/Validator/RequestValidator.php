<?php

namespace Validator;

use InvalidArgumentException;
use Validator\AuthValidator;

use Service\{
    CategoriasService,
    CuponsService,
    EmpresasService,
    EnderecosEmpresasService,
    InformacoesEmpresasService,
    ProdutosService,
};
use Util\{
    ConstantesGenericasUtil,
    JsonUtil
};

class RequestValidator
{

    private $request;
    private array $dados_request;
    private object $AuthValidator;
    
    const GET = 'GET';
    const DELETE = 'DELETE';
    const POST = 'POST';

    const EMPRESAS = 'EMPRESAS';
    const INFORMACOES_EMPRESAS = 'INFORMACOES_EMPRESAS';
    const ENDERECOS_EMPRESAS = 'ENDERECOS_EMPRESAS';
    const CATEGORIAS = 'CATEGORIAS';
    const PRODUTOS = 'PRODUTOS';
    const CUPONS = 'CUPONS';

    public function __construct($request)
    {
        $this->request = $request;
        $this->AuthValidator = new AuthValidator();
    }

    /*========================================================*/
    
    public function processarRequest() 
    {
        $retorno = (ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA);

        if (in_array($this->request['metodo'], ConstantesGenericasUtil::TIPO_REQUEST, true))
            $retorno = $this->direcionarRequest();

        return $retorno;
    }

    /*========================================================*/

    private function direcionarRequest() 
    {    
        if ($this->request['metodo'] === self::GET && $this->request['recurso'] == 'validar_token') {
            return $this->AuthValidator->checkAuth($this->request['tipo_usuario'], $this->request['token']);
        } else if ($this->request['metodo'] === self::POST && $this->request['recurso'] == 'login') {
            $this->dados_request = JsonUtil::tratarCorpoRequisicaoJson();

            return $this->AuthValidator->login(
                $this->request['tipo_usuario'], 
                $this->dados_request['login'], 
                $this->dados_request['senha'],
            );

        } else {
            if ($this->request['metodo'] !== self::GET && $this->request['metodo'] !== self::DELETE) 
                $this->dados_request = JsonUtil::tratarCorpoRequisicaoJson();

            $gets_sem_validacao = ['PRODUTOS', 'CUPONS'];
            
            if (!(in_array($this->request['rota'], $gets_sem_validacao) && $this->request['metodo'] === self::GET))
                $this->AuthValidator->checkAuth($this->request['tipo_usuario']);
            
            $metodo = $this->request['metodo'];

            return $this->$metodo();
        }
    }

    /*========================================================*/

    private function get() 
    {
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;

        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_GET, 'strict')) {
            switch ($this->request['rota']) {
                
                
                case self::EMPRESAS: 
                    $EmpresasService = new EmpresasService($this->request);
                    $retorno = $EmpresasService->validarGet();                    
                    break;

                case self::INFORMACOES_EMPRESAS: 
                    $InformacoesEmpresas = new InformacoesEmpresasService($this->request);
                    $retorno = $InformacoesEmpresas->validarGet();                    
                    break;
        
                case self::CATEGORIAS: 
                    $Categoria = new CategoriasService($this->request);
                    $retorno = $Categoria->validarGet();                    
                    break;
                
                case self::PRODUTOS: 
                    $Produto = new ProdutosService($this->request);
                    $retorno = $Produto->validarGet();                    
                    break;
                
                case self::ENDERECOS_EMPRESAS: 
                    $EnderecosEmpresas = new EnderecosEmpresasService($this->request);
                    $retorno = $EnderecosEmpresas->validarGet();                    
                    break;

                case self::CUPONS:
                    $CuponsService = new CuponsService($this->request);
                    $retorno = $CuponsService->validarGet();
                    break;

                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                    break;
            }
        }

        return $retorno;
    }

    /*========================================================*/

    private function delete() 
    {
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;

        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_DELETE, 'strict')) {
            switch ($this->request['rota']) {
                
                
                case self::EMPRESAS: 
                    $EmpresasService = new EmpresasService($this->request);
                    $retorno = $EmpresasService->validarDelete();                    
                    break;
                

                
                case self::CATEGORIAS: 
                    $Categoria = new CategoriasService($this->request);
                    $retorno = $Categoria->validarDelete();                    
                    break;
                
                case self::PRODUTOS: 
                    $Produto = new ProdutosService($this->request);
                    $retorno = $Produto->validarDelete();                    
                    break;

                case self::ENDERECOS_EMPRESAS: 
                    $EnderecosEmpresas = new EnderecosEmpresasService($this->request);
                    $retorno = $EnderecosEmpresas->validarDelete();                    
                    break;

                case self::CUPONS:
                    $CuponsService = new CuponsService($this->request);
                    $retorno = $CuponsService->validarDelete();
                    break;

                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                    break;
            }
        }

        return $retorno;
    }

    /*========================================================*/

    private function post() 
    {
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;

        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_POST, 'strict')) {
            switch ($this->request['rota']) {
                case self::EMPRESAS: 
                    $EmpresasService = new EmpresasService($this->request);
                    $EmpresasService->setDadosCorpoRequest($this->dados_request);
                    $retorno = $EmpresasService->validarPost();                    
                    break;
                
                
                case self::CATEGORIAS: 
                    $Categoria = new CategoriasService($this->request);
                    $Categoria->setDadosCorpoRequest($this->dados_request);
                    $retorno = $Categoria->validarPost();                    
                    break;
                
                case self::PRODUTOS: 
                    $Produto = new ProdutosService($this->request);
                    $Produto->setDadosCorpoRequest($this->dados_request);
                    $retorno = $Produto->validarPost();                    
                    break;
                
                case self::ENDERECOS_EMPRESAS: 
                    $EnderecosEmpresas = new EnderecosEmpresasService($this->request);
                    $EnderecosEmpresas->setDadosCorpoRequest($this->dados_request);
                    $retorno = $EnderecosEmpresas->validarPost();                    
                    break;

                case self::CUPONS:
                    $CuponsService = new CuponsService($this->request);
                    $CuponsService->setDadosCorpoRequest($this->dados_request);
                    $retorno = $CuponsService->validarPost();
                    break;

                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                    break;
            }
        }
        
        return $retorno;
    }

    /*========================================================*/

    private function put() 
    {
        $retorno = ConstantesGenericasUtil::MSG_ERRO_TIPO_ROTA;

        if (in_array($this->request['rota'], ConstantesGenericasUtil::TIPO_PUT, 'strict')) {
            switch ($this->request['rota']) {
                case self::EMPRESAS: 
                    $EmpresasService = new EmpresasService($this->request);                    
                    $EmpresasService->setDadosCorpoRequest($this->dados_request);
                    $retorno = $EmpresasService->validarPut();                    
                    break;

                case self::INFORMACOES_EMPRESAS: 
                    $InformacoesEmpresas = new InformacoesEmpresasService($this->request);
                    $InformacoesEmpresas->setDadosCorpoRequest($this->dados_request);
                    $retorno = $InformacoesEmpresas->validarPut();                    
                    break;

                case self::CATEGORIAS: 
                    $Categoria = new CategoriasService($this->request);
                    $Categoria->setDadosCorpoRequest($this->dados_request);
                    $retorno = $Categoria->validarPut();                    
                    break;
                
                case self::PRODUTOS: 
                    $Produto = new ProdutosService($this->request);
                    $Produto->setDadosCorpoRequest($this->dados_request);
                    $retorno = $Produto->validarPut();                    
                    break;

                case self::ENDERECOS_EMPRESAS: 
                    $EnderecosEmpresas = new EnderecosEmpresasService($this->request);
                    $EnderecosEmpresas->setDadosCorpoRequest($this->dados_request);
                    $retorno = $EnderecosEmpresas->validarPut();                    
                    break;

                case self::CUPONS:
                    $CuponsService = new CuponsService($this->request);
                    $CuponsService->setDadosCorpoRequest($this->dados_request);
                    $retorno = $CuponsService->validarPut();
                    break;

                default:
                    throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_RECURSO_INEXISTENTE);
                    break;
            }
        }

        return $retorno;
    }
}