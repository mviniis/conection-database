<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLWhereGroup
 * 
 * Classe responsável por definir as condições que estão agrupadas
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLWhereGroup extends SQLParts {
  /**
   * Construtor da classe
   * @param string                         $operador        Operador condicional do agrupamento
   * @param SQLWhereGroup[]|SQLWhere       $condicoes       Dados das condições do agrupamento
   */
  public function __construct(
    private string $operador,
    private array $condicoes = []
  ) {
    $this->analisingPreparedParams();
  }
  
  public function getClausule(): string {
    $condicoesValidas = [];

    // MONTAGEM DAS CONDIÇÕES
    foreach($this->condicoes as $obCondicaoItem) {
      $sqlGrupoValido     = $obCondicaoItem instanceof SQLWhereGroup;
      $sqlCondicaoValido  = $obCondicaoItem instanceof SQLWhere;
      if(!$sqlCondicaoValido && !$sqlGrupoValido) continue;

      $condicoesValidas[] = $obCondicaoItem->getClausule();
    }

    // VERIFICA SE EXISTEM CONDIÇÕES VÁLIDAS
    if(empty($condicoesValidas)) return '';

    return '('. implode(" {$this->operador} ", $condicoesValidas) .')';
  }

  protected function analisingPreparedParams(): self {
    foreach($this->condicoes as $obCondicaoItem) {
      $sqlGrupoValido     = $obCondicaoItem instanceof SQLWhereGroup;
      $sqlCondicaoValido  = $obCondicaoItem instanceof SQLWhere;
      if(!$sqlCondicaoValido && !$sqlGrupoValido) continue;

      // ADICIONA OS VALORES
      foreach($obCondicaoItem->getPreparedParams() as $preparedParamsWhere) {
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

  /**
   * Método responsável por definir os parâmetros preparados do agrupamento de condições em ordem
   * @param  array      $paramsCondition      Parâmetros preparados das condições do agrupamento
   * @return self
   */
  private function addPreparedParamsGroup(array $paramsCondition): self {
    // ADICIONA OS PARÂMETROS DAS CONDIÇÕES DO AGRUPAMENTO
    foreach($paramsCondition as $conditionParam) $this->addPrepareParams($conditionParam);

    return $this;
  }
}