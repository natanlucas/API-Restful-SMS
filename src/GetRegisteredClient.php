<?php
/**
 * Classe busca todos os clientes na Base, exceto o id que fez a requisição.
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Sms
 * @author    Lucas Gonçalves <lucasnatan@live.com>
 * @copyright 2019 (c) Codexs
 * @license   Codex
 * @link      codexs.com.br
 * @CriadoEm  12/12/2019
 */

/**
 * Classe busca todos os clientes na Base, exceto o id que fez a requisição.
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Sms
 * @author    Lucas Gonçalves <lucasnatan@live.com>
 * @copyright 2019 (c) Codexs
 * @license   Codex
 * @link      codexs.com.br
 * @CriadoEm  12/12/2019
 */
class GetRegisteredClient
{
    /**
     * Atributos
     *
     * @var string $_serviceKey   Chave do Serviço a ser executado
     * @var string $_client       Código do cliente gerado no momento do cadastro
     * @var string $_access       Nome do Remetente do SMS
     */
    private $_serviceKey;
    private $_client;
    private $_access;

    /**
     * Construtor da classe
     *
     * @param array $obj Objeto esperado pela API com os dados necessários para
     *                   utilização do serviço de Busca de Clientes cadastrados
     */
    public function __construct($obj)
    {
        $this->serviceKey = key($obj);

        $this->_checkService($obj);

        $this->client = $obj[$this->serviceKey]['aggregateId'];
        $this->access = $obj[$this->serviceKey]['access'];

        $this->_dataValidate();
    }

    /**
     * Método mostra todos os atributos
     *
     * @return void
     */
    public function getAllValues()
    {
        return "$this->serviceKey | $this->client | $this->access";
    }

    /**
     * Método que verifica se os dados da requisição estão de acordo com o serviço
     *
     * @param array $obj Objeto esperado pela API com os dados necessários para
     *                   utilização do serviço de consulta de dados
     *
     * @return void
     */
    private function _checkService($obj)
    {
        if (!empty($obj) && count(current($obj)) === 2) {

            (array_key_exists('access', $obj[$this->serviceKey])
                ?: die('Chave de acesso Inválida.'));

            (array_key_exists('aggregateId', $obj[$this->serviceKey])
                ?: die('Cliente Inválido.'));
        }
    }

    /**
     * Método de validação dos dados recebidos pela API
     *
     * @return void
     */
    private function _dataValidate()
    {
        (is_string($this->access)) ?: die('String Erro: Tipo Access inválido');
        (preg_match("/^[0-9a-zA-Z]{43}$/", $this->access))
            ?: die('Erro: Chave de acesso inválida');

        (is_string($this->client)) ?: die('String Erro: Tipo Client inválido');
        (preg_match("/^[0-9]{6}$/", $this->client))
            ?: die('Erro: ID Cliente inválido');

        /**
         * Instância da classe Authenticate
         *
         * @var object $dateTime
         */
        $authenticate = new Authenticate($this->client, $this->access);

        // Chama o método que envia os dados para a classe Crud
        $this->_sendQuery();
    }

    /**
     * Método que envia as query para a classe Crud
     *
     * @return JSON
     */
    private function _sendQuery()
    {
        /**
         * Instância da classe Crud
         *
         * @var object $dataBase
         */
        $dataBase = new Crud();

        $bFetch = $dataBase->select(
            "id_client, name_client",
            "tb_users",
            "WHERE id_client <> $this->client",
            array()
        );

        $fetch = $bFetch->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($fetch);
    }
}

?>
