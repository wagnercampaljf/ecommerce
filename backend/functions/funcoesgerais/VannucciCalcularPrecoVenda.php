<?php

namespace backend\functions\funcoesgerais;


use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\base\ErrorException;

class VannucciCalcularPrecoVenda extends Action
{
    public function run($parametros, $file_planilha){
        try {
        echo "INÍCIO da rotina de criação preço: \n\n";

        $faixas_compra = array();
        $faixas_compra = [
	        "9959"  => 0.376,
            "91"    => 0.376,
            "1279"  => 0.376,
            "2550"  => 0.376,
            "372"   => 0.376,
            "140"   => 0.376,
            "2220"  => 0.376,
            "644"   => 0.376,
            "729"   => 0.376,
            "726"   => 0.376,
            "587"   => 0.376,
            "2202"  => 0.376,
            "49"    => 0.376,
            "501"   => 0.376,
            "600"   => 0.376,
            "4486"  => 0.376,
            "2010"  => 0.376,
            "579"   => 0.376,
            "40"    => 0.376,
            "2017"  => 0.376,
            "9945"  => 0.376,
            "850"   => 0.376,
            "500"   => 0.376,
            "2144"  => 0.376,
            "126"   => 0.376,
            "2018"  => 0.376,
            "2069"  => 0.376,
            "2209"  => 0.376,
            "1154"  => 0.376,
            "1000"  => 0.376,
            "1196"  => 0.376,
            "9956"  => 0.376,
            "1050"  => 0.376,
            "421"   => 0.376,
            "475"   => 0.376,
            "440"   => 0.376,
            "1227"  => 0.376,
            "2563"  => 0.376,
            "847"   => 0.376,
            "9937"  => 0.376,
            "494"   => 0.376,
            "1035"  => 0.376,
            "1245"  => 0.376,
            "731"   => 0.376,
            "757"   => 0.376,
            "695"   => 0.376,
            "1238"  => 0.376,
            "2000"  => 0.376,
            "511"   => 0.376,
            "660"   => 0.376,
            "98"    => 0.376,
            "199"   => 0.376,
            "502"   => 0.376,
            "367"   => 0.376,
            "133"   => 0.376,
            "1272"  => 0.376,
            "285"   => 0.376,
            "2523"  => 0.376,
            "2522"  => 0.376,
            "9955"  => 0.376,
            "503"   => 0.376,
            "275"   => 0.376,
            "881"   => 0.376,
            "248"   => 0.376,
            "800"   => 0.376,
            "256"   => 0.376,
            "396"   => 0.376,
            "238"   => 0.376,
            "455"   => 0.376,
            "869"   => 0.376,
            "754"   => 0.376,
            "460"   => 0.376,
            "338"   => 0.376,
            "687"   => 0.376,
            "1305"  => 0.376,
            "914"   => 0.376,
            "828"   => 0.376,
            "482"   => 0.376,
            "423"   => 0.376,
            "159"   => 0.376,
            "1253"  => 0.376,
            "215"   => 0.376,
            "2200"  => 0.376,
            "904"   => 0.376,
            "110"   => 0.376,
            "553"   => 0.376,
            "265"   => 0.376,
            "1271"  => 0.376,
            "2171"  => 0.17,
            "2101"  => 0.17,
            "902"   => 0.17,
            "910"   => 0.17,
            "2300"  => 0.17,
            "707"   => 0.17,
            "718"   => 0.17,
	        "119"   => 0.17,
            "2221"  => 0.17,
	        "2245"  => 0.17,
        ];
        
     
        
        $faixas_venda = array();

        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas_venda = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas_venda [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }
        $LinhasArray = Array();

      
        $parametros_array = json_decode($parametros,true  );

         $arquivo_origem = '/var/www/html/backend/web/uploads/'. $file_planilha;
         // $arquivo_origem = '/var/tmp/'. $file_planilha;
    
        $file = fopen($arquivo_origem, 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        // var_dump($LinhasArray);
        //  die;
        fclose($file);
        
        $destino = '/var/tmp/'."precificado_".date('Y-M-d_H:i:s')."_".$file_planilha;
        $destino_erro = '/var/tmp/'."itens_problemas_".date('Y-M-d_H:i:s')."_".$file_planilha;

        // var_dump($destino);
        //  die;
        
        // if (file_exists($destino)){
        //    unlink($destino);
        // }
        $arquivo_destino = "";
        $arquivo_destino   = fopen($destino, "a");
         
        $arquivo_destino_2 = fopen($destino_erro, "a");

        // var_dump($arquivo_destino);
        // die;
        
        foreach ($LinhasArray as $i => &$linhaArray){
           
            // if($i <= 1206){
            //     continue;
            // }

            if(!array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)
              || (!array_key_exists($parametros_array['coluna_nome'], $linhaArray))
              || !array_key_exists($parametros_array['coluna_preco_compra'], $linhaArray) ){
		
               	if(array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)){
                    fwrite($arquivo_destino_2, "\n".'"'.$linhaArray[$parametros_array['coluna_codigo_fabricante']].'";Linha com erros, verificar planilha');    
		}
		else{
		    fwrite($arquivo_destino_2, "\n".'"'.$i.'";Linha com erros, verificar planilha"');
		}
		
                echo " - Linha com erros, verificar planilha";
                continue;
            }

	    
            
            $nome= $linhaArray[$parametros_array['coluna_nome']];           
            
            //$codigo_fabricante_completo = str_replace('??','',utf8_decode($linhaArray[$parametros_array['coluna_codigo_fabricante']]));

             $codigo_fabricante_completo = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
             $codigo_fabricante_completo = preg_replace('/[^a-zA-Z0-9]/i', '', $codigo_fabricante_completo);
             //$codigo_fabricante_completo = utf8_decode(preg_replace('/^[^a-zA-Z0-9]+$/','', $codigo_fabricante_completo));
             //echo $codigo_fabricante_completo. "\n"; die;
            //$codigo_fabricante_completo = preg_replace('/[\x00-\x1F\x7F]/u', '', $linhaArray[$parametros_array['coluna_codigo_fabricante']]);
            //echo preg_replace('/[^a-zA-Z0-9]/i', '', $codigo_fabricante_completo);
            //die;
            //echo str_replace('??','',utf8_decode($linhaArray[$parametros_array['coluna_codigo_fabricante']]));
        //     echo $codigo_fabricante_completo;
        //    die;

            $preco_compra   = (float) str_replace(",",".",$linhaArray[$parametros_array['coluna_preco_compra']]);
            
            echo "\n".$i." - ".$codigo_fabricante_completo." - ";
            
            if ($i <= 0){
                fwrite($arquivo_destino, $codigo_fabricante_completo.";".$nome.";".$preco_compra."\n");
                continue;
            }
            
            // var_dump($arquivo_destino);
            // die;
            
            $codigo_fabricante  = explode("-",$codigo_fabricante_completo);
            
            $desconto = 1-0.3760;
            
            if (array_key_exists(1,$codigo_fabricante)){
                echo $codigo_fabricante[1];

                $sufixo             = $codigo_fabricante[1];
                $sufixo             = str_replace("*1","",$sufixo);
                $sufixo             = str_replace("*2","",$sufixo);
                $sufixo             = str_replace("*3","",$sufixo);
                $sufixo             = str_replace("*4","",$sufixo);
                $sufixo             = str_replace("*5","",$sufixo);
                $sufixo             = str_replace("*6","",$sufixo);
                $sufixo             = str_replace("*7","",$sufixo);
                $sufixo             = str_replace("*8","",$sufixo);
                $sufixo             = str_replace("*9","",$sufixo);
                
                if (array_key_exists($sufixo,$faixas_compra)){
                    $desconto = 1-$faixas_compra[$sufixo];
                }
            }
            
            echo " - ".$desconto;
            
            self::existe_produto_caixa($linhaArray, $arquivo_destino, $faixas_compra, $faixas_venda,$parametros);
            
            //$preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[5]));

            $preco_compra   = (float) str_replace(",",".",$linhaArray[$parametros_array['coluna_preco_compra']]);

            echo " - ".$preco_compra;
            $preco_compra   = $preco_compra * $desconto;



           $codigo = explode("-", $linhaArray[$parametros_array['coluna_codigo_fabricante']]);
            $codigo_limpo= $codigo[count($codigo)-1];

            if ($codigo_limpo =='2171' ||$codigo_limpo =='2101' || $codigo_limpo =='902' ){

                $preco_compra= $preco_compra * 1.28;

            }


            
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas_venda as $k => $faixa_venda) {
              

                if($preco_compra >= $faixa_venda[0] && $preco_compra <= $faixa_venda[1]){

                    $preco_venda = round(($preco_compra * $faixa_venda[2]),2);

                    if ($faixa_venda[3]){

                        $preco_venda = $faixa_venda[2];

                    }

                    break;

                }
            }
            
            fwrite($arquivo_destino, $codigo_fabricante_completo.";".$nome.";".$preco_compra.";".$preco_venda.";".$codigo_fabricante_completo."\n");
        }

    

        $filial_nome='Vannucci';
        $assunto = 'Planilha de precificação Vannucci'.' - '.$filial_nome;
        
        $email_texto = 'Segue em anexo a Planilha de calculo de preço de venda';
       // if($arquivo_destino) {
        if($destino) {

           
            
                    // Yii::$app->mailer->compose()
                    // ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    // ->setTo(["dev2.pecaagora@gmail.com"])
                    // ->setSubject($assunto)
                    // ->setTextBody($email_texto) 
                    // ->attach($arquivo_destino_2)  
                    // ->attach($arquivo_destino)                 
                    // ->send();

                    if ( $file_planilha) {
                        $mail = \Yii::$app->mailer->compose()
                            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                            ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
                            ->setSubject($assunto)
                            ->setTextBody($email_texto);
                        //foreach ($parametros_array['file_planilha'] as $file) {
                            $filename = $destino;
                            //$filename = $arquivo_destino;
                           // $filename2 = $arquivo_log_erro;
                            //$file->saveAs($filename);
                              $mail->attach($filename);                              
                              $mail->send();
                            //$mail->attach($filename2);
                        //}
                        
                    } else {
                        $mail = Yii::$app->mailer->compose()
                            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                            ->setTo(["dev2.pecaagora@gmail.com"])
                            ->setSubject('Erro não foi gerada a planilha')
                            ->setTextBody($email_texto)
                            ->send();
                  }
         
        }

        $retorno = ["status" => "Finalizado com sucesso!!!"];
    } catch (ErrorException $e) {
        $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
        Yii::warning($e);

        if($e){
            $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
            
        }
    
    }
        
        // Fecha o arquivo
        fclose($arquivo_destino);
        fclose($arquivo_destino_2);
        //fclose($arquivo_destino_2);

        
        echo "\n\nFIM da rotina de criação preço!";
        return $retorno;
    }
    
    
    public static function existe_produto_caixa($linha, $arquivo_destino, $faixas_compra, $faixas_venda, $parametros){
        
        $parametros_array = json_decode($parametros,true  );


        $nome= $linha[$parametros_array['coluna_nome']];
            
            $codigo_fabricante_completo = preg_replace('/[^a-zA-Z0-9]/i', '', $linha[$parametros_array['coluna_codigo_fabricante']]);
            
            $preco_compra   = (float) str_replace(",",".",$linha[$parametros_array['coluna_preco_compra']]);

        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$codigo_fabricante_completo])
                                    ->andWhere(["=","fabricante_id",91])
                                    ->one();
        
        if($produto){
                        
            $codigo_fabricante  = explode("-",$codigo_fabricante_completo);
            
            $desconto = 1-0.3760;
            
            if (array_key_exists(1,$codigo_fabricante)){
                echo $codigo_fabricante[1];
                
                if (array_key_exists($codigo_fabricante[1],$faixas_compra)){
                    $desconto = 1-$faixas_compra[$codigo_fabricante[1]];
                }
            }
            
            echo " - desconto: ".$desconto;
            //$preco_compra   = (float) str_replace(",",".",str_replace(".","",$linha[5]));
            echo " - ".$preco_compra;
            $preco_compra   = $preco_compra * $desconto * $produto->multiplicador;
 
            //$preco_compra = $preco_compra * 0.65;
            foreach ($faixas_venda as $k => $faixa_venda) {
                if($preco_compra >= $faixa_venda[0] && $preco_compra <= $faixa_venda[1]){
                    $preco_venda = round(($preco_compra * $faixa_venda[2]),2);
                    if ($faixa_venda[3]){
                        $preco_venda = $faixa_venda[2];
                    }
                    fwrite($arquivo_destino, $codigo_fabricante_completo.";".$nome.";".$preco_compra.";".$preco_venda.";CX.".$codigo_fabricante_completo."\n");
                    break;
                }
            }
        }
    }
}
