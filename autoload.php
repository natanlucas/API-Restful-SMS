<?php
/**
 * Classe Autoload que3 faz o require automatico das Classes existentes no DIR 'src'
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
 * Classe Autoload que3 faz o require automatico das Classes existentes no DIR 'src'
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
class Autoload
{
    /**
     * Construtor da Classe
     */
    public function __construct()
    {
        spl_autoload_register();
    }
}

spl_autoload_register(
    function ($class) {
            include_once 'src' . DIRECTORY_SEPARATOR . $class . '.php';
    }
);

?>
