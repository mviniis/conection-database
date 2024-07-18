<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFrom, SQLJoin, SQLSet, SQLWhereGroup};

/**
 * class SQLUpdate
 * 
 * Classe responsável por criar as queries de atualização de registros no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLUpdate extends SQLBuilder {
  public function getQuery(): string {
    $odemValidacao = ['from', 'set', 'join', 'where', 'limit'];
    $partesQuery   = $this->formatQuery($odemValidacao);
    $updateQuery   = [
      'UPDATE',
      $partesQuery['from'],
      $partesQuery['join'],
      !is_null($partesQuery['set']) ? "SET {$partesQuery['set']}": null,
      !is_null($partesQuery['where'] && !empty($partesQuery['where'])) ? "WHERE {$partesQuery['where']}": null,
      $partesQuery['limit']
    ];

    return implode(' ', array_filter($updateQuery)) . ';';
  }

  public function addFrom(SQLFrom $obFrom): self {
    $this->queryParts['from'] = $obFrom;
    return $this;
  }

  public function addSet(array $setItens = []): self {
    foreach($setItens as $obSet) {
      if(!$obSet instanceof SQLSet) continue;

      if(!isset($this->queryParts['set'])) $this->queryParts['set'] = [];
      $this->queryParts['set'][] = $obSet;
    }

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

  public function addWhere(SQLWhereGroup $obWhereGroup): self {
    $this->queryParts['where'] = $obWhereGroup;
    
    return $this;
  }
}