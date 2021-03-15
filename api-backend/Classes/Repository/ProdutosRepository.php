<?php

namespace Repository;

use DB\MySQL;
use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class ProdutosRepository 
{

    private object $MySQL;
    public const TABELA = "produtos";

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

    public function cadastrar($dados)
    {
        $consultaInsert = "INSERT INTO " . self::TABELA . " 
                        VALUES (null, :id_categoria, :nome_produto, :preco, :img, :descricao, :status)";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);

        $stmt->bindValue(":id_categoria", $dados['id_categoria']);
        $stmt->bindValue(":nome_produto", $dados['nome_produto']);
        $stmt->bindValue(":preco", $dados['preco']);
        $stmt->bindValue(":img", $dados['img']);
        $stmt->bindValue(":descricao", $dados['descricao']);
        $stmt->bindValue(":status", $dados['status']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = "UPDATE " . self::TABELA . " 
                            SET nome_produto = :nome_produto, preco = :preco, img = :img, descricao = :descricao, 
                                status = :status, id_categoria_fk = :id_categoria 
                            WHERE id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(":nome_produto", $dados['nome_produto']);
        $stmt->bindValue(":preco", $dados['preco']);
        $stmt->bindValue(":img", $dados['img']);
        $stmt->bindValue(":descricao", $dados['descricao']);
        $stmt->bindValue(":status", $dados['status']);
        $stmt->bindValue(":id_categoria", $dados['id_categoria']);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function listar($fk, $id_usuario, $status, $id_categoria = '')
    {

        $array_status = [
            'd' => 'disponível',
            'e' => 'esgotado',
            'i' => 'indisponível',
        ];

        $status = $array_status[ $status[0] ];

        $consulta = "SELECT produtos.id, nome_produto, preco, img, descricao, status, titulo, categorias.id id_categoria, categorias.titulo categoria
                            FROM produtos 
                            INNER JOIN categorias ON categorias.id = id_categoria_fk
                            WHERE $fk = :id_informacoes_empresa ";

        ($id_categoria > 0) && $consulta .= "AND id_categoria_fk = :id_categoria ";
        ($status != '') && $consulta .= "AND produtos.status = :status ";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        
        $stmt->bindValue(":id_informacoes_empresa", $id_usuario);
        ($id_categoria > 0) && $stmt->bindValue(":id_categoria", $id_categoria);
        ($status != '') && $stmt->bindValue(":status", $status);
        $stmt->execute();

        $registros = $stmt->fetchAll($this->getMySQL()->getDb()::FETCH_ASSOC);

        if (is_array($registros) && count($registros) > 0) {
            return $registros;
        }    

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function consultar($id)
    {
        $consulta = "SELECT produtos.id id_produto, produtos.nome_produto, produtos.preco, produtos.img, produtos.descricao, 
                    produtos.status, id_categoria_fk, categorias.titulo categoria
                    FROM produtos 
                    INNER JOIN categorias ON categorias.id = id_categoria_fk
                    WHERE produtos.id = :id";

        $stmt = $this->MySQL->getDb()->prepare($consulta);
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();

        $registros = $stmt->fetch($this->getMySQL()->getDb()::FETCH_ASSOC);

        if (is_array($registros) && count($registros) > 0) {
            return $registros;
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function produtosUsuarios($id_informacoes_empresa)
    {
        $consulta = "SELECT produtos.id id_produto, nome_produto, preco, img, descricao, status, 
                        categorias.id id_categoria, titulo categoria
                        FROM produtos 
                        INNER JOIN categorias ON categorias.id = id_categoria_fk
                        WHERE id_informacoes_empresa_fk = :id_informacoes_empresa 
                        GROUP BY categorias.id, produtos.id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consulta);
        
        $stmt->bindValue(":id_informacoes_empresa", $id_informacoes_empresa);
        $stmt->execute();

        $registros = $stmt->fetchAll($this->getMySQL()->getDb()::FETCH_ASSOC);

        if (is_array($registros) && count($registros) > 0) {
            $i = 0;     
            $j = 0;

            foreach ($registros as $dados) {
                if (isset($retorno) && $retorno[$i]['id_categoria'] != $dados['id_categoria']) {
                    $i++;
                    $j=0;
                }

                if (isset($retorno[$i]['produtos'][$j]) && $retorno[$i]['produtos'][$j]['id_produto'] != $dados['id_produto']) {
                    $j++;
                }

                $retorno[$i]['id_categoria'] = $dados['id_categoria'];
                $retorno[$i]['nome_categoria'] = $dados['categoria'];
                
                if($dados['id_produto']) {
                    $retorno[$i]['produtos'][$j] = array(
                        "id" => $dados['id_produto'],
                        "nome_produto" => $dados['nome_produto'],
                        "preco" => $dados['preco'],
                        "img" => $dados['img'],
                        "descricao" => $dados['descricao'],
                        "status" => $dados['status'],
                        "numero_grupos" => $dados['numero_grupos']
                    );
                }
            }

            return $retorno;
        }

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function consultarEmpresaProduto($id_produto, $id_empresa)
    {
        $consulta = "SELECT id_informacoes_empresa_fk FROM categorias
                        INNER JOIN produtos ON id_categoria_fk = categorias.id
                        WHERE produtos.id = :id_produto AND id_informacoes_empresa_fk = :id_empresa ";

        $this->MySQL->getDb()->beginTransaction();
                
        $stmt = $this->MySQL->getDb()->prepare($consulta);

        $stmt->bindParam(":id_produto", $id_produto);
        $stmt->bindValue(":id_empresa", $id_empresa);
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/
}