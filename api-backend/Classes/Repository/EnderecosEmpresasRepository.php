<?php

namespace Repository;

use DB\MySQL;

class EnderecosEmpresasRepository 
{

    private object $MySQL;
    public const TABELA = "enderecos_empresas";

    public function __construct()
    {
        $this->MySQL = new MySQL();
    }

    /*===================================================*//*===================================================*/

    public function getMySQL()
    {
        return $this->MySQL;
    }

    /*===================================================*//*===================================================*/

    public function cadastrar($dados, $id_usuario)
    {
        $consultaInsert = "INSERT INTO " . self::TABELA . " 
                        VALUES (null, :id_informacoes_empresa, :id_bairro, :rua, :numero, :complemento, :ponto_referencia, :cep)";
    
        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);
        $stmt->bindValue(":id_informacoes_empresa", $id_usuario);
        $stmt->bindValue(":id_bairro", $dados['id_bairro']);
        $stmt->bindValue(":rua", $dados['rua']);
        $stmt->bindValue(":numero", $dados['numero']);
        $stmt->bindValue(":complemento", $dados['complemento']);
        $stmt->bindValue(":ponto_referencia", $dados['ponto_referencia']);
        $stmt->bindValue(":cep", $dados['cep']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " SET id_bairro_fk = :id_bairro, rua = :rua, numero = :numero, 
                            complemento = :complemento, ponto_referencia = :ponto_referencia, cep = :cep 
                            WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(":id_bairro", $dados['id_bairro']);
        $stmt->bindValue(":rua", $dados['rua']);
        $stmt->bindValue(":numero", $dados['numero']);
        $stmt->bindValue(":complemento", $dados['complemento']);
        $stmt->bindValue(":ponto_referencia", $dados['ponto_referencia']);
        $stmt->bindValue(":cep", $dados['cep']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

}