<?php

namespace Mviniis\ConnectionDatabase\DB;

use \Mviniis\ConnectionDatabase\DTO\DTO;

/**
 * class DBEntity
 * 
 * Classe responsável por representar uma entidade do banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class DBEntity {
  /**
   * Define se a operação do banco de dados foi bem sucedida
   * @var bool
   */
  private bool $success = false;

  /**
   * Guarda o resultado de uma operação
   * @var DTO
   */
  private ?DTO $data;

  /**
   * Guarda os resultados de uma operação
   * @var DTO[]
   */
  private array $datas = [];

  /**
   * Construtor da classe
   * @param string      $message      Mensagem da operação
   */
  public function __construct(
    private string $message = ''
  ) {}

  /**
   * Método responsável por definir o status de sucesso da operação
   * @param  bool      $success      Status de sucesso da operação
   * @return void
   */
  public function setSuccess(bool $success): void {
    $this->success = $success;
  }

  /**
   * Método responsável por retornar o status de sucesso da operação
   * @return bool
   */
  public function getSuccess(): bool {
    return $this->success;
  }

  /**
   * Método responsável por retornar a mensagem da operação
   * @return string
   */
  public function getMessageOperation(): string {
    return $this->message;
  }

  /**
   * Define os dados de uma única operação
   * @param  DTO       $obResponse       Dados da tabela consultada
   * @return self
   */
  public function setData(DTO $obResponse): self {
    $this->data = $obResponse;
    return $this;
  }

  /**
   * Método responsável por guardar os dados de uma operação
   * @param  DTO[]      $responses      Dados que serão retornados
   * @return self
   */
  public function setAllData(array $responses): self {
    if(!empty($responses)) {
      foreach($responses as $obDTO) {
        if(!$obDTO instanceof DTO) continue;
        $this->datas[] = $obDTO;
      }
    }

    return $this;
  }

  /**
   * Método responsável por retornar os dados de uma única operação
   * @return DTO
   */
  public function getData(): DTO {
    return $this->data;
  }

  /**
   * Método responsável por retornar os dados de várias operações
   * @return DTO[]
   */
  public function getAllData(): array {
    return $this->datas;
  }
}