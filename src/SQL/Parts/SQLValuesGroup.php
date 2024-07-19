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
   * @param SQLValues[]       $itens       Array com os valores inseridos agrupados
   */
  public function __construct(
    private array $itens = []
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $campos = [];
    foreach($this->itens as $value) $campos[] = '?';

    return !empty($campos) ? '(' . implode(', ', $campos) . ')': '';
  }

  protected function analisingPreparedParams(): self {
    foreach($this->itens as $value) {
      if(is_array($value)) continue;

      $this->addPrepareParams($value);
    }

    return $this;
  }

  /**
   * Método responsável por retornar a quantidade de itens de um agrupamento
   * @return int
   */
  public function getTotalItens(): int {
    return count($this->getPreparedParams());
  }
}