<?php

namespace Mviniis\ConnectionDatabase\DB;

use \PDO;
use \PDOException;
use \Illuminate\Http\JsonResponse;
use \Mviniis\ConnectionDatabase\Exceptions\DBConnectionInvalid;

/**
 * class DBConnection
 * 
 * Classe responsável por realizar a conexão com o banco de dados
 * 
 * @author Matheus Vinicius <matheusv.16santos@gmail.com>
 */
abstract class DBConnection {
  /**
   * Define o host do banco
   * @var string
   */
  private static string $host;

  /**
   * Define o nome do banco de dados acessado
   * @var string
   */
  private static string $dbName;

  /**
   * Define a porta de acesso ao banco
   * @var string
   */
  private static string $port;

  /**
   * Define a linguagem do banco
   * @var string
   */
  private static string $charset;

  /**
   * Define o nome do usuário que iriá acessar o banco
   * @var string
   */
  private static string $username;

  /**
   * Define a senha de acesso ao banco
   * @var string
   */
  private static string $password;

  /**
   * Guarda a configuração de DNS do servidor
   * @var string
   */
  private static string $dns;

  /**
   * Guarda a intância de conexão com o banco
   * @var PDO
   */
  private static ?PDO $pdo = null;

  /**
   * Método responsável por iniciar a conexão com o banco de dados
   * @return void
   */
  public static function init() {
    self::carregarCredenciais();
    self::connect();
  }

  /**
   * Método responsável por carregar as credenciais de acesso ao banco de dados
   * @return void
   */
  private static function carregarCredenciais(): void {
    $configuracoesObrigatorias = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_CHARSET'];

    // VERIFICA SE AS CONFIGURAÇÕES DE BANCO FORAM CARREGADAS
    foreach($configuracoesObrigatorias as $hashEnv) {
      if(!in_array($hashEnv, array_keys($_ENV ?? []))) throw new DBConnectionInvalid('Configurações de acesso ao banco inválidas!');
    }

    // CARREGA AS CONFIGURAÇÕES
    self::$host     = 'host=' . $_ENV['DB_HOST'];
    self::$dbName   = 'dbname=' . $_ENV['DB_DATABASE'];
    self::$port     = 'port=' . $_ENV['DB_PORT'];
    self::$charset  = 'charset=' . $_ENV['DB_CHARSET'];
    self::$username = $_ENV['DB_USERNAME'];
    self::$password = $_ENV['DB_PASSWORD'];
    self::$dns      = $_ENV['DB_CONNECTION'] . ':' . implode(';', [self::$host, self::$dbName, self::$port, self::$charset]);
  }

  /**
   * Método responsável por verificar se a conexão com o PDO foi carregada
   * @return bool
   */
  private static function pdoCarregado(): bool {
    return self::$pdo instanceof PDO;
  }

  /**
   * Método responsável por realizar a conexão com o banco de dados
   * @return self
   */
  private static function connect(): void {
    if(self::pdoCarregado()) return;

    $options = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
      self::$pdo = new PDO(self::$dns, self::$username, self::$password, $options);
    } catch(PDOException $e) {
      throw new DBConnectionInvalid($e->getMessage(), JsonResponse::HTTP_SERVICE_UNAVAILABLE);
    }
  }

  /**
   * Método responsável por retornar o objeto de conexão com o banco
   * @return PDO
   */
  public static function getPdo(): mixed {
    if(!self::pdoCarregado()) self::init();
    return self::$pdo;
  }

  /**
   * Método responsável por validar se o banco de dados está online
   * @return bool
   */
  public static function databaseOnline(): bool {
    try {
      self::init();
      return self::pdoCarregado();
    } catch(\Exception $ex) {
      return false;
    }
  }
}
