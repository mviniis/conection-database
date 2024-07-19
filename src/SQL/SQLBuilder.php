<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLParts, SQLSet, SQLFields, SQLFrom, SQLInto, SQLJoin, SQLOrder, SQLValues, SQLWhereGroup};

/**
 * class SQLBuilder
 * 
 * Classe responsável por definir os métodos compartilhados dos tipos de queries
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
abstract class SQLBuilder {
  /**
   * Guarda as partes de uma query
   * @var array
   */
  protected $queryParts = [];

  /**
   * Guarda os parâmetros que devem ser preparados pelo PDO
   * @var array
   */
  private $preparedParams = [];

  private function addPartQuery(string $part, SQLParts $obSqlPart, mixed &$queryParts): void {
    $addParams = ['fields', 'fieldsValues', 'values', 'where', 'join', 'set'];

    // ADICIONA OS PARÂMETROS PREPARADOS
    if(in_array($part, $addParams)) $this->addPreparedParams($obSqlPart->getPreparedParams());
        
    // ADICIONA A CLÁUSULA
    $clausule = $obSqlPart->getClausule();
    if(strlen(trim($clausule))) $queryParts = $clausule;
  }

  /**
   * Método responsável por extrair os dados dos parâmetros preparados
   * @param  array      $params      Parâmetros preparados
   * @param  array      $result      Resultado da extração
   * @return void
   */
  private function extractPreparedValues(array $params, array &$result): void {
    foreach($params as $value) {
      if(is_array($value)) {
        $this->extractPreparedValues($value, $result);
        continue;
      }

      $result[] = $value;
    }
  }

  /**
   * Método responsável por definir os parâmetros preparados em ordem sequencial
   * @param  array      $preparedParams      Parâmetros que serão adicionados
   * @return self
   */
  protected function addPreparedParams(array $preparedParams): self {
    foreach($preparedParams as $preparedParam) $this->preparedParams[] = $preparedParam;
    return $this;
  }

  /**
   * Método responsável por formatar as partes de uma query
   * @param  array      $ordem      Define a ordem de validaçãos das partes da query
   * @return array
   */
  protected function formatQuery(array $ordem): array {
    $response = [];

    foreach($ordem as $parte) {
      $valorParte       = $this->queryParts[$parte] ?? null;
      $response[$parte] = null;
      $partsIsArray     = ['join', 'fields', 'fieldsValues'];
      $partsIsObjetct   = ['into', 'set', 'from', 'order', 'values', 'where'];
      $partsIsDefault   = ['limit'];
      
      switch(true) {
        case in_array($parte, $partsIsArray):
          $response[$parte] = ($parte == 'fields') ? '*': null;
          $virgulaSeparator = ['fields', 'fieldsValues'];
          $formatedValue    = null;

          if(is_array($valorParte)) {
            $addValues = [];
            foreach($valorParte as $obSqlPart) {
              if(!$obSqlPart instanceof SQLParts) continue;

              $this->addPartQuery($parte, $obSqlPart, $formatedValue);
              $addValues[] = $formatedValue;
            }
        
            $separador = in_array($parte, $virgulaSeparator) ? ', ': ' ';
            if(!empty($addValues)) $response[$parte] = implode($separador, $addValues);
          }
        break;

        case in_array($parte, $partsIsObjetct):
          if($valorParte instanceof SQLParts) {
            $response[$parte] = [];
            $this->addPartQuery($parte, $valorParte, $response[$parte]);
          }
        break;

        case in_array($parte, $partsIsDefault):
          $response[$parte] = $valorParte;
        break;

        case ($parte == 'select'):
          if((is_null($response['values']) && is_null($response['fieldsValues'])) && $valorParte instanceof SQLSelect) {
            $response[$parte] = $valorParte->getQuery();
            $this->addPreparedParams($valorParte->getPreparedParams());
          }
        break;
      }
    }

    return $response;
  }

  /**
   * Método responsável por gerar a query da operação
   * @return string
   */
  abstract public function getQuery(): string;

  /**
   * Método responsável por retornar os parâmetros preparados da querie
   * @return array
   */
  public function getPreparedParams(): array {
    $preparedParams = [];
    $this->extractPreparedValues($this->preparedParams, $preparedParams);

    return $preparedParams;
  }

  /**
   * Método reponsável por definir os campos de uma operação
   * @param  SQLFields[]      $fields      Campos da operação
   * @return self
   */
  public function addFields(array $fields = []): self {
    return $this;
  }

  /**
   * Método responsável por definir a tabela que será manipulada na operação
   * @param  SQLFrom      $obFrom      Objeto contendo a definição da tabela da operação
   * @return self
   */
  public function addFrom(SQLFrom $obFrom): self {
    return $this;
  }

  /**
   * Método responsável por definir as condições de uma operação de forma agrupada
   * @param  SQLWhereGroup|SQWhere      $obWhere      Agrupamento de condições que serão aplicadas na operação
   * @return self
   */
  public function addWhere(mixed $obWhere): self {
    return $this;
  }

  /**
   * Método responsável por definir a junção com outra tabela
   * @param  SQLJoin[]      $joins      Configuração das junções com outras trabelas
   * @return self
   */
  public function addJoin(array $joins): self {
    return $this;
  }

  /**
   * Método responsável por definir a ordenação dos dados
   * @param  SQLOrder       $obOrder       Objeto com a definição de ordenação dos dados da operação
   * @return self
   */
  public function addOrder(SQLOrder $obOrder): self {
    return $this;
  }

  /**
   * Método responsável por definir a clausula INTO de uma operação SQL
   * @param  SQLInto      $obInto      Dados da cláusula de inclusão
   * @return self
   */
  public function addInto(SQLInto $obInto): self {
    return $this;
  }

  /**
   * Método responsável por definir os campos de uma operação de atualização ou inserção de dados
   * @param SQLValues       $obValues       Array com os valores que serão inseridos
   * @return self
   */
  public function addValues(SQLValues $obValues): self {
    return $this;
  }

  /**
   * Método responsável por definir os campos que serão atualizados em uma operação
   * @param  SQLSet       $setItens       Campos que serão atualizados
   * @return self
   */
  public function addSet(SQLSet $setItens): self {
    return $this;
  }

  /**
   * Método responsável por definir o limite de dados que serão manipulados
   * @param  int      $limit       Limite de dados
   * @param  int      $offset      Página da consulta
   * @return self
   */
  public function addLimit(? int $limit, ? int $offset = null): self {
    $validLimit  = is_numeric($limit) && $limit > 0;
    $validOffset = is_numeric($offset) && $offset >= 0;
    
    // ADICIONA O LIMIT E O OFFSET
    if($validLimit) $this->queryParts['limit'] = "LIMIT {$limit}";
    if($validLimit && $validOffset) $this->queryParts['limit'] = "LIMIT {$offset},{$limit}";

    return $this;
  }
}