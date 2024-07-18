<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLValuesGroup
 * 
 * Classe responsável por definir um agrupamento dos valores de uma operação de atualização ou inserção
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLValuesGroup extends SQLParts {
  /**
   * Construtor da classe
   * @param SQLValuesItem[]       $itens       Array com os itens do agrupamento
   */
  public function __construct(
    private array $itens = []
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $campos = [];
    foreach($this->itens as $obValueItem) {
      if(!$obValueItem instanceof SQLValuesItem) continue;

      $campos[] = $obValueItem->getClausule();
    }

    return implode(', ', $campos);
  }

  protected function analisingPreparedParams(): self {
    foreach($this->itens as $obValueItem) if(isset($obValueItem->getPreparedParams()[0])) $this->addPrepareParams($obValueItem->getPreparedParams()[0]);
    return $this;
  }

  /**
   * Método responsável por retornar a quantidade de itens de um agrupamento
   * @return int
   */
  public function totalItens(): int {
    $quantidade = 0;
    foreach($this->itens as $obValueItem) if($obValueItem instanceof SQLValuesItem) $quantidade += 1;

    return $quantidade;
  }
}