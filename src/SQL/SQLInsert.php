<?php

namespace Mviniis\ConnectionDatabase\SQL;

use \Mviniis\ConnectionDatabase\SQL\Parts\{SQLFields, SQLInto, SQLValues};

/**
 * class SQLInsert
 * 
 * Classe responsável por criar as queries de inserção de registros no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLInsert extends SQLBuilder {
  public function getQuery(): string {
    $odemValidacao = ['into', 'fieldsValues', 'values', 'where', 'select'];
    $partesQuery   = $this->formatQuery($odemValidacao);

    $insertQuery = ['INSERT', $partesQuery['into']];
    if(isset($partesQuery['fieldsValues']) && !is_null($partesQuery['fieldsValues'])) {
      $insertQuery[] = "({$partesQuery['fieldsValues']})";
    }
    
    if(isset($partesQuery['values']) && !empty($partesQuery['values'])) {
      $insertQuery[] = "VALUES {$partesQuery['values']}";
    }
    
    if(isset($partesQuery['where']) && !is_null($partesQuery['where'])) {
      $insertQuery[] = "WHERE {$partesQuery['where']}";
    }

    // ADICIONA UM SELECT NO INSERT
    if(isset($partesQuery['select']) && !is_null($partesQuery['select'])) {
      $insertQuery[] = $partesQuery['select'];
    }

    $insertQuery             = implode(' ', array_filter($insertQuery));
    $adicionarVirgulaAoFinal = is_null($partesQuery['select']);
    return $adicionarVirgulaAoFinal ? $insertQuery . ';': $insertQuery;
  }

  public function addInto(SQLInto $obInto): self {
    $this->queryParts['into'] = $obInto;

    return $this;
  }

  public function addFields(array $fields = []): self {
    foreach($fields as $obField) {
      if(!$obField instanceof SQLFields) continue;

      if(!isset($this->queryParts['fieldsValues'])) $this->queryParts['fieldsValues'] = [];
      $this->queryParts['fieldsValues'][] = $obField;
    }

    return $this;
  }

  public function addValues(SQLValues $obValues): self {
    $this->queryParts['values'] = $obValues;

    return $this;
  }

  public function addWhere(mixed $obWhere): self {
    $this->queryParts['where'] = $obWhere;
    
    return $this;
  }

  /**
   * Método responsável por definir uma consulta no insert
   * @param  SQLSelect      $obSelect      Querie de consulta para fazer o select insert
   * @return self
   */
  public function addSelect(SQLSelect $obSelect): self {
    $this->queryParts['select'] = $obSelect;
    return $this;
  }
}