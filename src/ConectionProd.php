<?php
/**
 * Classe que realiza a conexão com o Banco
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
 * Classe que realiza a conexão com o Banco
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
abstract class Conection
{

    /**
     * Método protegido que faz a conexão com o banco
     *
     * @return object
     */
    protected function conectaDB()
    {
        $db = [
        'host' => 'db_sms.l70cnn0935.mysql.dbaas.com.br',
        'username' => 'db_sms',
        'password' => 'x@banco1MbkhHp',
        'db' => 'db_sms'
        ];

        try {
            $conn = new PDO(
                "mysql:host={$db['host']};dbname={$db['db']}",
                $db['username'],
                $db['password']
            );

            // set to PDO error modo exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }
}

?>
