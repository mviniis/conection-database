<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFields, SQLFrom, SQLJoin, SQLOrder, SQLWhereGroup};

/**
 * class SQLSelect
 * 
 * Classe responsável por criar as queries de consultas ao banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLSelect extends SQLBuilder {
  public function getQuery(): string {
    $ordemValidacao = ['fields', 'from', 'join', 'where', 'order', 'limit'];
    $partesQuery    = $this->formatQuery($ordemValidacao);
    $selectQuery    = [
      'SELECT',
      $partesQuery['fields'],
      !is_null($partesQuery['from'] ?? null) && !empty($partesQuery['from']) ? 'FROM ' . $partesQuery['from']: '',
      $partesQuery['join'],
      !is_null($partesQuery['where'] ?? null) && !empty($partesQuery['where']) ? 'WHERE ' . $partesQuery['where']: '',
      $partesQuery['order'],
      $partesQuery['limit']
    ];
    return implode(' ', array_filter($selectQuery)) . ';';
  }

  public function addFields(array $fields = []): self {
    if(!isset($this->queryParts['fields'])) $this->queryParts['fields'] = [];

    foreach($fields as $obField) {
      if(!$obField instanceof SQLFields) continue;

      $this->queryParts['fields'][] = $obField;
    }

    return $this;
  }

  public function addFrom(SQLFrom $obFrom): self {
    $this->queryParts['from'] = $obFrom;

    return $this;
  }

  public function addWhere(SQLWhereGroup $obWhereGroup): self {
    $this->queryParts['where'] = $obWhereGroup;
    
    return $this;
  }

  public function addJoin(array $joins): self {
    $this->queryParts['join'] = [];

    foreach($joins as $obJoin) {
      if(!$obJoin instanceof SQLJoin) continue;

      $this->queryParts['join'][] = $obJoin;
    }

    return $this;
  }

  public function addOrder(? SQLOrder $obOrder): self {
    if(!is_null($obOrder)) $this->queryParts['order'] = $obOrder;

    return $this;
  }
}