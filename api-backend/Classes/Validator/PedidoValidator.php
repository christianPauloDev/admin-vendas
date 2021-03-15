<?php

namespace Validator;

use DB\MySQL;

use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

use Repository\{
    ProdutosRepository,
    AdicionaisRepository,
    FormasPagamentosRepository
};

class PedidoValidator
{
    private object $MySQL;
    private object $ProdutosRepository;
    private object $AdicionaisRepository;
    private object $FormasPagamentosRepository;

    public function __construct()
    {
        $this->MySQL = new MySQL();
        $this->ProdutosRepository = new ProdutosRepository();
        $this->AdicionaisRepository = new AdicionaisRepository();
        $this->FormasPagamentosRepository = new FormasPagamentosRepository();
    }

    /*========================================================*/

    public function verificarProdutosEmpresa($produtos, $id_informacao_empresa)
    {
        foreach($produtos as $produto) {
            $consulta = $this->ProdutosRepository->consultarEmpresaProduto($produto['produto']['id'], $id_informacao_empresa);
            $this->ProdutosRepository->getMySQL()->getDb()->commit();

            // Caso o produto não seja da empresa
            if ($consulta == 0) return 1;

            foreach($produto['adicionais'] as $adicional) {
                $consulta = $this->AdicionaisRepository->consultarAdicionaisProduto($adicional['id'], $produto['produto']['id']);
                $this->AdicionaisRepository->getMySQL()->getDb()->commit();

                // Caso o adicional não seja do produto
                if ($consulta == 0) return 2;
            }
        }

        return 0;
    }

    /*========================================================*/

    public function verificarFormaPagamento($id_forma_pagamento, $id_informacao_empresa)
    {
        $consulta = $this->FormasPagamentosRepository->consultarFormaPagamentoEmpresa($id_forma_pagamento, $id_informacao_empresa);
        $this->FormasPagamentosRepository->getMySQL()->getDb()->commit();

        // Caso a forma de pagamento não seja da empresa
        if ($consulta == 0) 
            return 1;
        else
            return 0;
    }
}