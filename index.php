<?php
/**
 * Index que recebe a requisição POST e envia para as Classes responsáveis
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

require 'autoload.php';

/**
 * Instância da classe Autoload
 *
 * @var object $autoload
 */
$autoload = new autoload();

// Aceitamos apenas requisições POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    /**
     * Arquivo JSON recebido
     *
     * @var json $json
     */
    $json = file_get_contents('php://input');

    /**
     * Array enviado para tratamento nas classes
     *
     * @var array $obj
     */
    $obj = json_decode($json, true);

    /**
     * Essa Variável recebe a chave do serviço que o cliente deseja consumir
     *
     * @var string $serviceKey
     */
    $serviceKey = key($obj);

    switch ($serviceKey) {

        case 'sendSmsOnly':
            $smsOnly = new SmsOnly($obj);
            break;

        case 'dataSearch':
            $dataSearch = new DataSearch($obj);
            break;

        case 'getRegisteredClient':
            $getRegisteredClient = new GetRegisteredClient($obj);
            break;

        default:
            die('Erro: Serviço não encontrado');
    }

} else {
    die('Falha na Requisição:Entre em contato com o administrador do Sistema VISH!');
}

?>
