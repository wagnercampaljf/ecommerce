<?php

namespace console\controllers\actions\omei;

use yii\helpers\Json;
use yii\httpclient\Client;

class Omie2 extends Client
{
    //const USER_EMAIL = 'sac@pecaagora.com';
    //const API_KEY = 'xHUdqQ92HLX-shDg55JT';
    const APP_KEY     = '531935801397';
    const APP_SECRET  = '1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh';
    
    //http://app.omie.com.br/api/v1/geral/contacorrente/?JSON={"call":"Pesq
    //uisarContaCorrente","app_key":"1560731700","app_secret":"226dcf372489b
    //b45ceede61bfd98f0f1","param":[{"pagina":1,"registros_por_pagina":100,"
    //apenas_importado_api":"N"}]} 
    
    //public $baseUrl = 'https://api.skyhub.com.br';
    public $baseUrl = 'http://app.omie.com.br';
    private $method = 'GET';
    private $url = '/';
    
    /**
     * @param $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
    
    /**
     * @return $this
     */
    public function products()
    {
        $this->url = 'products';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function orders()
    {
        $this->url = 'orders';
        return $this;
    }
    
    /**
     * @return $this
     */
    public function queues()
    {
        $this->url = 'queues/orders';
        return $this;
    }
    
    
    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        $this->setMethod('POST');
        return $this->doRequest($data);
    }
    
    /**
     * @param $data
     * @return mixed
     */
    public function aprove($code)
    {
        $this->setMethod('POST');
        $this->url .= "/" . $code . "/" . "approval";
        return $this->doRequest([
            "status" => "payment_received"
        ]);
    }
    
    /**
     * @param $data
     * @return mixed
     */
    public function delivery($code)
    {
        $this->setMethod('POST');
        $this->url .= "/" . $code . "/" . "delivery";
        return $this->doRequest([
            "status" => "complete"
        ]);
    }
    
    /**
     * @param $data
     * @return mixed
     */
    public function cancel($code)
    {
        $this->setMethod('POST');
        $this->url .= "/" . $code . "/" . "cancel";
        return $this->doRequest([
            "status" => "order_canceled"
        ]);
    }
    
    
    /**
     * @param $data
     * @return mixed
     */
    public function ship($code, $shipmentData = [])
    {
        $this->setMethod('POST');
        $this->url .= "/" . $code . "/" . "shipments";
        return $this->doRequest([
            "status" => "order_shipped",
            "shipment" => $shipmentData
        ]);
    }
    
    
    /**
     * @param $sku
     * @return mixed
     */
    public function disable($sku)
    {
        $this->setMethod('PUT');
        $this->url .= "/" . $sku;
        $data = [
            'product' => [
                'status' => 'disabled'
            ]
        ];
        
        return $this->doRequest($data);
    }
    
    /**
     * @param $sku
     * @return mixed
     */
    public function enable($sku)
    {
        $this->setMethod('PUT');
        $this->url .= "/" . $sku;
        $data = [
            'product' => [
                'status' => 'enabled'
            ]
        ];
        
        return $this->doRequest($data);
    }
    
    /**
     * @param String $code
     * @param array $data
     * @return mixed
     */
    public function update($code, $data)
    {
        $this->setMethod('PUT');
        $this->url .= "/" . $code;
        return $this->doRequest($data);
    }
    
    
    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function findAll($page = 1, $per_page = 10)
    {
        if ($page && $per_page) {
            //$this->url .= "?page=$page&per_page=$per_page";
            $this->url .= '/api/v1/geral/contacorrente/?pagina=1&registros_por_pagina=100';
        }
        
        $this->setMethod('GET');
        //print_r($this->doRequest());
        return Json::decode($this->doRequest()->getContent());
    }
    
    /**
     * @param $code
     * @return mixed
     */
    public function find($code)
    {
        $this->url .= '/' . $code;
        $this->setMethod('GET');
        return Json::decode($this->doRequest()->getContent());
    }
    
    /**
     * @param $id
     * @return mixed
     */
    public function remove($id)
    {
        $this->setMethod('DELETE');
        $this->url .= '/' . $id;
        return $this->doRequest();
    }
    
    /**
     * @param $data
     * @return mixed
     */
    public function doRequest($data = null)
    {
         $response = $this->createRequest()
        ->setFormat(self::FORMAT_JSON)
        ->setMethod($this->method)
        ->setUrl($this->url)
        ->addHeaders([
            //'X-User-Email' => self::USER_EMAIL,
            //'x-Api-Key' => self::API_KEY,
            'call' => 'PesquisarContaCorrente',
            'app_key' => '1560731700',
            'app_secret' => '226dcf372489bb45ceede61bfd98f0f1',
            'apenas_importado_api' => 'N',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
            ->setData($data)
            ->send();
        
        //http://app.omie.com.br/api/v1/geral/contacorrente/?JSON={"call":"Pesq
        //uisarContaCorrente","app_key":"1560731700","app_secret":"226dcf372489b
        //b45ceede61bfd98f0f1","param":[{"pagina":1,"registros_por_pagina":100,"
        //apenas_importado_api":"N"}]} 
        var_dump($response);
            
        return $response;
    }
    
}