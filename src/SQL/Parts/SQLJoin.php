<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLWhereGroup
 * 
 * Classe responsável por definir as junções com outras tabelas
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLJoin extends SQLParts {
  /**
   * Construtor da classe
   * @param string                      $tabela         Tabela que será considerada no join
   * @param string                      $alias          Alias da tabela
   * @param string                      $tipo           Tipo do join
   * @param SQLWhereGroup|SQLWhere      $condicoes      Condições do agrupamento
   */
  public function __construct(
    private string $tabela,
    private string $alias = '',
    private string $tipo = 'INNER',
    private mixed $condicoes = null
  ) {
    $this->analisingPreparedParams();
  }

  public function getClausule(): string {
    $tiposPermitidos = ['INNER', 'LEFT', 'RIGHT', 'FULL OUTER', 'CROSS', 'SELF'];
    if(
      !($this->condicoes instanceof SQLWhereGroup || $this->condicoes instanceof SQLWhere) || 
      !in_array($this->tipo, $tiposPermitidos)
    ) {
      return '';
    }

    // FORMATAÇÃO DA TABELA
    $tabela = $this->tabela;
    if(strlen(trim($this->alias))) $tabela .= " AS {$this->alias}";
    
    // FORMATAÇÃO DO TIPO DO JOIN
    $tipoJoin = "{$this->tipo} JOIN";
    if($this->tipo == 'SELF') $tipoJoin = 'JOIN';
    
    return "{$tipoJoin} {$tabela} ON {$this->condicoes->getClausule()}";
  }

  protected function analisingPreparedParams(): self {
    foreach($this->condicoes->getPreparedParams() as $preparedParams) {
      if(!is_array($preparedParams)) {
        $this->addPrepareParams($preparedParams);
        continue;
      }

      foreach($preparedParams as $preparedParam) $this->addPrepareParams($preparedParam);
    }
    
    return $this;
  }
}