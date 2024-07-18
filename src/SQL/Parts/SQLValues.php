<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLValues
 * 
 * Classe responsável por definir os valores de uma operação de atualização ou inclusão de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLValues extends SQLParts {
  /**
   * Construtor da classe
   * @param SQLValuesGroup[]      $groups      Valores que serão adicionados
   */
  public function __construct(
    private array $groups = []
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $values     = [];
    $quantidade = -1;
    foreach($this->groups as $obValueGroup) {
      if(!$obValueGroup instanceof SQLValuesGroup) continue;

      // VALIDA SE O TOTAL DE ITENS É IGUAL AO PRIMEIRO VALOR DE INSERÇÃO
      if($quantidade < 0) $quantidade = count($obValueGroup->getPreparedParams());
      if(!$this->validateTotalFields($quantidade, $obValueGroup)) continue;

      $values[] = "({$obValueGroup->getClausule()})";
    }

    return implode(', ', $values);
  }

  protected function analisingPreparedParams(): self {
    $quantidade = -1;
    foreach($this->groups as $obValueGroup) {
      // VALIDA SE O TOTAL DE ITENS É IGUAL AO PRIMEIRO VALOR DE INSERÇÃO
      if($quantidade < 0) $quantidade = count($obValueGroup->getPreparedParams());
      if(!$this->validateTotalFields($quantidade, $obValueGroup)) continue;

      // ADICIONA OS PARÂMETROS PREPRADOS DO VALOR QUE ESTÁ VÁLIDO
      foreach($obValueGroup->getPreparedParams() as $preparedParam) $this->addPrepareParams($preparedParam);
    }

    return $this;
  }

  /**
   * Método responsável por validar se os itens do agrupamento possuem a mesma quantidade de campos
   * @param  int                 $quantidade        Quantidade de itens que o agrupamento deve possuir
   * @param  SQLValuesGroup      $obValueGroup      Objeto de agrupamento dos valores
   * @return bool
   */
  private function validateTotalFields(int $quantidade, SQLValuesGroup $obValueGroup): bool {
    return ($obValueGroup->totalItens() === $quantidade);
  }
}