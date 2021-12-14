<?php

namespace console\controllers\actions\mercadolivre;


use common\models\Filial;
use common\models\ProdutoFilial;
use Livepixel\MercadoLivre\Meli;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\MarcaProduto;
use common\models\Subcategoria;

class GerarRelatorioMorelateAction extends Action
{
    public function run()
    {
        echo "INICIO da função - FICHA TECNICA - "; $date = date('Y-m-d H:i'); echo $date;

        $nome_arquivo = "morelate_para_subir.csv";
        
        if (file_exists("/var/tmp/log_".$nome_arquivo)){
            unlink("/var/tmp/log_".$nome_arquivo);
        }
        $arquivo_log = fopen("/var/tmp/log_".$nome_arquivo, "a");
        fwrite($arquivo_log, date("Y-m-d H:i:s")."\n\n");

        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
        $response = ArrayHelper::getValue($user, 'body');
        print_r($response);

         if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
             $meliAccessToken = $response->access_token;

             $file = fopen("/var/tmp/".$nome_arquivo, 'r');
             
             fwrite($arquivo_log, "codigo_fabricante;codigo_similar;codigo_montadora;codigo_global;nome;marca;aplicacao;valor;quantidade;;;categoria_meli_id;categoria_meli_nome;subcategoria_id;e_mercado_envios;valor_frete;marca_produto_id");

             $x = 0;
             while (($linha = fgetcsv($file, null, ';')) !== false) {
                 
                 echo "\n". $x++. " - " . $linha[4];
                 
                 if($x<=2){
                     continue;
                 }
                 
                 fwrite($arquivo_log, "\n".'"'.$linha[0].'";"'.$linha[1].'";"'.$linha[2].'";"'.$linha[3].'";"'.$linha[4].'";"'.$linha[5].'";"'.$linha[6].'";"'.$linha[7].'";"'.$linha[8].'";"'.$linha[9].'";"'.$linha[10].'"');

                 $nome = str_replace(" ","%20",$linha[4]);
                 
                 $response = $meli->get("sites/MLB/domain_discovery/search?q=".$nome);
                 if ($response['httpCode'] >= 300) {
                     echo " - ERRO Categoria";
                     fwrite($arquivo_log, ";;;;");
                 } else {
                     echo " - OK Categoria";
                     
                     $categoria_meli_id      = ArrayHelper::getValue($response, 'body.0.category_id');
                     $categoria_meli_nome    = ArrayHelper::getValue($response, 'body.0.category_name');
                     fwrite($arquivo_log, ';"'.$categoria_meli_id.'";"'.$categoria_meli_nome.'"');
                     
                     $subcategoria = Subcategoria::find()->andWhere(["=","meli_id",$categoria_meli_id])->one();
                     if($subcategoria){
                         echo " - Subcategoria encontrada no PeçaAgora";
                         fwrite($arquivo_log, ";".$subcategoria->id);
                     }
                     else{
                         echo " - Subcategoria não encontrada no PeçaAgora";
                         fwrite($arquivo_log, ";");
                     }
                     
                     $response_categoria = $meli->get("categories/".$categoria_meli_id);
                     if ($response_categoria['httpCode'] >= 300) {
                         echo " - ERRO ME";
                         fwrite($arquivo_log, ";");
                     } else {
                         echo " - OK ME";
                         
                         $tipos_envio = ArrayHelper::getValue($response_categoria, 'body.settings.shipping_modes');
                         $me = "Sem ME";
                         foreach($tipos_envio as $tipo_envio){
                             if($tipo_envio=="me2"){
                                 $me = "Com ME";
                                 break;
                             }
                         }
                         fwrite($arquivo_log, ';"'.$me.'"');
                     }
                     
                     $response_categoria_dimensoes = $meli->get("categories/".$categoria_meli_id."/shipping");
                     if ($response_categoria_dimensoes['httpCode'] >= 300) {
                         echo " - ERRO Dimensoes";
                         fwrite($arquivo_log, ";");
                     } else {
                         echo " - OK Dimensoes";
                         
                         $response_categoria_frete = $meli->get("/users/193724256/shipping_options/free?dimensions=".ArrayHelper::getValue($response_categoria_dimensoes, 'body.height')."x".ArrayHelper::getValue($response_categoria_dimensoes, 'body.width')."x".ArrayHelper::getValue($response_categoria_dimensoes, 'body.length').",".ArrayHelper::getValue($response_categoria_dimensoes, 'body.weight'));
                         if ($response_categoria_frete['httpCode'] >= 300) {
                             echo " - ERRO Frete";
                             fwrite($arquivo_log, ";");
                         } else {
                             echo " - OK Frete";
                             fwrite($arquivo_log, ';"'.ArrayHelper::getValue($response_categoria_frete, 'body.coverage.all_country.list_cost').'"');
                         }
                     }
                 }
                 
                 $marca = $linha[3];
                 $marca_produto = MarcaProduto::find()->andWhere(["=","nome",$marca])->one();
                 if($marca_produto){
                     fwrite($arquivo_log, ";".$marca_produto->id);
                 }
                 //die;
             }
             fclose($file);
         }

    	fwrite($arquivo_log, "\n\n".date("Y-m-d H:i:s"));
    	fwrite($arquivo_log, "\nFim da rotina de atualização dos produtos no ML");
    	fclose($arquivo_log);

        echo "Fim da rotina de atualização dos produtos no ML";
    }
}
