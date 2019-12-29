<?php
/**
 * Classe que recebe uma requisição para consulta de dados no DB e retorna um JSON
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
 * Classe que recebe uma requisição para consulta de dados no DB e retorna um JSON
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
class DataSearch
{
    /**
     * Atributos
     *
     * @var string $_serviceKey   Chave do Serviço a ser executado
     * @var string $_client       Código do cliente gerado no momento do cadastro
     * @var string $_access       Nome do Remetente do SMS
     * @var string $_clientSearch Celular do Destinário
     * @var string $_dateIni      Data Inicial para consulta
     * @var string $_dateFin      Data Final para consulta
     * @var string $_statusSms    Status do SMS que devera ser consultado
     */
    private $_serviceKey;
    private $_client;
    private $_access;
    private $_clientSearch;
    private $_dateIni;
    private $_dateFin;
    private $_statusSms;

    /**
     * Construtor da classe
     *
     * @param array $obj Objeto esperado pela API com os dados necessários para
     *                   utilização do serviço de busca dos dados
     */
    public function __construct($obj)
    {

        $this->serviceKey = key($obj);

        $this->_checkService($obj);

        $this->client = $obj[$this->serviceKey]['aggregateId'];
        $this->access = $obj[$this->serviceKey]['access'];
        $this->clientSearch = $obj[$this->serviceKey]['clientSearch'];
        $this->dateIni = $obj[$this->serviceKey]['dateIni'];
        $this->dateFin = $obj[$this->serviceKey]['dateFin'];
        $this->statusSms = $obj[$this->serviceKey]['statusSms'];

        $this->_dataValidate();
    }

    /**
     * Método mostra todos os atributos
     *
     * @return void
     */
    public function getAllValues()
    {
        return "$this->serviceKey | $this->client | $this->access |
                $this->clientSearch | $this->dateIni | $this->dateFin |
                $this->statusSms";
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
        if (!empty($obj) && count(current($obj)) === 6) {

            (array_key_exists('access', $obj[$this->serviceKey])
                ?: die('Chave de acesso Inválida.'));

            (array_key_exists('aggregateId', $obj[$this->serviceKey])
                ?: die('Cliente Inválido.'));

            (array_key_exists('clientSearch', $obj[$this->serviceKey])
                ?: die('Cliente Inválido.'));

            (array_key_exists('dateIni', $obj[$this->serviceKey])
                ?: die('Data inicial inválida.'));

            (array_key_exists('dateFin', $obj[$this->serviceKey])
                ?: die('Data final inválida.'));

            (array_key_exists('statusSms', $obj[$this->serviceKey])
                ?: die('Campo inválido.'));
        } else {
            die('Erro: JSON inválido');
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

        (is_string($this->clientSearch)) ?: die('String Erro: Tipo Client inválido');
        (preg_match("/^[0-9]{6}$/", $this->clientSearch))
            ?: die('Erro: ID BClient inválido');

        (is_string($this->dateIni)) ?: die('String Erro: Tipop data Ini incorreta');
        (preg_match("/^[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}$/", $this->dateIni))
            ?: die('Erro: Data Inicial inválida');

        (is_string($this->dateFin)) ?: die('String Erro: Tipo data final incorreta');
        (preg_match("/^[0-9]{4}(-)[0-9]{2}(-)[0-9]{2}$/", $this->dateFin))
            ?: die('Erro: Data final inválida');

        (is_string($this->statusSms)) ?: die('String Erro:Tipo statusSms incorreto');
        (preg_match("/^(success|failure|all)$/", $this->statusSms))
            ?: die('Erro: Status SMS inválido');

        // Autentica o usuário
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
        $dataBase = new Crud();

        $clientTable = 'tb_client_' . $this->clientSearch;

        switch ($this->statusSms) {
        case 'success':
                $bFetch = $dataBase->select(
                    "id_client, id_sms, sender, status_description, date_time",
                    "$clientTable",
                    "WHERE date_time >= '$this->dateIni%' AND date_time
                    <= '$this->dateFin%' AND status_code = '00'",
                    array()
                );
            break;

        case 'failure':
                $bFetch = $dataBase->select(
                    "id_client, id_sms, sender, status_description, date_time",
                    "$clientTable",
                    "WHERE date_time >= '$this->dateIni%' AND date_time
                    <= '$this->dateFin%' AND status_code <> '00'",
                    array()
                );
            break;

        case 'all':
                $bFetch = $dataBase->select(
                    "id_client, id_sms, sender, status_description, date_time",
                    "$clientTable",
                    "WHERE date_time >= '$this->dateIni%' AND date_time
                    <= '$this->dateFin%'",
                    array()
                );
            break;

        default:
            die('Falha: Status para a consulta do SMS não existe.');
        }

        $fetch = $bFetch->fetchAll(PDO::FETCH_ASSOC);

        $responseClientRequest = array (
            'dataSearchResponse' =>  array($fetch)
        );

        echo json_encode($responseClientRequest);
    }
}

?>
