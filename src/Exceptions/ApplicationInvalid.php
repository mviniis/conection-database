<?php

namespace Mviniis\ConnectionDatabase\Exceptions;

use \Exception;

/**
 * class ApplicationInvalid
 * 
 * Classe responsável por exibir uma exception por falta de configuração
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class ApplicationInvalid extends Exception {
  public function __construct(string $message, int $code = 0) {
    parent::__construct($message, $code);
  }
}