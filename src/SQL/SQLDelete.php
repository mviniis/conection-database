<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFrom, SQLJoin, SQLOrder, SQLSet, SQLWhereGroup};

/**
 * class SQLDelete
 * 
 * Classe responsável por criar a query de remoção de registros no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLDelete extends SQLBuilder {
  public function getQuery(): string {
    $odemValidacao = ['from', 'where'];
    $partesQuery   = $this->formatQuery($odemValidacao);

    // EVITA DE CRIAR QUERIES DE DELETE SEM O WHERE
    if(is_null($partesQuery['where'])) return '';

    $deleteQuery = [
      'DELETE',
      "FROM {$partesQuery['from']}",
      "WHERE {$partesQuery['where']}"
    ];

    return implode(' ', array_filter($deleteQuery)) . ';';
  }
  
  public function addFrom(SQLFrom $obFrom): self {
    $this->queryParts['from'] = $obFrom;
    return $this;
  }

  public function addWhere(SQLWhereGroup $obWhereGroup): self {
    $this->queryParts['where'] = $obWhereGroup;
    
    return $this;
  }
}