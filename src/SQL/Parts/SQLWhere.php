<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLOrder
 * 
 * Classe responsável por definir a ordenação dos dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class SQLWhere extends SQLParts {
  /**
   * Guarda a condição que foi aplicada
   * @var string
   */
  private $condicao = '';

  /**
   * Construtor da classe
   * @param  string       $campo              Campo da tabela que será comparado
   * @param  string       $operador           Operador de comparação
   * @param  string       $valor              Valor que será comparado. Caso seja uma condição especial e necessitar de mais de um valor, separar por pipe '|'
   * @param  bool         $valorCampo         Define se o valor comparado é um campo do banco de dados
   * @param  bool         $adicionarNot       Define se irá adicionar o NOT
   */
  public function __construct(
    private string $campo, 
    private string $operador, 
    private string $valor, 
    private bool $valorCampoBanco = false, 
    private bool $adicionarNot = false
  ) {
    // ADICIONA A PREPARAÇÃO DO CAMPO
    $indiceManipulado = -1;
    if(!$this->valorCampoBanco) $indiceManipulado = $this->addPrepareParams($valor);

    // FORMATAÇÃO DE CONDIÇÃO PADRÃO
    $condicao = "{$campo} {$operador} {$this->getPreparedParam($indiceManipulado, $valor)}";
    if($adicionarNot) $condicao = 'NOT ' . $condicao;

    // VERIFICA SE O OPERADOR POSSUI UMA FORMATAÇÃO DIFERENTE DA PADRÃO
    $operacaoEspecifica = $this->validarOperador($indiceManipulado);
    if(strlen($operacaoEspecifica)) $condicao = $operacaoEspecifica;

    $this->condicao = $condicao;
    return $this;
  }

  /**
   * Método responsável por validar a formatação de operadores específicos
   * @param  int       $index       Define o índice que está sendo manipulado na preparação dos parâmetros
   * @return string
   */
  private function validarOperador(int $index): string {
    $campo        = $this->campo;
    $valor        = $this->valor;
    $operador     = $this->operador;
    $adicionarNot = $this->adicionarNot;
    $formatado    = '';

    switch($operador) {
      case 'IN':
        if($adicionarNot) $operador = "NOT IN";
        if(!$this->valorCampoBanco) $this->addPrepareParams(array_filter(array_map('trim', explode('|', $valor))), $index);
        $formatado = "{$campo} {$operador} ({$this->getPreparedParam($index, $campo)})";
      break;

      case 'LIKE':
        if($adicionarNot) $operador = "NOT LIKE";
        if(!$this->valorCampoBanco) $this->addPrepareParams($valor, $index);
        $formatado = "{$campo} {$operador} {$this->getPreparedParam($index, $campo)}";
      break;

      case 'IS':
        if($adicionarNot) $operador = "IS NOT";
        if(!$this->valorCampoBanco) $this->addPrepareParams($valor, $index);
        $formatado = "{$campo} {$operador} {$this->getPreparedParam($index, $campo)}";
      break;

      case 'BETWEEN':
        $valores = explode('|', $valor);
        $valores = array_filter(array_map('trim', $valores));
        if(!empty($valores) && count($valores) == 2) {
          if(!$this->valorCampoBanco) $this->addPrepareParams($valores, $index);
          $condicao  = implode(' AND ', explode(',', $this->getPreparedParam($index, $campo)));
          $operador  = $adicionarNot ? 'NOT BETWEEN': 'BETWEEN';
          $formatado = "{$campo} {$operador} {$condicao}";
        }
      break;

      case 'CONTAINS':
      case 'FREETEXT':
        if(!$this->valorCampoBanco) $this->addPrepareParams($valor, $index);
        if($adicionarNot) $operador = "NOT {$operador}";
        $formatado = "{$operador}({$campo}, {$this->getPreparedParam($index, $campo)})";
      break;

      case 'MATCH':
        if(!$this->valorCampoBanco) $this->addPrepareParams($valor, $index);
        $formatado = "MATCH({$campo}) AGAINST ({$this->getPreparedParam($index, $campo)})";
        if($adicionarNot) $formatado = "NOT {$formatado}";
      break;
    }

    return $formatado;
  }

  public function getClausule(): string {
    return "({$this->condicao})";
  }
}