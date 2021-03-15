<?php

namespace Repository;

use DB\MySQL;
use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class CuponsRepository 
{

    private object $MySQL;  
    public const TABELA = "cupons";

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
                            VALUES (null, :id_informacoes_empresa, :titulo, :tipo_desconto, :valor_desconto, :valor_minimo, :data_validade, :status)";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);

        $stmt->bindValue(":id_informacoes_empresa", $id_usuario);
        $stmt->bindValue(":titulo", $dados['titulo']);
        $stmt->bindValue(":tipo_desconto", $dados['tipo_desconto']);
        $stmt->bindValue(":valor_desconto", $dados['valor_desconto']);
        $stmt->bindValue(":valor_minimo", $dados['valor_minimo']);
        $stmt->bindValue(":data_validade", $dados['data_validade']);
        $stmt->bindValue(":status", 1);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " 
                            SET titulo = :titulo, tipo_desconto = :tipo_desconto, valor_desconto = :valor_desconto, 
                            valor_minimo = :valor_minimo, data_validade = :data_validade, status = :status WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":titulo", $dados['titulo']);
        $stmt->bindValue(":tipo_desconto", $dados['tipo_desconto']);
        $stmt->bindValue(":valor_desconto", $dados['valor_desconto']);
        $stmt->bindValue(":valor_minimo", $dados['valor_minimo']);
        $stmt->bindValue(":data_validade", $dados['data_validade']);
        $stmt->bindValue(":status", $dados['status']);
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

    public function dadosCupomUsuario($id_informacao_empresa, $titulo)
    {
        
        if ($titulo) {
            $consulta = "SELECT id, valor_desconto, valor_minimo, tipo_desconto 
                        FROM " . self::TABELA . " 
                        WHERE titulo = :titulo AND status = :status AND data_validade >= :data 
                            AND id_informacoes_empresa_fk = :id_informacoes_empresa ";

            $stmt = $this->MySQL->getDb()->prepare($consulta);
            $stmt->bindParam(':titulo', str_replace("&-&", " ", $titulo));
            $stmt->bindValue(':status', 1);
            $stmt->bindValue(':id_informacoes_empresa', $id_informacao_empresa);
            $stmt->bindValue(':data', Date('Y-m-d'));
            
            $stmt->execute();

            $totalRegistros = $stmt->rowCount();
            
            if ($totalRegistros === 1) {
                return $stmt->fetch($this->MySQL->getDb()::FETCH_ASSOC);
            }
            
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_TITULO_OBRIGATORIO);
    }
}