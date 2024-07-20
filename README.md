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
          ```php
            $obProduto->fetchObject()->nome;
          ```
    * **fetchAllObjects:**
      * *Retorno:* Um objeto do tipo `DBEntity`;
          ```php
            foreach($obProduto->fetchAllObjects() as $obDTOProduto) $obDTOProduto->nome;
          ```
    * **fetchColumn:**
      * *Retorno:* Os dados da coluna que foi consultada;
          ```php
            $obProduto->fetchColumn();
          ```
    * **rowCount:**
      * *Retorno:* Quantidade de linhas que foram afetadas pela operação;
          ```php
            $obProduto->rowCount();
          ```
    * **getLastInsertId:**
      * *Retorno:* Retorna o último ID de uma operação de inserção de dados;
          ```php
            $obProduto->getLastInsertId();
          ```
  
  * *OBSERVAÇÃO:*
    * O objeto `DBEntity`, possui alguns métodos para verificar o sucesso da operação e também, retornar os objetos DTOs das respectivas tabelas;
    * Para acessar o valor consultado, basta realizar a chamada do parâmetro;
      * *getSuccess:* Retorna o sucesso da operação;
      * *getMessageOperation:* Retorna a mensagem de erro, caso a operação tenha falhado;
      * *getData:* Se a operação for bem sucedida, retornará o objeto DTO da classe;
      * *getAllData:* Se a operação for bem sucedida, retornará uma array com os objetos DTOs da classe;

## DOCUMENTAÇÃO DE MÉTODOS
  * Documentação dos principais métodos que poderão ser utilizados

### `DBExecute::class`
Classe que será responsável por realizar as operações de banco de dados.
  * **select:**
    * **conditions:** Condições da consulta
      * **Tipos aceitos:** SQLWhereGroup|SQLWhere
      * **Obrigatório:** Não
    * **joins:** Array com as junções com outras tabelas
      * **Tipos aceitos:** SQLJoin[]
      * **Obrigatório:** Não
    * **fields:** Campos que serão retornados na consulta
      * **Tipos aceitos:** SQLFields[]
      * **Obrigatório:** Não
    * **order:** Ordenação dos dados retornados
      * **Tipos aceitos:** SQLOrder
      * **Obrigatório:** Não
    * **limit:** Limite de dados retornados
      * **Tipos aceitos:** Integer
      * **Obrigatório:** Não
    * **offset:** Paginação dos dados
      * **Tipos aceitos:** Integer
      * **Obrigatório:** Não
  
  * **delete:**
    * **conditions:** Condições da remoção
      * **Tipos aceitos:** SQLWhereGroup|SQLWhere
      * **Obrigatório:** Não
  
  * **insert:**
    * **fields:** Array com os campos que serão populados
      * **Tipos aceitos:** SQLFields[]
      * **Obrigatório:** Não
    * **values:** Array com os campos que serão populados
      * **Tipos aceitos:** SQLFields[]
      * **Obrigatório:** Não
    * **conditions:** Condições da inserção dos dados
      * **Tipos aceitos:** SQLWhereGroup|SQLWhere
      * **Obrigatório:** Não
    * **ignore:** Adiciona a cláusula IGNORE a inserção do(s) registro(s)
      * **Tipos aceitos:** Boolean
      * **Obrigatório:** Não
  
  * **insertSelect:**
    * **obSqlSelect:** Query que será utilizada para selecionar os dados que serão inseridos
      * **Tipos aceitos:** SQLSelect
      * **Obrigatório:** Não
    * **fields:** Array com os campos que serão populados
      * **Tipos aceitos:** SQLFields[]
      * **Obrigatório:** Não
    * **ignore:** Adiciona a cláusula IGNORE a inserção do(s) registro(s)
      * **Tipos aceitos:** Boolean
      * **Obrigatório:** Não
  
  * **update:**
    * **set:** Campos que serão atualizados
      * **Tipos aceitos:** SQLSet
      * **Obrigatório:** Não
    * **conditions:** Condições da atualização dos dados
      * **Tipos aceitos:** SQLWhereGroup|SQLWhere
      * **Obrigatório:** Não
    * **joins:** Array com as junções com outras tabelas
      * **Tipos aceitos:** SQLJoin[]
      * **Obrigatório:** Não
    * **limit:** Limite de dados
      * **Tipos aceitos:** Integer
      * **Obrigatório:** Não
  
