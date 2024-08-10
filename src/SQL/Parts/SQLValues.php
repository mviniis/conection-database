<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLValuesItem
 * 
 * Classe responsável por definir um item individual dos valores de uma operação de atualização ou inserção
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLValues extends SQLParts {
  /**
   * Construtor da classe
   * @param SQLValuesGroup[]       $values       Valor do campo
   */
  public function __construct(private array $values = []) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $values = [];
    foreach($this->values as $obValueGroup) {
      if(!$obValueGroup instanceof SQLValuesGroup) continue;

      // ADICIONA OS VALORES QUE SERÃO INSERIDOS
      $this->addPrepareParams($obValueGroup->getPreparedParams());
      $value = $obValueGroup->getClausule();
      if(strlen($value)) $values[] = $value;
    }
    
    return implode(', ', $values);
  }

  protected function analisingPreparedParams(): self {
    $quantityBase = null;
    foreach($this->values as $key => $obValueGroup) {
      if(!$obValueGroup instanceof SQLValuesGroup) continue;

      // VERIFICA SE TODOS OS AGRUPAMENTOS POSSUEM A MESMA QUANTIDADE DE VALORES
      $quantityBaseValid = !is_null($quantityBase);
      if(!$quantityBaseValid) $quantityBase = $obValueGroup->getTotalItens();
      if($quantityBase !== $obValueGroup->getTotalItens()) {
        unset($this->values[$key]);
        continue;
      }

      // ADICIONA OS VALORES PREPARADOS
      $this->addPrepareParams($obValueGroup->getPreparedParams());
    }

    // REORDENAÇÃO DOS CAMPOS
    sort($this->values);
    
    return $this;
  }

  /**
   * Método responsável por retornar a quantidade de valores que serão adicionadas
   * @return int
   */
  public function getTotalValues(): int {
    return count($this->getPreparedParams());
  }
}