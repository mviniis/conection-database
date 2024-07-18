<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLInto
 * 
 * Classe responsável por definir a tabela de uma operação de atualização ou inclusão de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLInto extends SQLParts {
  /**
   * Construtor da classe
   * @param string      $tabela         Tabela que será manipulada
   * @param bool        $addIgnore      Definie se a consulta ignorará dados existentes
   */
  public function __construct(
    private string $tabela,
    private bool $addIgnore = false
  ) {}

  public function getClausule(): string {
    $into = "INTO {$this->tabela}"; 
    return $this->addIgnore ? 'IGNORE ' . $into: $into;
  }
}