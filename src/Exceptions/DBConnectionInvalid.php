<?php

namespace Mviniis\ConnectionDatabase\Exceptions;

use \Exception;
use \Illuminate\Http\JsonResponse;

/**
 * class DBConnectionInvalid
 * 
 * Classe responsável por exibir uma exception de erro ao conectar ou executar uma opração no banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class DBConnectionInvalid extends Exception {
  public function render($request) {
    $requestCode = $this->getCode();
    return response()->json(
      ['error' => $this->getMessage()], 
      ($requestCode > 0) ? $requestCode: JsonResponse::HTTP_INTERNAL_SERVER_ERROR
    );
  }
}