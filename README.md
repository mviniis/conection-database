# Connection Database
Gerenciador de conexões com banco de dados, utilizando o PDO do PHP.

# Getting Started
## Instalation
```sh
composer require mviniis/connection-database
```

## Usage
 * Defina a constante `PATH_ENV_APP`, para informar a localização do seu arquivo `.env`;
 * No seu arquivo `.env`, defina as seguintes variáveis:
    ```
      DB_CONNECTION="mysql"
      DB_HOST="host"
      DB_PORT="3306"
      DB_DATABASE="database"
      DB_USERNAME="root"
      DB_PASSWORD=""
      DB_CHARSET="utf8"
    ```
  
  * Em primeiro lugar, é necessário implementar a classe de transição de dados (DTO) de uma tabela;
    ```php
      use \Mviniis\ConnectionDatabase\DTO\DTO;

      class ProdutoDTO extends DTO {
        protected $id; 
        protected $nome; 
        protected $preco; 
        protected $sku; 

        public function getParametrosClasse(): array {
          return ['id', 'nome', 'preco', 'sku'];
        }

        public function getParametrosTabela(): array {
          return ['id', 'nome', 'preco', 'sku'];
        }
      }
    ```
  
  * Para realizar as ações, extenda a classe `Mviniis\ConnectionDatabase\DB\DBExecute`, a uma classe de manipulação da tabela;
  * Defina os seguintes dados para a configuração da classe:
    * **table:** Nome da tabela que será manipulada;
    * **modelData:** Classe DTO da respectiva tabela;
    ```php
      use \Mviniis\ConnectionDatabase\DB\DBExecute;

      class Produto extends DBExecute {
        protected string $table = 'produto';
        protected string $modelData = ProdutoDTO::class;
      }

      $obProduto = new Produto;
    ```

  * Após configurado, pode-se utilizar dos seguintes métodos de manipulação de banco (`select`, `update`, `insert`, `insertSelect`, `delete`);
    ```php
      use Mviniis\ConnectionDatabase\SQL\Parts\SQLWhere;
      use Mviniis\ConnectionDatabase\SQL\Parts\SQLWhereGroup;

      $condicoes = new SQLWhereGroup('AND', [
        new SQLWhere('id', '=', 1)
      ]);
      $obProduto->select($condicoes);
    ```
  
  * Depois, basta utilizar um dos métodos abaixo, para processar a requisição ao banco de dados:
    * **fetchObject:**
      * *Retorno:* Um objeto do tipo `DBEntity`;
    * **fetchAllObjects:**
      * *Retorno:* Um objeto do tipo `DBEntity`;
    * **fetchColumn:**
      * *mixed:* Os dados que foram consultados;
    * **rowCount:**
      * *int:* Quantidade de linhas que foram afetadas pela operação;
  
  * *OBSERVAÇÃO:*
    * O objeto `DBEntity`, possui alguns métodos para verificar o sucesso da operação e também, retornar os objetos DTOs das respectivas tabelas;
      * *getSuccess:* Retorna o sucesso da operação;
      * *getMessageOperation:* Retorna a mensagem de erro, caso a operação tenha falhado;
      * *getData:* Se a operação for bem sucedida, retornará o objeto DTO da classe;
      * *getAllData:* Se a operação for bem sucedida, retornará uma array com os objetos DTOs da classe;
