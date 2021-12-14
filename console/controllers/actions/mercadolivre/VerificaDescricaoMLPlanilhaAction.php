<?php

namespace console\controllers\actions\mercadolivre;

use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use Livepixel\MercadoLivre\Meli;
use common\models\ProdutoFilial;

class VerificaDescricaoMLPlanilhaAction extends Action
{
    public function run($cliente = 1){
       
        echo "INÍCIO\n\n";
        
        $arquivo_log = fopen("/var/tmp/log_produtos_descricao_ml_2020-04-17_tratar_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "meli_id;status_antigo;status;status_alteracao_atual\n");
        
        $meli = new Meli(static::APP_ID, static::SECRET_KEY);
        $user = $meli->refreshAccessToken('TG-5e2efe08144ef6000642cdb6-193724256');
        $response = ArrayHelper::getValue($user, 'body');

        if (is_object($response) && ArrayHelper::getValue($user, 'httpCode') < 400) {
            $meliAccessToken = $response->access_token;
            
            $LinhasArray = Array();
            $file = fopen('/var/tmp/produtos_descricao_ml_2020-04-17_tratar.csv', 'r');
            while (($line = fgetcsv($file,null,';')) !== false)
            {
                $LinhasArray[] = $line;
            }
            fclose($file);
            
            foreach ($LinhasArray as $k => &$linhaArray ){
                
                if ($k <= 0){
                    continue;
                }
                
                echo "\n".$k." - ".$linhaArray[0];
                fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";");
                
                $response_item = $meli->get("/items/".$linhaArray[0]."?access_token=".$meliAccessToken);
		if(ArrayHelper::getValue($response_item, 'body.status') != "active"){
			echo " - produto não ativo";
			continue;
		}
                
                $produto_filial = ProdutoFilial::find()->andWhere(['=','meli_id',$linhaArray[0]])->one();
                
                $page = $this->controller->renderFile(__DIR__ . '/../../../../lojista/views/mercado-livre/produtoML.php',['produto' => $produto_filial]);
                $page = str_replace("'", "", $page);
                $page = str_replace("<p>", " ", $page);
                $page = str_replace("</p>", " ", $page);
                $page = str_replace("<br>", "\n", $page);
                $page = str_replace("<BR>", "\n", $page);
                $page = str_replace("<br/>", "\n", $page);
                $page = str_replace("<BR/>", "\n", $page);
                $page = str_replace("<strong>", " ", $page);
                $page = str_replace("</strong>", " ", $page);
                $page = str_replace('<span class="redactor-invisible-space">', " ", $page);
                $page = str_replace('</span>', " ", $page);
                $page = str_replace('<span>', " ", $page);
                $page = str_replace('<ul>', " ", $page);
                $page = str_replace('</ul>', " ", $page);
                $page = str_replace('<li>', "\n", $page);
                $page = str_replace('</li>', " ", $page);
                $page = str_replace('<p style="margin-left: 20px;">', " ", $page);
                $page = str_replace('<h1>', " ", $page);
                $page = str_replace('</h1>', " ", $page);
                $page = str_replace('<h2>', " ", $page);
                $page = str_replace('</h2>', " ", $page);
                $page = str_replace('<h3>', " ", $page);
                $page = str_replace('</h3>', " ", $page);
                $page = str_replace('<span class="redactor-invisible-space" style="">', " ", $page);
                $page = str_replace('>>>', "(", $page);
                $page = str_replace('<<<', ")", $page);
                $page = str_replace('<u>', " ", $page);
                $page = str_replace('</u>', "\n", $page);
                $page = str_replace('<b>', " ", $page);
                $page = str_replace('</b>', " ", $page);
                $page = str_replace('<o:p>', " ", $page);
                $page = str_replace('</o:p>', " ", $page);
                $page = str_replace('<p style="margin-left: 40px;">', " ", $page);
                $page = str_replace('<del>', " ", $page);
                $page = str_replace('</del>', " ", $page);
                $page = str_replace('/', "-", $page);
                $page = str_replace('<em>', " ", $page);
                $page = str_replace('<-em>', " ", $page);
                
                //$body = ["description" => ["plain_text" => $page],];
		$body = ["plain_text" => $page];
		//print_r($body);
                $response = $meli->put("items/{$linhaArray[0]}/description?access_token=" . $meliAccessToken, $body, [] );
		//$response = $meli->put("items/{$linhaArray[0]}?access_token=" . $meliAccessToken, $body, [] );
		print_r($response);
		die;
                if ($response['httpCode'] >= 300) {
                    echo " - ERROR Descrição";
                    print_r($response); 
                    fwrite($arquivo_log, "Descrição não alterado");
                }
                else{
                    echo " - OK Descrição";
                    fwrite($arquivo_log, "Descrição alterada");
                }
            }
        }
        
        echo "\n\nFIM!\n\n";
    }
}
