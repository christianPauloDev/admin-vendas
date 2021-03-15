<?php

namespace Repository;

use DB\MySQL;

class InformacoesEmpresasRepository 
{

    private object $MySQL;
    public const TABELA = "informacoes_empresas";

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

    public function cadastrar($dados, $id_empresa)
    {
        $consultaInsert = "INSERT INTO " . self::TABELA . " 
                            VALUES (null, :id_empresa, :id_especialidade, :nome_fantasia, :fone, :email, :cnpj, :descricao, :img, :status)";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);

        $stmt->bindValue(":id_empresa", $id_empresa);
        $stmt->bindValue(":id_especialidade", $dados['id_especialidade']);
        $stmt->bindValue(":nome_fantasia", $dados['nome_fantasia']);
        $stmt->bindValue(":fone", $dados['fone']);
        $stmt->bindValue(":email", $dados['email']);
        $stmt->bindValue(":cnpj", $dados['cnpj']);
        $stmt->bindValue(":descricao", $dados['descricao']);
        $stmt->bindValue(":img", $dados['img']);
        $stmt->bindValue(":status", 'fechado');
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function consultarStatus($id)
    {
        $consultaSelect = "SELECT status FROM " . self::TABELA . " WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaSelect);
        
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0)
            return $stmt->fetch($this->getMySQL()->getDb()::FETCH_ASSOC);
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " 
                            SET nome_fantasia = :nome_fantasia, fone = :fone, cnpj = :cnpj, email = :email";

        if ($dados['descricao']) $consultaUpdate .= ", descricao = :descricao";
        if ($dados['img']) $consultaUpdate .= ", img = :img";

        $consultaUpdate .= " WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(":nome_fantasia", $dados['nome_fantasia']);
        $stmt->bindValue(":fone", $dados['fone']);
        $stmt->bindValue(":cnpj", $dados['cnpj']);
        ($dados['email']) && $stmt->bindValue(":email", $dados['email']);
        ($dados['descricao']) && $stmt->bindValue(":descricao", $dados['descricao']);
        ($dados['img']) && $stmt->bindValue(":img", $dados['img']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizarStatus($id, $status)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " SET status = :status WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(":status", $status);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/


}
