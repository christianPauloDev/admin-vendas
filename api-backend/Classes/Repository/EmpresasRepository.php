<?php

namespace Repository;

use DB\MySQL;
use InvalidArgumentException;
use Util\ConstantesGenericasUtil;

class EmpresasRepository 
{

    private object $MySQL;
    public const TABELA = "empresas";

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

    public function getAll($id_proprietario)
    {
        $consulta = "SELECT empresas.id, cnpj, descricao, email, fone, img, nome_fantasia, razao_social, empresas.status, 
                        titulo especialidade, nome_estado, cidades.nome cidade, bairros.nome bairro, rua, numero,
                        complemento, ponto_referencia, cep
                    FROM " . self::TABELA . "
                    INNER JOIN informacoes_empresas ON empresas.id = id_empresa_fk
                    INNER JOIN especialidades ON especialidades.id = id_especialidade_fk
                    INNER JOIN enderecos_empresas ON informacoes_empresas.id = id_informacoes_empresa_fk
                    INNER JOIN bairros ON bairros.id = id_bairro_fk
                    INNER JOIN cidades ON cidades.id = id_cidade_fk
                    INNER JOIN estados ON estados.id = id_estado_fk
                    WHERE id_proprietario_fk = :id_proprietario";
        
        $consulta = $this->MySQL->getDb()->prepare($consulta);
        
        $consulta->bindValue(':id_proprietario', $id_proprietario);
        $consulta->execute();

        if ($consulta->rowCount() > 0)
            return $consulta->fetchAll($this->MySQL->getDb()::FETCH_ASSOC);

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function getOneByKey($id, $fk = null, $fk_id = null)
    {
        $consulta = "SELECT empresas.id, razao_social, empresas.login, img, cnpj, descricao, fone, email, nome_fantasia, empresas.status, 
                        id_especialidade_fk especialidade, estados.id id_estado, cidades.id id_cidade, bairros.id id_bairro, 
                        rua, numero, complemento, ponto_referencia, cep 
                    FROM " . self::TABELA . "
                    INNER JOIN informacoes_empresas ON empresas.id = id_empresa_fk
                    INNER JOIN especialidades ON especialidades.id = id_especialidade_fk
                    INNER JOIN enderecos_empresas ON informacoes_empresas.id = id_informacoes_empresa_fk
                    INNER JOIN bairros ON bairros.id = id_bairro_fk
                    INNER JOIN cidades ON cidades.id = id_cidade_fk
                    INNER JOIN estados ON estados.id = id_estado_fk
                    WHERE empresas.id = :id";
        if ($fk_id) $consulta .= ' AND ' . $fk . ' = :fk';

        $consulta = $this->MySQL->getDb()->prepare($consulta);
        
        $consulta->bindValue(':id', $id);
        if ($fk_id) $consulta->bindValue(':fk', $fk_id);
        $consulta->execute();

        if ($consulta->rowCount() > 0)
            return $consulta->fetch($this->MySQL->getDb()::FETCH_ASSOC);

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function ids($id)
    {
        $consulta = "SELECT empresas.id, informacoes_empresas.id id_2
                    FROM " . self::TABELA . "
                    INNER JOIN informacoes_empresas ON empresas.id = id_empresa_fk
                    WHERE empresas.id = :id";

        $consulta = $this->MySQL->getDb()->prepare($consulta);
        
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        if ($consulta->rowCount() > 0)
            return $consulta->fetch($this->MySQL->getDb()::FETCH_ASSOC);

        throw new InvalidArgumentException(ConstantesGenericasUtil::MSG_ERRO_SEM_RETORNO);
    }

    /*===================================================*//*===================================================*/

    public function cadastrar($dados, $id_proprietario)
    {
        $consultaInsert = "INSERT INTO " . self::TABELA . " VALUES (null, :id_proprietario, :razao_social, :login, :senha, :status)";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaInsert);

        $stmt->bindValue(":id_proprietario", $id_proprietario);
        $stmt->bindValue(":razao_social", $dados['razao_social']);
        $stmt->bindValue(":login", $dados['login']);
        $stmt->bindValue(":senha", password_hash($dados['senha'], PASSWORD_BCRYPT, [15]));
        $stmt->bindValue(":status", 'ativo');
        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

    public function atualizar($id, $dados)
    {
        $consultaUpdate = " UPDATE " . self::TABELA . " 
                            INNER JOIN informacoes_empresas ON empresas.id = id_empresa_fk
                            INNER JOIN enderecos_empresas ON informacoes_empresas.id = id_informacoes_empresa_fk
                            SET 
                                razao_social = :razao_social, empresas.login = :login, empresas.status = :status,
                                
                                id_especialidade_fk = :id_especialidade, nome_fantasia = :nome_fantasia, cnpj = :cnpj, fone = :fone, 
                                descricao = :descricao, img = :img, email = :email,
                                
                                rua = :rua, numero = :numero, id_bairro_fk = :id_bairro, 
                                complemento = :complemento, ponto_referencia = :ponto_referencia, cep = :cep 
                            
                            WHERE empresas.id = :id";

        $this->MySQL->getDb()->beginTransaction();
        
        $stmt = $this->MySQL->getDb()->prepare($consultaUpdate);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindValue(':razao_social', $dados['razao_social']);
        $stmt->bindValue(':login', $dados['login']);
        $stmt->bindValue(':status', $dados['status']);

        $stmt->bindValue(':id_especialidade', $dados['id_especialidade']);
        $stmt->bindValue(':nome_fantasia', $dados['nome_fantasia']);
        $stmt->bindValue(':cnpj', $dados['cnpj']);
        $stmt->bindValue(':fone', $dados['fone']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':img', $dados['img']);
        $stmt->bindValue(':email', $dados['email']);

        $stmt->bindValue(':rua', $dados['rua']);
        $stmt->bindValue(':numero', $dados['numero']);
        $stmt->bindValue(':id_bairro', $dados['id_bairro']);
        $stmt->bindValue(':complemento', $dados['complemento']);
        $stmt->bindValue(':ponto_referencia', $dados['ponto_referencia']);
        $stmt->bindValue(':cep', $dados['cep']);

        $stmt->execute();

        return $stmt->rowCount();
    }

    /*===================================================*//*===================================================*/

}
