<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLFields
 * 
 * Classe responsável por definir os campos de uma consulta
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLFields extends SQLParts {
  /**
   * Construtor da classe
   * @param string      $campo            Campo utilizado na operação
   * @param string      $aliasTabela      Alias da tabela da operação
   * @param string      $aliasCampo       Alias do campo utilizado na operação
   * @param string      $function         Função que deve ser aplicada ao campo
   */
  public function __construct(
    private string $campo, 
    private string $aliasTabela = '',
    private string $aliasCampo = '',
    private string $function = ''
  ) {}

  public function getClausule(): string {
    $clausule = $this->campo;
    if(strlen(trim($this->aliasTabela))) $clausule = $this->aliasTabela . '.' . $clausule;
    if(strlen($this->function)) $clausule = "{$this->function}($clausule)";
    if(strlen($this->aliasCampo)) $clausule .= " AS {$this->aliasCampo}";

    return $clausule;
  }
}