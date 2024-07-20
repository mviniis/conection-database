<?php

namespace Mviniis\ConnectionDatabase\DTO;

/**
 * class DTO
 * 
 * Classe responsável por ser o modelo padrão para os DTOs das tabelas
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
abstract class DTO implements DtoInterface {
  /**
   * Contrutor da classe
   * @param  mixed        $dados       Dados exeternos que serão aplicados ao DTO
   * @param  string       $de          Tipo dos campos que estão nos dados
   */
  public function __construct($dados = [], string $de = 'tabela') {
    if(is_array($dados)) $this->definirDados($dados, $de);
  }

  public function setValorEspecifico(string $campo, array $dados): mixed {
    return $dados[$campo] ?? null;
  }

  public function definirDados(mixed $dados, string $de = 'tabela'): DTO {
    if(empty($dados)) return $this;
    
    $metodo = ($de == 'tabela') ? 'getParametrosTabela': 'getParametrosClasse';
    foreach($this->{$metodo}() as $ordem => $nomeCampo) {
      $campoCorrespondente = $this->getParametrosClasse()[$ordem] ?? null;
      if(is_null($campoCorrespondente)) continue;
      
      $this->{$campoCorrespondente} = $this->setValorEspecifico($nomeCampo, $dados);

      // EVITA DUPLICAÇÃO DOS DADOS
      unset($dados[$campoCorrespondente]);
    }
    
    // ADICIONA OS VALORES EXCEDENTES
    if(!empty($dados)) {
      foreach($dados as $chave => $valorExcedente) $this->$chave = $valorExcedente;
    }

    return $this;
  }

  /**
   * Método responsável por retornar o valor de uma propriedade do DTO
   * @param  string       $propety       Propriedade que está sendo buscada
   * @return mixed
   */
  public function __get(string $propety): mixed {
    $value = null;
    if(property_exists($this, $propety)) $value = $this->{$propety};
    return $value;
  }

  /**
   * Método responsável por definir os dados de um objeto DTO
   * @param  string       $propety       Propriedade que está sendo buscada
   * @param  mixed        $value         Valor que será adicionado
   * @return self
   */
  public function __set(string $propety, mixed $value): void {
    $this->{$propety} = $value;
  }
} 