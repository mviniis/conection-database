<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFrom, SQLJoin, SQLSet};

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

    $updateQuery = ['UPDATE'];
    if(isset($partesQuery['from']) && !is_null($partesQuery['from'])) {
      $updateQuery[] = $partesQuery['from'];
    }

    if(isset($partesQuery['join']) && !is_null($partesQuery['join'])) {
      $updateQuery[] = $partesQuery['join'];
    }

    if(isset($partesQuery['set']) && !is_null($partesQuery['set'])) {
      $updateQuery[] = "SET {$partesQuery['set']}";
    }

    if(isset($partesQuery['where']) && !is_null($partesQuery['where'])) {
      $updateQuery[] = "WHERE {$partesQuery['where']}";
    }

    if(isset($partesQuery['limit']) && !is_null($partesQuery['limit'])) {
      $updateQuery[] = $partesQuery['limit'];
    }

    return implode(' ', array_filter($updateQuery)) . ';';
  }

  public function addFrom(SQLFrom $obFrom): self {
    $this->queryParts['from'] = $obFrom;
    return $this;
  }

  public function addSet(SQLSet $setItens): self {
    if($setItens instanceof SQLSet) {
      $this->queryParts['set'] = $setItens;
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

  public function addWhere(mixed $obWhere): self {
    $this->queryParts['where'] = $obWhere;
    
    return $this;
  }
}