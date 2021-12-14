<?php
/**
 * Created by PhpStorm.
 * User: igorm
 * Date: 14/07/2016
 * Time: 10:55
 */

namespace lojista\controllers\actions\pedido;

use lojista\components\AccessDataProducao;
use PhpSigep\Config;
use PhpSigep\Services\Real\SoapClientFactory;
use SoapClient;

class BuscarClienteAction extends Action
{

    public function run()
    {
        set_time_limit(200);
        $accessData = $this->getAccessData(Config::ENV_PRODUCTION);
        $config = new \PhpSigep\Config();
        $config->setAccessData($accessData);
        $config->setEnv(Config::ENV_PRODUCTION);
        $config->setCacheOptions(
            array(
                'storageOptions' => array(
                    // Qualquer valor setado neste atributo será mesclado ao atributos das classes
                    // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                    // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                    'enabled' => false,
                    'ttl' => 10,
                    // "time to live" de 10 segundos
                    'cacheDir' => sys_get_temp_dir(),
                    // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                ),
            )
        );
        \PhpSigep\Bootstrap::start($config);

        $phpSigep = new \PhpSigep\Services\SoapClient\Real();
        $result = $phpSigep->buscaCliente($accessData);

        if (!$result->hasError()) {
            /** @var $buscaClienteResult \PhpSigep\Model\BuscaClienteResult */
            $buscaClienteResult = $result->getResult();

            // Anula as chancelas antes de imprimir o resultado, porque as chancelas não estão é liguagem humana
            $servicos = $buscaClienteResult->getContratos()->cartoesPostagem->servicos;
            foreach ($servicos as &$servico) {
                $servico->servicoSigep->chancela->chancela = 'Chancelas anulada via código.';
            }
        }

        echo '<pre>';
        var_dump($result);
        echo '</pre>';
        //echo "REQUEST:\n" . htmlentities(SoapClientFactory::getSoapClient()->__getLastRequest()) . "\n";
    }

    /*public function run()
    {
        set_time_limit(200);
        $params = $this->getAccessData(Config::ENV_PRODUCTION);

        $soapUrl = 'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl'; // asmx URL of WSDL

        $xml_post_string = '<?xml version="1.0" encoding="UTF-8"?>
                            <SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
                                               xmlns:ns1="http://cliente.bean.master.sigep.bsb.correios.com.br/">
                                <SOAP-ENV:Body>
                                    <ns1:buscaCliente>
                                        <idContrato>9912398979</idContrato>
                                        <idCartaoPostagem>0072351357</idCartaoPostagem>
                                        <usuario>PecaAgora</usuario>
                                        <senha>grnhw8</senha>
                                    </ns1:buscaCliente>
                                </SOAP-ENV:Body>
                            </SOAP-ENV:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Content-length: " . strlen($xml_post_string),
            'SOAPAction: ""'
        );

        $url = $soapUrl;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//        // converting
//        $response = curl_exec($ch);
//        curl_close($ch);
//        echo htmlentities($response);

        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        echo "<pre>";
        var_dump($header);
        die("</pre>");
    }*/

    /*public function run()
    {
        set_time_limit(200);
        $params = $this->getAccessData(Config::ENV_PRODUCTION);

        $opts = array(
            'ssl' => [
                'ciphers' => 'RC4-SHA',
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false
            ],
            'https' => [
                'curl_verify_ssl_peer' => true,
                'curl_verify_ssl_host' => true
            ]
        );
        $client = new SoapClient(
            'https://apps.correios.com.br/SigepMasterJPA/AtendeClienteService/AtendeCliente?wsdl',
            [
                'encoding' => 'ISO-8859-1',
                'verifypeer' => false,
                'verifyhost' => false,
                'soap_version' => SOAP_1_1,
                'trace' => true,
                'exceptions' => true,
                "connection_timeout" => 180,
                'stream_context' => stream_context_create($opts)
            ]
        );

//        echo "<pre>";
//        var_dump($client);
//        die;

        $soapArgs = array(
            'idContrato' => $params->getNumeroContrato(),
            'idCartaoPostagem' => $params->getCartaoPostagem(),
            'usuario' => $params->getUsuario(),
            'senha' => $params->getSenha(),
        );

//        $soapArgs = array(
//            'cep' => '36035260'
//        );

        try {
            $resultado = $client->buscaCliente($soapArgs);
            echo "<pre>";
            var_dump($resultado);
        } catch (\SoapFault $e) {
            echo "<pre>";
            var_dump($client->__getLastRequestHeaders());
            var_dump($client->__getLastResponseHeaders());
//            echo htmlentities($client->__getLastRequest());
//            echo htmlentities($client->__getLastResponse());
            var_dump($e->getMessage());
        }
    }*/
}