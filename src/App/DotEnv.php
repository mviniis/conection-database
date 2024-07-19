<?php

namespace Mviniis\ConnectionDatabase\App;

use \Illuminate\Http\JsonResponse;
use \Symfony\Component\Dotenv\Dotenv as SymfonyDotEnv;
use \Mviniis\ConnectionDatabase\Exceptions\ApplicationInvalid;

/**
 * class DotEnv
 * 
 * Classe responsável por manipular os dados de configuração de conexão com o banco
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
class DotEnv {
  /**
   * Objeto de manipulação dos dados de um arquivo de configuração .env
   * @var SymfonyDotEnv
   */
  private static ?SymfonyDotEnv $obDotEnv = null;

  /**
   * Construtor da classe
   * @param SymfonyDotEnv      $obDotEnv      Objeto de manipulação dos dados de um arquivo de configuração .env
   */
  public static function init() {
    if(self::$obDotEnv instanceof SymfonyDotEnv) return;
    self::validate();
    self::loadDotEnv();
  }

  /**
   * Método responsável por carregar as informações do arquivo de configuração
   * @return void
   */
  private static function loadDotEnv(): void {
    self::$obDotEnv = new SymfonyDotEnv;
    self::$obDotEnv->loadEnv(PATH_ENV_APP);
  }

  /**
   * Método responsável por agrupar as validações do arquivo .env
   * @return void
   */
  private static function validate(): void {
    self::verifyDefinedConstantPathDotEnv();
    self::verifyValidPath();
  }

  /**
   * Método responsável por verificar se a constante com o caminho do arquivo .env foi configurada
   * @return void
   */
  private static function verifyDefinedConstantPathDotEnv(): void {
    if(!defined("PATH_ENV_APP")) {
      throw new ApplicationInvalid("A constante 'PATH_ENV_APP' não foi definida.", JsonResponse::HTTP_NOT_IMPLEMENTED);
    }
  }

  /**
   * Método responsável por validar se o arquivo de configuração existe
   * @return void
   */
  private static function verifyValidPath(): void {
    $userConstants = get_defined_constants(true)['user'] ?? [];
    if(!file_exists($userConstants['PATH_ENV_APP'] ?? '')) {
      throw new ApplicationInvalid("O arquivo .env não foi encontrado.");
    }
  }
}