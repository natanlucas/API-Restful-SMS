<?php
/**
 * Classe que faz a autenticação do usuário
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
 * Classe que faz a autenticação do usuário
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
class Authenticate
{
    /**
     * Atributos
     *
     * @var $_access    Chave de acesso que deve ser identica a cadastrada no DB
     * @var $_id_client ID do cliente cadastrado no DB
     */
    private $_access;
    private $_id_client;

    /**
     * Construtor da Classe
     *
     * @param string $client ID do cliente que esta fazendo a requisição para API
     * @param string $access Chave de acesso
     */
    public function __construct($client, $access)
    {
        $this->id_client = $client;
        $this->access = $access;

        $this->_verifyAccess();
    }

    /**
     * Método faz o decrypt da senha de acesso do cliente
     *
     * @return string
     */
    private function _decrypt()
    {
        $this->access = base64_decode($this->access);

        return $this->access;
    }

    /**
     * Método que verifica se o acesso é valido
     *
     * @return void
     */
    private function _verifyAccess()
    {
        /**
         * Instancia da Classe Crud
         *
         * @var object $dataBase
         */
        $dataBase = new Crud();

        $bFetch = $dataBase->select(
            "access",
            "tb_users",
            "WHERE `id_client` = '$this->id_client' LIMIT 1",
            array()
        );

        $fetch = $bFetch->fetch(PDO::FETCH_ASSOC);

        if ($fetch['access'] != $this->_decrypt()) {
            die('Erro na autenticação');
        }
    }
}

?>
