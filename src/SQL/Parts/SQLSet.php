<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLSet
 * 
 * Classe responsável por definir os campos de uma operação de atualização de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLSet extends SQLParts {
  /**
   * Construtor da clase
   * @param SQLSetItem[]      $setItens      Campos que serão atualizados
   */
  public function __construct(private array $setItens) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $setItens = [];
    foreach($this->setItens as $obSetItns) {
      if(!$obSetItns instanceof SQLSetItem) continue;
      
      $setItens[] = $obSetItns->getClausule();
    }

    return implode(', ', $setItens);
  }

  protected function analisingPreparedParams(): self {
    foreach($this->setItens as $obSetItns) {
      if(!$obSetItns instanceof SQLSetItem) continue;
      
      foreach($obSetItns->getPreparedParams() as $preparedParam) $this->addPrepareParams($preparedParam);
    }

    return $this;
  }
}