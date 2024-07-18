<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLSetItem
 * 
 * Classe responsável por definir um campo de uma operação de atualização de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLSetItem extends SQLParts {
  /**
   * Construtor da clase
   * @param string      $field                  Campo que será atualizado
   * @param mixed       $value                  Valor com o qual campo será atualizado
   * @param bool        $isFieldOtherTable      Define se o valor representa um campo de outra tabela
   */
  public function __construct(
    private string $field,
    private mixed $value,
    private bool $isFieldOtherTable = false
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $value = $this->isFieldOtherTable ? $this->value: '?';
    return "{$this->field} = {$value}";
  }
  
  protected function analisingPreparedParams(): self {
    if(!$this->isFieldOtherTable) $this->addPrepareParams($this->value);
    return $this;
  }
}