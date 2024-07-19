<?php

namespace Mviniis\ConnectionDatabase\DB;

use \stdClass;
use \PDOException;
use \Mviniis\ConnectionDatabase\SQL\{SQLSelect, SQLBuilder, SQLUpdate};
use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFields, SQLFrom, SQLJoin, SQLOrder, SQLWhereGroup, SQLInto, SQLSet, SQLValues};

/**
 * class DBExecute
 * 
 * Classe responsável por realizar a manipulação dos dados de uma tabela no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
abstract class DBExecute {
  /**
   * Define o nome da tabela que será manipulada
   * @var string
   */
  protected ?string $table;

  /**
   * Define o modelo dos dados que serão retornados
   * @var string
   */
  protected ?string $modelData;

  /**
   * Guarda a SQL utilizada para a operação no banco de dados
   * @var SQLBuilder
   */
  private ?SQLBuilder $sql;

  /**
   * Método responsável por retornar um objeto de erro
   * @param  string       $message       Mensagem de erro da operação
   * @return DBEntity
   */
  protected function getEntityError(?string $message = ''): DBEntity {
    return new DBEntity($message);
  }

  /**
   * Método responsável por realizar a busca de um ou vários registros no banco de dados
   * @param  SQLWhereGroup|SQLWhere       $conditions       Condições da consulta
   * @param  SQLJoin[]                    $joins            Junções com outras tabelas
   * @param  SQLFields[]                  $fields           Campos retornados
   * @param  SQLOrder                     $order            Ordenação dos dados retornados
   * @param  int                          $limit            Limite de dados retornados
   * @param  int                          $offset           Paginação dos dados
   * @return self
   */
  public function select(
    mixed $conditions = null,
    ? array $joins = [],
    ? array $fields = [],
    ? SQLOrder $order = null,
    ? int $limit = null,
    ? int $offset = null
  ): self {
    $obSql = new SQLSelect;

    // MONTA AS PARTES DA CONSULTA
    $obSql->addWhere($conditions)
          ->addFrom(new SQLFrom($this->table))
          ->addJoin($joins)
          ->addFields($fields)
          ->addOrder($order)
          ->addLimit($limit, $offset);

    // ADICIONA O OBJETO DA QUERY MONTADA
    $this->sql = $obSql;

    return $this;
  }

  /**
   * Método responsável por realizar a remoção de registros no banco de dados
   * @param  SQLWhereGroup       $conditions       Condições da remoção
   * @return self
   */
  public function delete(SQLWhereGroup $conditions): self {
    return $this;
  }

  /**
   * Método responsável por realizar a busca de um ou vários registros no banco de dados
   * @param  SQLFields                $fields           Campos que serão populados
   * @param  SQLJoin                  $values           Valores que serão inseridos
   * @param  SQLWhereGroup|null       $conditions       Condições da inserção
   * @return self
   */
  public function insert(
    SQLFields $fields,
    SQLValues $values,
    ? SQLWhereGroup $conditions = null
  ): self {
    return $this;
  }

  /**
   * Método responsável por inserir um ou vários reguistros no banco de dados, com base em uma consulta
   * @param  SQLWhereGroup|null       $conditions       Condições da consulta
   * @param  SQLJoin|null             $joins            Junções com outras tabelas
   * @param  SQLFields|null           $fields           Campos retornados
   * @param  SQLOrder|null            $order            Ordenação dos dados retornados
   * @param  int|null                 $limit            Limite de dados retornados
   * @param  int|null                 $offset           Paginação dos dados
   * @return self
   */
  public function insertSelect(
    ? SQLWhereGroup $conditions = null,
    ? SQLJoin $joins = null,
    ? SQLFields $fields = null,
    ? SQLOrder $order = null,
    ? int $limit = null,
    ? int $offset = null
  ): self {
    return $this;
  }

  /**
   * Método responsável por atualizar um ou vários registros no banco de dados
   * @param SQLSet[]                $set        Campos que serão atualizados
   * @param SQLWhereGroup|null      $where      Condições da atualização
   * @param SQLJoin[]|null          $join       junções com outras tabelas
   * @param int|null                $limit      Limite de dados
   * @return self
   */
  public function update(
    array $set,
    ? SQLWhereGroup $conditions = null,
    ? array $joins = [],
    ? int $limit = null
  ): self {
    $obSql = new SQLUpdate;

    // MONTA AS PARTES DA CONSULTA
    $obSql->addWhere($conditions)
          ->addFrom(new SQLFrom($this->table))
          ->addJoin($joins)
          ->addSet($set)
          ->addLimit($limit);

    // ADICIONA O OBJETO DA QUERY MONTADA
    $this->sql = $obSql;

    return $this;
  }

  /**
   * Método responsável por recuperar os dados de um único objeto de uma operação
   * @return DBEntity
   */
  public function fetchObject(): DBEntity {
    $pdo = DBConnection::getPdo();
    try {
      // PREPARA A OPERAÇÃO
      $pdo->beginTransaction();
      $pdoStatement = $pdo->prepare($this->sql->getQuery());

      // EXECUTA A OPERAÇÃO
      $pdoStatement->execute($this->sql->getPreparedParams());
      $pdo->commit();

      // VERIFICA O SUCESSO
      $object        = $pdoStatement->fetchObject();
      $success       = $object instanceof stdClass;
      $obEntity      = $this->getEntityError();
      $obDTOResponse = new $this->modelData();
      $obEntity->setData($obDTOResponse);

      // RETORNA EM CASO DE ERRO
      if(!$success) return $obEntity;

      // DEFINE OS DADOS VINDOS DO BANCO
      $obEntity->setSuccess($success);
      $obEntity->setData($obDTOResponse->definirDados((array) $object));
      return $obEntity;
    } catch (PDOException $pdoEx) {
      $pdo->commit();
      return $this->getEntityError($pdoEx->getMessage());
    }
  }

  /**
   * Método responsável por recuperar os dados de vários objetos de uma operação
   * @return DBEntity
   */
  public function fetchAllObjects(): DBEntity {
    $pdo = DBConnection::getPdo();
    try {
      // PREPARA A OPERAÇÃO
      $pdo->beginTransaction();
      $pdoStatement = $pdo->prepare($this->sql->getQuery());

      // EXECUTA A OPERAÇÃO
      $pdoStatement->execute($this->sql->getPreparedParams());
      $pdo->commit();

      // VERIFICA O SUCESSO
      $objects  = $pdoStatement->fetchAll();
      $success  = is_array($objects);
      $obEntity = $this->getEntityError();

      // RETORNA EM CASO DE ERRO
      if(!$success) return $obEntity;

      // FORMATA PARA O DTO
      foreach($objects as &$data) $data = new $this->modelData((array) $data);

      // DEFINE OS DADOS VINDOS DO BANCO
      $obEntity->setSuccess($success);
      $obEntity->setAllData($objects);
      return $obEntity;
    } catch (PDOException $pdoEx) {
      $pdo->commit();
      return $this->getEntityError($pdoEx->getMessage());
    }
  }

  /**
   * Método responsável por retornar os dados de uma única coluna em uma operação
   * @return mixed
   */
  public function fetchColumn(): mixed {
    $pdo = DBConnection::getPdo();
    try {
      // PREPARA A OPERAÇÃO
      $pdo->beginTransaction();
      $pdoStatement = $pdo->prepare($this->sql->getQuery());

      // EXECUTA A OPERAÇÃO
      $pdoStatement->execute($this->sql->getPreparedParams());
      $pdo->commit();

      // VERIFICA O SUCESSO
      $data = $pdoStatement->fetchColumn();
      return ($data === false) ? null: $data;
    } catch (PDOException $pdoEx) {
      $pdo->commit();
      return null;
    }
  }

  /**
   * Método responsável por retornar quantas linhas foram afetadas por uma operação
   * @return int
   */
  public function rowCount(): int {
    $pdo    = DBConnection::getPdo();
    $counts = 0;
    try {
      // PREPARA A OPERAÇÃO
      $pdo->beginTransaction();
      $pdoStatement = $pdo->prepare($this->sql->getQuery());

      // EXECUTA A OPERAÇÃO
      $pdoStatement->execute($this->sql->getPreparedParams());
      $counts = $pdoStatement->rowCount();

      // VERIFICA O SUCESSO
      if($counts <= 0) throw new PDOException('Nenhum registro foi modificado', 400);

      // SALVA OS DADOS NO BANCO
      $pdo->commit();
      return $counts;
    } catch (PDOException $pdoEx) {
      $pdo->rollBack();
      return $counts;
    }
  }
}