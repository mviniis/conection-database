<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLValuesItem
 * 
 * Classe responsável por definir um item individual dos valores de uma operação de atualização ou inserção
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLValuesItem extends SQLParts {
  /**
   * Construtor da classe
   * @param mixed       $value       Valor do campo
   */
  public function __construct(private mixed $value) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    return '?';
  }

  protected function analisingPreparedParams(): self {
    $this->addPrepareParams($this->value);
    return $this;
  }
}