<?php

namespace console\controllers\actions\omie;

use yii\helpers\Json;

class Omie
{

    /**
     * Configuration for urls
     */
    protected $urls = array(
        'API_ROOT_URL'                  => 'https://api.mercadolibre.com',
        'API_ROOT_URL_OMIE'             => 'https://app.omie.com.br',
        'AUTH_URL'                      => 'http://auth.mercadolivre.com.br/authorization',
        'CONSULTA_CONTACORRENTE_URL'    => '/api/v1/geral/contacorrente/?JSON=',
        'CONSULTA_PRODUTO_URL'          => '/api/v1/geral/produtos/?JSON=',
        'CONSULTA_CLIENTE_URL'          => '/api/v1/geral/clientes/?JSON=',
        'CONSULTA_PRODUTO_PEDIDO_URL'   => '/api/v1/produtos/pedido/',
        'OAUTH_URL'                     => '/oauth/token'
    );

    //http://app.omie.com.br/api/v1/geral/contacorrente/?JSON={"call":"Pesq
    //uisarContaCorrente","app_key":"1560731700","app_secret":"226dcf372489b
    //b45ceede61bfd98f0f1","param":[{"pagina":1,"registros_por_pagina":100,"
    //apenas_importado_api":"N"}]}

    /**
     * Configuration for CURL
     */
    protected $curl_opts = array(
        CURLOPT_USERAGENT => "MELI-PHP-SDK-1.0.0",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 60
    );

    protected $client_id;
    protected $client_secret;
    protected $client_id_omie;
    protected $client_secret_omie;

    /**
     * Constructor method. Set all variables to connect in Meli
     *
     * @param string $client_id
     * @param string $client_secret
     * @param string $access_token
     */
    public function __construct($client_id, $client_secret, $urls = null, $curl_opts = null)
    {
        $this->client_id            = $client_id;
        $this->client_secret        = $client_secret;
        $this->client_id_omie       = '531935801397';
        $this->client_secret_omie   = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
        $this->urls                 = $urls ? $urls : $this->urls;
        $this->curl_opts            = $curl_opts ? $curl_opts : $this->curl_opts;
        //echo "\n Teste: ";
        //var_dump($this->client_id, $this->client_secret, $this->urls, $this->curl_opts);
        //echo ";\n ";
    }

    /**
     * Return an string with a complete Meli login url.
     *
     * @param string $redirect_uri
     * @return string
     */
    public function getAuthUrl($redirect_uri)
    {
        $params = array("client_id" => $this->client_id, "response_type" => "code", "redirect_uri" => $redirect_uri);
        $auth_uri = $this->urls['AUTH_URL'] . "?" . http_build_query($params);
        return $auth_uri;
    }

    /**
     * Executes a POST Request to authorize the application and take
     * an AccessToken.
     *
     * @param string $code
     * @param string $redirect_uri
     *
     */
    public function authorize($code, $redirect_uri)
    {



        $body = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "code" => $code,
            "redirect_uri" => $redirect_uri
        );

        $opts = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );

        return $this->execute($this->urls['OAUTH_URL'], $opts);
    }
    /**
     * Execute a POST Request to create a new AccessToken from a existent refresh_token
     *
     * @param string $refresh_token
     *
     * @return string|mixed
     */
    public function refreshAccessToken($refresh_token = null)
    {
        if ($refresh_token) {

            $body = array(
                "grant_type" => "refresh_token",
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "refresh_token" => $refresh_token
            );

            $opts = array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
            );
            //echo "\n Teste: ";
            //var_dump($this->urls['OAUTH_URL']);
            //echo ";\n ";
            return $this->execute($this->urls['OAUTH_URL'], $opts);
        } else {
            $result = array(
                'error' => 'Offline-Access is not allowed.',
                'httpCode'  => null
            );
            return $result;
        }
    }

    /**
     * Execute a GET Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function get($path, $params = null)
    {
        echo "\n ********************************************************************* \n";
        $exec = $this->execute($path, null, $params);
        return $exec;
    }

    /**
     * Execute a POST Request
     *
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array())
    {
        $body = Json::encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body
        );
        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute a PUT Request
     *
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params)
    {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );

        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute a DELETE Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params)
    {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE"
        );

        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a OPTION Request
     *
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null)
    {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
        );

        $exec = $this->execute($path, $opts, $params);
        return $exec;
    }

    /**
     * Execute all requests and returns the json body and headers
     *
     * @param string $path
     * @param array $opts
     * @param array $params
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = array())
    {
        //echo "\n :: "; print_r($path); echo " :: \n";
        $uri = $this->make_path($path, $params);
        //$uri = 'http://app.omie.com.br/api/v1/geral/contacorrente/?JSON={"call":"PesquisarContaCorrente","app_key":"1560731700","app_secret":"226dcf372489bb45ceede61bfd98f0f1","param":[{"pagina":1,"registros_por_pagina":100,"apenas_importado_api":"N"}]}';
        //print_r($uri); echo "\n111\n";
        $ch = curl_init($uri);
        //print_r($ch); echo "\n222\n";
        curl_setopt_array($ch, $this->curl_opts);
        //print_r($ch); echo "\n333\n";
        if (!empty($opts)) {
            curl_setopt_array($ch, $opts);
        }
        //print_r($ch); echo "\n444\n";
        //print_r(curl_exec($ch)); echo "\n555\n";
        $return["body"] = json_decode(curl_exec($ch));
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $return["urlTeste"] = $uri;
        curl_close($ch);

        return $return;
    }

    /**
     * Check and construct an real URL to make request
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array())
    {
        if (!preg_match("/^http/", $path)) {
            if (!preg_match("/^\//", $path)) {
                $path = '/' . $path;
                echo " \n 1 \n";
                //print_r($path);
            }
            $uri        = $this->urls['API_ROOT_URL'] . $path;
            $uri_omie   = $this->urls['API_ROOT_URL_OMIE'] . $path;

            echo " \n 2 \n"; //print_r($uri_omie);
        } else {
            $uri = $path;
            echo " \n 3 \n"; //print_r($uri);
        }
        if (!empty($params)) {
            $paramsJoined = array();
            foreach ($params as $param => $value) {
                $paramsJoined[] = "$param=$value";
            }
            $params = '?' . implode('&', $paramsJoined);
            echo " \n 6 \n"; //print_r($params);
            $uri = $uri . $params;
            echo " \n 7 \n"; //print_r($uri);
        }
        echo " \n 8 \n"; //print_r($uri);

        return $uri;
        //return $uri_omie;
    }

    public function consulta_conta_corrente($path, $body = null, $params = array())
    {

        $body = json_encode($body);
        $opts = array(
            //CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            //CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => $body
        );

        $exec = $this->executa($this->urls['CONSULTA_CONTACORRENTE_URL'], $opts, $params, $body);
        return $exec;
    }

    public function consulta_produto($path, $body = null, $params = array())
    {

        return $this->consulta_post($path, $body, $params);

        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_PRODUTO_URL'], $opts, $params, $body);
        return $exec;
    }

    public function cria_produto($path, $body = null, $params = array())
    {

        return $this->consulta_post($path, $body, $params);

        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_PRODUTO_URL'], $opts, $params, $body);
        return $exec;
    }

    public function altera_produto($path, $body = null, $params = array())
    {

        return $this->consulta_post($path, $body, $params);

        //$body = json_encode($body, JSON_UNESCAPED_UNICODE);
        //echo "\n\n R1: "; var_dump($body); echo "\n\n : R1"; 
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        //echo "\n\n R2: "; var_dump($body); echo "\n\n : R2";
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_PRODUTO_URL'], $opts, $params, $body);
        return $exec;
    }

    public function cria_cliente($path, $body = null, $params = array())
    {

        return $this->consulta_post($path, $body, $params);

        $body = json_encode($body, JSON_UNESCAPED_UNICODE);
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_CLIENTE_URL'], $opts, $params, $body);
        return $exec;
    }

    public function consulta_cliente($path, $body = null, $params = array())
    {

        //print_r($this->consulta_post($path, $body, $params)); die;
        return $this->consulta_post($path, $body, $params);

        $body = json_encode($body);
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_CLIENTE_URL'], $opts, $params, $body);
        return $exec;
    }

    public function lista_cliente($path, $body = null, $params = array())
    {

        $body = json_encode($body);
        $opts = array();

        $exec = $this->executa($this->urls['CONSULTA_CLIENTE_URL'], $opts, $params, $body);
        return $exec;
    }

    public function executa($path, $opts = array(), $params = array(), $body = null)
    {
        $uri = $this->cria_caminho($path, $body, $params);
        //$uri = 'http://app.omie.com.br/api/v1/geral/contacorrente/?JSON={"call":"PesquisarContaCorrente","app_key":"1560731700","app_secret":"226dcf372489bb45ceede61bfd98f0f1","param":[{"pagina":1,"registros_por_pagina":100,"apenas_importado_api":"N"}]}';
        //echo "\n\nkkkkk:"; var_dump($uri); echo "\n\nkkkkk:";
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curl_opts);

        if (!empty($opts)) {
            curl_setopt_array($ch, $opts);
        }

        $return["body"] = json_decode(curl_exec($ch), JSON_UNESCAPED_UNICODE);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $return["urlTeste"] = $uri;
        curl_close($ch);

        return $return;
    }

    public function cria_caminho($path, $body, $params = array())
    {
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);

        $body = substr(str_replace("\\", "", $body), 1);

        $uri  = $this->urls['API_ROOT_URL_OMIE'] . $path . $body;
        //echo "\n\n|| ";var_dump($uri);echo " ||\n\n";
        $uri = substr($uri, 0, -1);

        $uri = str_replace(":{", ":[{", $uri);
        $uri = str_replace("}}", "}]}", $uri);
        $uri = str_replace("}}", "}]}", $uri);

        return $uri;
    }

    public function executa_pedido($path, $opts = array(), $params = array(), $body = null)
    {
        $uri = $this->cria_caminho_pedido($path, $body, $params);
        $ch = curl_init($uri);
        curl_setopt_array($ch, $this->curl_opts);

        if (!empty($opts)) {
            curl_setopt_array($ch, $opts);
        }

        $return["body"] = json_decode(curl_exec($ch), JSON_UNESCAPED_UNICODE);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $return["urlTeste"] = $uri;
        curl_close($ch);

        return $return;
    }

    public function cria_caminho_pedido($path, $body, $params = array())
    {
        $body = json_encode($body, JSON_UNESCAPED_UNICODE);

        $body = substr(str_replace("\\", "", $body), 1);

        $uri  = $this->urls['API_ROOT_URL_OMIE'] . $path . $body;
        //echo "\n\n|| ";var_dump($uri);echo " ||\n\n";
        $uri = substr($uri, 0, -1);
        echo "\n\n"; //print_r($uri); echo "\n\n";
        $uri = str_replace(":{", ":[{", $uri);
        $uri = str_replace("}}", "}]}", $uri);
        $uri = str_replace("}}", "}]}", $uri);
        $uri = str_replace('},"det"', '}],"det"', $uri);
        //$uri = str_replace('"quantidade_itens":1}','"quantidade_itens":1}]',$uri);
        $uri = str_replace('"simples_nacional":""}', '"simples_nacional":""}]', $uri);
        $uri = str_replace('},"ipi":', '}],"ipi":', $uri);
        $uri = str_replace('},"pis_padrao":', '}],"pis_padrao":', $uri);
        $uri = str_replace('},"icms_sn"', '}],"icms_sn"', $uri);
        $uri = str_replace('},"produto":', '}],"produto":', $uri);
        $uri = str_replace('}]},"frete"', '}]}],"frete"', $uri);
        $uri = str_replace('},"informacoes_adicionais"', '}],"informacoes_adicionais"', $uri);

        return $uri;
    }

    public function cria_pedido($path, $body = null, $params = array())
    {

        return $this->cria_pedido_post($this->urls['CONSULTA_PRODUTO_PEDIDO_URL'], $body, $params);

        $body = json_encode($body);
        $opts = array();

        $exec = $this->executa_pedido($this->urls['CONSULTA_PRODUTO_PEDIDO_URL'], $opts, $params, $body);
        //$exec = $this->cria_caminho_pedido($path, $body, $params);
        return $exec;
    }

    public function consulta_pedido($path, $body = null, $params = array())
    {

        return $this->consulta_post("/api/v1/produtos/pedido/", $body, $params);

        $body = json_encode($body);
        $opts = array();

        $exec = $this->executa('/api/v1/produtos/pedido/?JSON=', $opts, $params, $body);
        return $exec;
    }

    public function consulta($path, $body = null, $params = array())
    {

        //echo "<pre>"; var_dump($body); echo "</pre>";
        return $this->consulta_post($path, $body, $params);

        $body = json_encode($body);
        $opts = array();

        $exec = $this->executa($path, $opts, $params, $body);
        return $exec;
    }

    public function consulta_post($path, $body, $params = array())
    {

        //echo "<pre>"; var_dump($body); echo "</pre>";

        $body   = json_encode($body);
        $body   = str_replace(":{", ":[{", $body);
        $body   = str_replace("}}", "}]}", $body);
        $body     = str_replace("}}", "}]}", $body);
        $body = str_replace("%20", " ", $body);
        //echo "<pre>111111 - "; var_dump($body); echo "</pre>";

        $url    = "https://app.omie.com.br/" . $path;
        $url    = str_replace("?JSON=", "", $url);
        $ch     = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $return["body"]   = json_decode($result, JSON_UNESCAPED_UNICODE);
        $return["httpCode"] = $info;
        $return["urlTeste"] = $url;
        $return["filter"]     = json_decode($body);
        curl_close($ch);

        return $return;
    }

    public function cria_pedido_post($path, $body = null, $params = array())
    {
        $body   = json_encode($body);
        $body = str_replace(":{", ":[{", $body);
        $body = str_replace("}}", "}]}", $body);
        $body = str_replace("}}", "}]}", $body);
        $body = str_replace("\/", "/", $body);
        $body = str_replace('},"det"', '}],"det"', $body);
        $body = str_replace('"simples_nacional":""}', '"simples_nacional":""}]', $body);
        $body = str_replace('},"ipi":', '}],"ipi":', $body);
        $body = str_replace('},"pis_padrao":', '}],"pis_padrao":', $body);
        $body = str_replace('},"icms_sn"', '}],"icms_sn"', $body);
        $body = str_replace('},"produto":', '}],"produto":', $body);
        $body = str_replace('}]},"frete"', '}]}],"frete"', $body);
        $body = str_replace('},"informacoes_adicionais"', '}],"informacoes_adicionais"', $body);
        $body = str_replace("%20", " ", $body);

        $url    = "https://app.omie.com.br/" . $path;
        $url    = str_replace("?JSON=", "", $url);
        $ch     = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $return["body"]   = json_decode($result, JSON_UNESCAPED_UNICODE);
        $return["httpCode"] = $info;
        $return["urlTeste"] = $url;
        $return["filter"]     = json_decode($body);
        curl_close($ch);

        return $return;
    }

    public function CriarPedido($body)
    {
        $body   = json_encode($body);
        $body = str_replace("\/", "/", $body);

        $url    = "https://app.omie.com.br" . $this->urls['CONSULTA_PRODUTO_PEDIDO_URL'];
        $ch     = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $return["body"]   = json_decode($result, JSON_UNESCAPED_UNICODE);
        $return["httpCode"] = $info;
        $return["urlTeste"] = $url;
        $return["filter"]     = json_decode($body);
        curl_close($ch);

        return $return;
    }
}
