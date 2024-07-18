<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLWhereGroupItem
 * 
 * Classe responsável por definir as condições de um bloco de condições
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLWhereGroupItem extends SQLParts {
  /**
   * Construtor da classe
   * @param string          $operador       Operador lógico das condições
   * @param SQLWhere[]      $condicoes      Array com as condições do grupo
   */
  public function __construct(
    private string $operador,
    private array $condicoes = []
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $condicoesValidas = [];
    foreach($this->condicoes as $obWhere) {
      if(!$obWhere instanceof SQLWhere) continue;

      $condicoesValidas[] = $obWhere->getClausule();
    }
    
    // VERIFICA SE EXISTEM CONDIÇÕES VÁLIDAS
    if(empty($condicoesValidas)) return '';

    return '('. implode(" {$this->operador} ", $condicoesValidas) .')';
  }

  protected function analisingPreparedParams(): self {
    foreach($this->condicoes as $obWhere) {
      if(!$obWhere instanceof SQLWhere) continue;

      // ADICIONA OS VALORES
      foreach($obWhere->getPreparedParams() as $preparedParamsWhere) {
        if(!is_array($preparedParamsWhere)) {
          $this->addPrepareParams($preparedParamsWhere);
          continue;
        }

        // PREPARAÇÃO DE MÚLTIPLOS PARÂMETROS
        foreach($preparedParamsWhere as $preparedParamWhere) $this->addPrepareParams($preparedParamWhere);
      }
    }

    return $this;
  }
}