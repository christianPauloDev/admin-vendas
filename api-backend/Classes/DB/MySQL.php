<?php

namespace DB;

use InvalidArgumentException;
use PDO;
use PDOException;
use Util\ConstantesGenericasUtil;

class MySQL
{
    private object $db;

    /**
     * MySQL constructor.
     */
    public function __construct()
    {
        $this->db = $this->setDB();
    }

    /**
     * @return PDO
     */
    public function setDB()
    {
        try {
            return new PDO(
                'mysql:host=' . HOST . '; dbname=' . BANCO . ';', USUARIO, SENHA
            );
        } catch (PDOException $exception) {
            throw new PDOException($exception->getMessage());
        }
    }

    /**
     * @param $tabela
     * @param $id
     * @return string
     */
    public function delete($tabela, $id)
    {
        $consultaDelete = 'DELETE FROM ' . $tabela . ' WHERE id = :id';

        if ($tabela && $id) {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare($consultaDelete);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $this->db->commit();
                return ConstantesGenericasUtil::MSG_DELETADO_SUCESSO;
            }

            $this->db->rollBack();

            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }
        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_GENERICO);
    }

    /**
     * @param $tabela
     * @return array
     */
    public function getAll($tabela, $fk = null, $fk_id = null)
    {
        if ($tabela) {
            $consulta = 'SELECT * FROM ' . $tabela;
            if ($fk_id) $consulta .= ' WHERE ' . $fk . ' = :fk';

            $stmt = $this->db->prepare($consulta);
            if ($fk_id) $stmt->bindValue(':fk', $fk_id);
            $stmt->execute();

            $registros = $stmt->fetchAll($this->db::FETCH_ASSOC);
            if (is_array($registros) && count($registros) > 0) {
                return $registros;
            }
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /**
     * @param $tabela
     * @param $id
     * @return mixed
     */
    public function getOneByKey($tabela, $id, $fk = null, $fk_id = null)
    {
        if ($tabela && $id) {
            $consulta = 'SELECT * FROM ' . $tabela . ' WHERE id = :id';
            if ($fk_id) $consulta .= ' AND ' . $fk . ' = :fk';

            $stmt = $this->db->prepare($consulta);
            $stmt->bindParam(':id', $id);
            if ($fk_id) $stmt->bindValue(':fk', $fk_id);
            
            $stmt->execute();

            $totalRegistros = $stmt->rowCount();
            
            if ($totalRegistros === 1) {
                return $stmt->fetch($this->db::FETCH_ASSOC);
            }
            
            throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_ID_OBRIGATORIO);
    }

    /*===================================================*//*===================================================*/

    public function verificarUnico($tabela, $campo, $valor, $id = null)
    {
        $consulta = 'SELECT * FROM ' . $tabela . ' WHERE ' . $campo .' LIKE BINARY :campo';

        if ($id) $consulta .= ' AND id != :id';

        $stmt = $this->db->prepare($consulta);
        $stmt->bindParam(':campo', $valor);

        if ($id) $stmt->bindValue(':id', $id);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function validarLogin($tabela, $login, $senha)
    {
        $consulta = "SELECT id, senha FROM " . $tabela . " 
                    WHERE login LIKE BINARY :login AND status = :status";

        $stmt = $this->db->prepare($consulta);
        $stmt->bindValue(':login', $login);
        if (!in_array($tabela, ['usuarios', 'admins'])) $stmt->bindValue(':status', 'ativo');
        $stmt->execute();

        $totalRegistros = $stmt->rowCount();

        if ($totalRegistros === 1) {
            $dados = $stmt->fetch($this->db::FETCH_ASSOC);
            
            if (password_verify($senha, $dados['senha'])) {
                return $dados['id'];
            }
        }
        
        return false;
    }

    /**
     * @return object|PDO
     */
    public function getDb()
    {
        return $this->db;
    }
}