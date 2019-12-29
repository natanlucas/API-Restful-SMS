<?php
/**
 * Classe CRUD
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Sms
 * @author    Lucas Gonçalves <lucasnatan@live.com>
 * @copyright 2019 (c) Codexs
 * @license   Codex
 * @link      codexs.com.br
 * @CriadoEm  09/12/2019
 */

/**
 * Classe CRUD
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Sms
 * @author    Lucas Gonçalves <lucasnatan@live.com>
 * @copyright 2019 (c) Codexs
 * @license   Codex
 * @link      codexs.com.br
 * @CriadoEm  09/12/2019
 */
class Crud extends Conection
{
    /**
     * Atributos
     *
     * @var object $_crud   Chave do Serviço a ser executado
     */
    private $_crud;

    /**
     * Método que prepara e executa a query
     *
     * @param string $query      Consulta a ser executada
     * @param array  $parametros Parametros
     *
     * @return void
     */
    public function prepareStatements($query, $parametros)
    {
        $this->crud = $this->conectaDB()->prepare($query);
        $this->bindAllValues($this->crud, $parametros);
        $this->crud->execute();
    }

    /**
     * Associa todos os parametros a um SQL
     *
     * @param object $statement Query preparada
     * @param array  $params    Parametros
     *
     * @return object
     */
    public function bindAllValues($statement, $params)
    {
        foreach ($params as $param => $value) {
            $statement->bindValue(':'.$param, $value);
        }

        return $statement;
    }

    /**
     * Insere os dados na Tabela
     *
     * @param string $clientTable Tabela do cliente que esta consumindo o serviço
     * @param string $column      Coluna da tabela
     * @param string $value       Valores que serão inseridos na tabela
     * @param array  $params      Parametros obrigatório
     *
     * @return object
     */
    public function insert($clientTable, $column, $value, $params)
    {
        $this->prepareStatements(
            "INSERT INTO {$clientTable} ({$column}) VALUES ({$value})", $params
        );

        return $this->crud;
    }

    /**
     * Realiza o Select na Tabela
     *
     * @param string $fields      Campo da tabela
     * @param string $clientTable Tabela do Cliente
     * @param string $condition   Condição da Query
     * @param array  $params      Parametros obrigatórios
     *
     * @return object
     */
    public function select($fields, $clientTable, $condition, $params)
    {
        $this->prepareStatements(
            "SELECT {$fields} FROM {$clientTable} {$condition}", $params
        );

        return $this->crud;
    }

    /**
     * Query para criar DB e Tabelas
     *
     * @param string $clientTable Tabela do Cliente
     *
     * @return void
     */
    public function create($clientTable)
    {
        $this->prepareStatements(
            "CREATE TABLE IF NOT EXISTS $clientTable (
                              `id_client` int(6) UNSIGNED ZEROFILL NOT NULL,
                              `id_sms` int(11) NOT NULL PRIMARY KEY,
                              `sender` varchar(20) NOT NULL,
                              `phone_recipient` varchar(20) NOT NULL,
                              `text_sms` varchar(157) NOT NULL,
                              `status_code` varchar(10) NOT NULL,
                              `status_description` varchar(50) NOT NULL,
                              `detail_code` varchar(20) NOT NULL,
                              `detail_description` varchar(50) NOT NULL,
                              `date_time` varchar(50) NOT NULL)
                              ENGINE=MyISAM DEFAULT CHARSET=latin1",
            array()
        );
    }
}
?>
