<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLOrder
 * 
 * Classe responsável por definir a ordenação dos dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLOrder extends SQLParts {
  /**
   * Construtor da classe
   * @param string      $campo          Campo que será ordenado
   * @param string      $alias          Alias da tabela
   * @param string      $direction      Tipo da ordenação dos dados
   */
  public function __construct(
    private string $campo,
    private string $alias = '', 
    private string $direction = 'ASC'
  ) {}

  public function getClausule(): string {
    $campo = $this->campo;
    if(strlen(trim($this->alias))) $campo = "{$this->alias}.{$campo}";

    $direcao  = strtoupper($this->direction);
    $direcao  = !in_array($direcao, ['ASC', 'DESC']) ? 'ASC': $direcao;
    $clausule = "ORDER BY {$campo} {$direcao}";

    return $clausule;
  }
}