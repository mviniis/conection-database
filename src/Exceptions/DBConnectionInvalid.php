<?php

namespace Mviniis\ConnectionDatabase\Exceptions;

use \Exception;

/**
 * class DBConnectionInvalid
 * 
 * Classe responsável por exibir uma exception de erro ao conectar ou executar uma operação no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class DBConnectionInvalid extends Exception {
  public function __construct(string $message, int $code = 0) {
    parent::__construct($message, $code);
  }
}