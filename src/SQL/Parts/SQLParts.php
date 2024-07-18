<?php

namespace Mviniis\ConnectionDatabase\SQL\Parts;

/**
 * class SQLParts
 * 
 * Classe responsável por definir os métodos em comum de uma parte da querie
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
abstract class SQLParts {
  /**
   * Guarda os parâmetros que devem ser validados pelo PDO
   * @var array
   */
  private $prepareParams = [];

  /**
   * Método responsável por retornar a clausula da parte formatada
   * @return string
   */
  abstract public function getClausule(): string;

  /**
   * Método responsável por retornar os valores dos parâmetros preparados para a querie
   * @return array
   */
  public function getPreparedParams(): array {
    return $this->prepareParams;
  }

  /**
   * Método responsável por retornar o parâmetro preparado
   * @param  int          $index       Posição do campo
   * @param  string       $field       Campo que está sendo preparado
   * @return string
   */
  protected function getPreparedParam(int $index, string $filed): string {
    $preparedParam = $filed;

    if(isset($this->prepareParams[$index])) {
      $values = $this->prepareParams[$index];

      if(is_array($values)) {
        $formatedValues = [];
        foreach(array_keys($values) as $indexValues) $formatedValues[$indexValues] = '?';

        $preparedParam = implode(',', $formatedValues);
      } else {
        $preparedParam = '?';
      }
    }

    return $preparedParam;
  }

  /**
   * Método responsável por adicionar um novo valor que deverá ser preparado pelo PDO antes da consulta
   * @param  mixed          $values       Valores que deverão ser preparados
   * @param  int            $index        Índice do campo sendo manipulado
   * @return int
   */
  protected function addPrepareParams(mixed $values, ?int $index = null): int {
    $existeIndice = is_numeric($index) && isset($this->prepareParams[$index]);
    return ($existeIndice) ? $this->updatePrepareParam($index, $values): $this->addNewPrepareParam($values);
  }

  /**
   * Método responsável por analizar os parâmetros preparados
   * @return self
   */
  protected function analisingPreparedParams(): self {
    return $this;
  }

  /**
   * Método responsável por adicionar um novo valor a preparação
   * @param  mixed          $values       Valores que deverão ser preparados
   * @return int
   */
  private function addNewPrepareParam(mixed $values): int {
    $newIndex                       = count($this->prepareParams);
    $this->prepareParams[$newIndex] = $values;
    return $newIndex;
  }

  /**
   * Método responsável por realizar a atualização de um campo de preparação
   * @param  int            $index          Índice que terá o valor manipulado
   * @param  mixed          $values         Valores que deverão ser preparados
   * @return int
   */
  private function updatePrepareParam(int $index, mixed $values): int {
    $this->prepareParams[$index] = $values;
    return $index;
  }
}