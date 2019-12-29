<?php
/**
 * Classe que faz o envio dos dados recebidos por nossa API SMS para API do Zenvia.
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
 * Classe que faz o envio dos dados recebidos por nossa API SMS para API do Zenvia.
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
abstract class RequestHttpZenvia
{
    /**
     * Método faz o POST via curl para API do Zenvia
     *
     * @param string $request Objeto esperado pela API com os dadsários para
     * @param string $url     Objeto esperado pela API com os dados
     * @param array  $header  Objeto esperado pela API com os dados
     * @param json   $content Objeto esperado pela API com os dados
     *
     * @return json Retorno com status da mensagem
     */
    protected static function sendContentsToAPI($request, $url, $header, $content)
    {
        try {
            // Inicializa cURL para uma URL.
            $ch = curl_init($url);

            // Marca que vai enviar por POST(1=SIM), caso tpRequisicao seja
            // igual a "POST"
            if ($request == 'POST') {

                //Trabalhando com método POST
                curl_setopt($ch, CURLOPT_POST, 1);

                //Passa o conteudo para o campo de envio por POST
                curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            }
            // Se foi passado como parametro, adiciona o cabecalho a requisicao que
            // no caso de consumo da API REST do Zenvia é obrigatório
            if (!empty($header)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }
            // Marca que vai receber string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Ativa a verificação SSL
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            /**
             * Inicia a conexão pega a resposta e guarda nessa variável
             *
             * @var json $response
             */
            $response = curl_exec($ch);

            // Fecha a conexao
            curl_close($ch);

        } catch(Exception $e) {
            return $e->getMessage();
        }
        return $response;
    }
}
?>
