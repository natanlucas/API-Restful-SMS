<?php
/**
 * Classe que faz o tratamento dos dados recebidos para consumir API do Zenvia
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
 * Classe que faz o tratamento dos dados recebidos para consumir API do Zenvia
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
class SmsOnly extends RequestHttpZenvia
{
    /**
     * Atributos
     *
     * @var string $_serviceKey   Chave do Serviço a ser executado
     * @var string $_client       Código do cliente gerado no momento do cadastro
     * @var string $_sender       Nome do Remetente do SMS
     * @var string $_cellPhone    Celular do Destinário
     * @var string $_message      Mensagem de Texto
     */
    private $_serviceKey;
    private $_client;
    private $_access;
    private $_sender;
    private $_cellPhone;
    private $_message;

    /**
     * Constantes
     *
     * @var string ENDPOINT_SIMPLE EndPoint para envio de SMS Simples
     * @var string ENDPOINT_MULT   EndPoint para envio de SMS Multiplo
     * @var string TP_REQUEST      Tipo de requisição feita para o ZENVIA
     */
    const ENDPOINT_SIMPLE = 'https://api-rest.zenvia.com/services/send-sms';
    const ENDPOINT_MULT   = 'https://api-rest.zenvia.com/services/send-sms-mult';
    const TP_REQUEST      = 'POST';


    /**
     * Construtor da classe
     *
     * @param array $obj Objeto esperado pela API com os dados necessários para
     *                   utilização do serviço de envio de SMS
     */
    public function __construct($obj)
    {
        $this->serviceKey = key($obj);

        $this->_checkService($obj);

        $this->client = $obj[$this->serviceKey]['aggregateId'];
        $this->access = $obj[$this->serviceKey]['access'];
        $this->sender = $obj[$this->serviceKey]['sender'];
        $this->cellPhone = $obj[$this->serviceKey]['phoneRecipient'];
        $this->message = $obj[$this->serviceKey]['message'];

        $this->_dataValidate();
    }

    /**
     * Método que verifica o objeto e se a chave do serviço é valida e chama o método
     * validador de cada serviço
     *
     * @param array $obj Objeto esperado pela API com os dados necessários para
     *                   utilização do serviço de envio de SMS
     *
     * @return void
     */
    private function _checkService($obj)
    {
        if (!empty($obj) && count(current($obj)) === 5) {

            (array_key_exists('access', $obj[$this->serviceKey]))
                ?: die('Array: Chave de acesso Inválida');

            (array_key_exists('aggregateId', $obj[$this->serviceKey]))
                ?: die('Cliente Inválido');

            (array_key_exists('sender', $obj[$this->serviceKey]))
                ?: die('Remetente Inválido');

            (array_key_exists('phoneRecipient', $obj[$this->serviceKey])
                ?: die('Celular Inválido'));

            (array_key_exists('message', $obj[$this->serviceKey])
                ?: die('Mensagem Inválida'));
        } else {
            die('Erro: JSON inválido');
        }
    }

    /**
     * Método valida dados
     *
     * @return void
     */
    private function _dataValidate()
    {
        // Valida todos os dados recebidos pela API
        // Valida tamanho da chave de acesso
        (is_string($this->access)) ?: die('String: Chave de acesso Inválida');
        (preg_match("/^[0-9a-zA-Z]{43}$/", $this->access))
            ? 'Dado Validado' : die('Match: Chave de acesso Inválida');

        // Valida  o Código do Cliente
        (is_string($this->client)) ?: die('Cliente Inválido');
        (preg_match("/^[0-9]{6}$/", $this->client))
            ?: die('Cliente Inválido');

        // Valida o Remetente
        (is_string($this->sender)) ?: die('Remetente inválido');
        (preg_match("/[0-9a-zA-Z]{1,20}/", $this->sender))
            ?: die('Remetente Inválido');

        // Valida Celular do Remetente
        (is_string($this->cellPhone)) ?: die('Celular Inválido');
        (preg_match("/^[0-9]{13}$/", $this->cellPhone))
            ?: die('Celular inválido');

        // Valida Mesagem de Texto
        (is_string($this->message)) ?: die('Mensagem Inválida');
        (preg_match("/[0-9a-zA-Z]{1,157}/", $this->message))
            ?: die('Mensagem inválida');

        // Pacote SMS limitado a quantidade de 158 caracteres, o total é a soma
        // do tamanho do campo remetente e texto do SMS.
        (strlen($this->sender) + strlen($this->message) <= 158)
            ?: die('Falha: SMS não suportado');

        /**
         * Instância da classe Authenticate
         *
         * @var object $authenticate
         */
        $authenticate = new Authenticate($this->client, $this->access);

        // Chama o método que envia os dados para a classe Crud
        $this->_sendSmsOnly();
    }

    /**
     * Método que faz a chamada a API do Zenvia e o envio dos dados para classe
     * DataBase tratar.
     *
     * @return void
     */
    private function _sendSmsOnly()
    {
        // Header obrigatória para autenticação com Zenvia
        $zenviaHeader = $this->_getBaseHeaderZenvia();

        // Data de agendamento da mensagem no formato ISO 8601 -> Padrão Obrigatório
        // para o Zenvia.
        date_default_timezone_set("America/Sao_Paulo");

        /**
         * Instância da classe DateTime
         *
         * @var object $dateTime
         */
        $dateTime = new DateTime();

        /**
         * Data do envio do SMS
         *
         * @var string $dateNow
         */
        $dateNow = $dateTime->format('Y-m-d\TH:i:s');

        /**
         * Instância da classe Crud
         *
         * @var object $dataBase
         */
        $dataBase = new Crud();

        /**
         * Nome da tabela do cliente onde serão inseridos
         * todos os dados referente ao SMS
         *
         * @var string $clientTable
         */
        $clientTable = 'tb_client_' . $this->client;

        // Cria tabela para o cliente caso não exista
        $dataBase->create($clientTable);

        $bFetch = $dataBase->select(
            "*",
            "$clientTable",
            "ORDER BY id_sms DESC",
            array()
        );

        $fetch=$bFetch->fetch(PDO::FETCH_ASSOC);

        if (!empty($fetch)) {
            $id_sms = $fetch['id_sms'] + 1;
        } else {
            $id_sms = 1;
        }

        /**
         * Dados que API do Zenvia espera
         *
         * @var array $zenviaContents
         */
        $zenviaContents = array(
                        "sendSmsRequest" => array(
                            'from'            => $this->sender,
                            'to'              => $this->cellPhone,
                            'schedule'        => $dateNow,
                            'msg'             => $this->message,
                            'callbackOption'  => 'NONE',
                            'id'              => "1",
                            //'id'              => "$id_sms",
                            'aggregateId'     => $this->client,
                            'flashSms'        => false
                        )
        );

        $zenviaJson = json_encode($zenviaContents, JSON_UNESCAPED_UNICODE);

        /**
         * Resposta do Zenvia
         *
         * @var JSON $zenviaResponse
         */
        $zenviaResponse = RequestHttpZenvia::sendContentsToAPI(
            SmsOnly::TP_REQUEST,
            SmsOnly::ENDPOINT_SIMPLE,
            $zenviaHeader,
            $zenviaJson
        );

        $objRsp = json_decode($zenviaResponse, true);

        //Captura o retorno do Zenvia e armazena em variáveis
        if (is_array($objRsp)) {
            if (key($objRsp) == 'sendSmsResponse') {
                $statusCode        = $objRsp['sendSmsResponse']['statusCode'];
                $statusDescription = $objRsp['sendSmsResponse']['statusDescription'];
                $detailCode        = $objRsp['sendSmsResponse']['detailCode'];
                $detailDescription = $objRsp['sendSmsResponse']['detailDescription'];

            } else {
                die('Falha: Retorno não esperado');
            }
        } else {
            die('Falha: Retorno não esperado');
        }

        /**
         * Dados que serão enviados para o DB
         *
         * @var array $zenviaContents
         */
        $dataObj = array(
            'id_client'             => $this->client,
            'id_sms'                => "$id_sms",
            'sender'                => $this->sender,
            'phone_recipient'       => $this->cellPhone,
            'text_sms'              => $this->message,
            'status_code'           => $statusCode,
            'status_description'    => $statusDescription,
            'detail_code'           => $detailCode,
            'detail_description'    => $detailDescription,
            'date_time'             => $dateNow
        );

        /*
        $handle = fopen("log" . DIRECTORY_SEPARATOR . "log.txt", "a+");

        foreach ($dataObj as $key => $value) {
            $string = "$key : $value";
            fwrite($handle, $string);
            fwrite($handle, "\r\n");
        }
            fwrite($handle, "\r\n");

        fclose($handle);
        exit();

        // Campos e Valores que devem ser inseridos na base de dados

        /**
         * Colunas da tabela do cliente
         *
         * @var string $columnDb
         */
        $columnDb = "id_client, id_sms, sender, phone_recipient, text_sms,
                     status_code, status_description, detail_code,
                     detail_description, date_time";

        /**
         * Valores que seão ligados na classe dataBase
         *
         * @var string $zenviaContents
         */
        $values = ":id_client, :id_sms, :sender, :phone_recipient, :text_sms,
                   :status_code, :status_description, :detail_code,
                   :detail_description, :date_time";

        $responseDB = $dataBase->insert($clientTable, $columnDb, $values, $dataObj);

        /**
         * Após o envio do SMS retornaremos um JSON na requisição de nossa API SMS
         *
         * @var array $zenviaContents
         */
        $responseClientRequest = array(
            'sendSmsOnlyResponse'    => array(
                'id_client'          => $this->client,
                'id_sms'             => "$id_sms",
                'status_code'        =>  $statusCode,
                'status_description' =>  $statusDescription
            )
        );

        echo json_encode($responseClientRequest);
    }

    /**
     * Método que contém os dados da header que é obrigatoria para consumir o API
     * do Zenvia
     *
     * @return array
     */
    private function _getBaseHeaderZenvia()
    {
        $zenviaHeader = array(
                        'Accept: application/json',
                        'Content-Type: application/json; charset=UTF-8',
                        'Authorization: Basic ZHluYW1pYzpicEhaTEl6WUxr'
                    );

        return $zenviaHeader;
    }
}

?>
