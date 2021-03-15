<?php

namespace Repository;

use DB\MySQL;

class CategoriasRepository 
{

    private object $MySQL;
    public const TABELA = "categorias";

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
                            VALUES (null, :id_informacoes_empresa, :titulo)";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);

        $stmt->bindValue(":id_informacoes_empresa", $id_usuario);
        $stmt->bindValue(":titulo", $dados['titulo']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " SET titulo = :titulo WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(":titulo", $dados['titulo']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

}