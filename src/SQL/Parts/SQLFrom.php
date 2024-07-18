<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLFrom
 * 
 * Classe responsável por definir qual a tabela em que a operação será executada
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLFrom extends SQLParts {
  /**
   * Construtor da classe
   * @param string      $tabela      Define qual a tabela que será requisitada no banco de dados
   * @param string      $alias       Define o alias da tabela
   */
  public function __construct(
    private string $tabela, 
    private string $alias = ''
  ) {}

  public function getClausule(): string {
    $clausule = $this->tabela;
    if(strlen(trim($this->alias))) $clausule .= ' AS ' . $this->alias;

    return $clausule;
  }
}