<?php

namespace Mviniis\ConnectionDatabase\DTO;

/**
 * interface DtoInterface
 * 
 * Inteterface responsável por definir os método obrigatórios em um componente de transacionamento de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
interface DtoInterface {
  /**
   * Método responsável por retornar os dados dos parâmetros da classe
   * @return array [ordem => parametroClasse]
   */
  public function getParametrosClasse(): array;
  
  /**
   * Método responsável por retornar os dados dos parâmetros da classe
   * @return array [ordem => parametro_tabela]
   */
  public function getParametrosTabela(): array;

  /**
   * Método responsável por salvar os dados externos no DTO
   * @param  mixed        $dados       Dados exeternos que serão aplicados ao DTO
   * @param  string       $de          Tipo dos campos que estão nos dados
   * @return DtoInterface
   */
  public function definirDados(mixed $dados, string $de): DtoInterface;

  /**
   * Método responsável por formatar um valor específico de um campo
   * @param  string       $campo       Nome do parâmetro da classe
   * @param  array        $dados       Dados do objeto de dados
   * @return mixed
   */
  public function setValorEspecifico(string $campo, array $dados): mixed;
}