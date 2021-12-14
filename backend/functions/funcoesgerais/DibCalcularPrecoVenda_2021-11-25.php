<?php

namespace backend\functions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\base\ErrorException;


class DibCalcularPrecoVenda extends Action
{
    public function run($parametros, $file_planilha)
    {

        echo "INÍCIO da rotina de criação preço: \n\n";
       

        $faixas = array();
        try {
            

            $markup_mestre      = MarkupMestre::find()->andWhere(['=', 'e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();
            
            $markups_detalhe = MarkupDetalhe::find()->andWhere(['=', 'markup_mestre_id', $markup_mestre->id])->all();

            $faixas = [];

            foreach ($markups_detalhe as $markup_detalhe) {
               

                $faixas[] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];
            }

            $LinhasArray = array();

            $parametros_array = json_decode($parametros, true);

            $arquivo_origem = '/var/www/html/backend/web/uploads/' . $file_planilha;

            $file = fopen($arquivo_origem, 'r');

            while (($line = fgetcsv($file, null, ';')) !== false) {
                $LinhasArray[] = $line;
            }
            fclose($file);

            $destino = '/var/tmp/' . "precificado_" . date('Y-M-d_H:i:s') . "_" . $file_planilha;
            $destino_erro = '/var/tmp/' . "precificado_com_erro" . date('Y-M-d_H:i:s') . "_" . $file_planilha;
            // if (file_exists($destino)){
            //     unlink($destino);
            // }

            $arquivo_destino = fopen($destino, "a");
            $arquivo_destino_erro = fopen($destino_erro, "a");

            

            foreach ($LinhasArray as $i => &$linhaArray) {               

            //    var_dump($linhaArray);
            //    die;  

                if (
                    !array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_nome'], $linhaArray)
                   // || !array_key_exists($parametros_array['coluna_capas'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_preco_compra'], $linhaArray)
                ) {

                   
                    if (array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)) {
                        

                        fwrite($arquivo_destino_erro, "\n" . '"' . $linhaArray[$parametros_array['coluna_codigo_fabricante']] . '";Linha com erros, verificar planilha');
                    } else {
                      
                        fwrite($arquivo_destino_erro, "\n" . '"' . $i . '";Linha com erros, verificar planilha"');
                    }

                    echo " - Linha com erros, verificar planilha";
                    continue;
                }
                $codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
               // var_dump($codigo_fabricante); 
                $nome = $linhaArray[$parametros_array['coluna_nome']];
               // var_dump($nome);    
                $preco_compra   = (float) str_replace(",", ".", str_replace(".", "", $linhaArray[$parametros_array['coluna_preco_compra']]));                
                //var_dump($preco_compra);            
                //$capas = $linhaArray[$parametros_array['coluna_capa']];                
                //var_dump($capas);
                //die;
                 if($i <1){
                    continue;
                }

                if ($i <= 0) {
                    fwrite($arquivo_destino,  $codigo_fabricante . ';' . $nome .  ';' .$linhaArray[$parametros_array['coluna_capa']]. ';' . $linhaArray[$parametros_array['coluna_estoque']] . ';' . $preco_compra . ";"."\n");
                    continue;
               }

               // echo $i." - ".$codigo_fabricante." \n";
                // //Acrescenta mais duas colunas
                // if ($i == 0 && $i <= 4){

                //     fwrite($arquivo_destino, "7;8");

                //     continue;

                // }

                // if ($i == 0 && $i <= 4){

                //     fwrite($arquivo_destino, "PRECO COMPRA;PRECO VENDA");

                //     continue;

                // }

                // Preco de compra

                //$preco_compra   = $linhaArray[3];



                $multiplicador     = 1;

                //$produto		= Produto::find()->andWhere(['=','codigo_global',$codigo_fabricante])->one();
                $produto        = Produto::find()->andWhere(['=', 'codigo_fabricante', 'D' . $codigo_fabricante])->one();              


                // if($produto == null){
                //    continue;
                // }
                if ($produto) {

                    // calculo do desconto
                    if ($produto->marca_produto_id == 1187) {
                        $preco_compra = 0.46 * $preco_compra;
                        continue;
                    }

                    $preco_compra += self::calcular_impostos($preco_compra, $produto->marca_produto_id, $produto->ipi);

                   // $preco_compra += self::calcular_impostos($preco_compra, $produto->marca_produto_id,$ipiplanilha);


                    if (!is_null($produto->multiplicador)) {

                        if ($produto->multiplicador > 1) {


                            $multiplicador = $produto->multiplicador;
                        }
                    }
                }
                // else{
                //     echo $i." - produto não encontrado";
                //     continue;
                // }

                $preco_compra = $multiplicador * $preco_compra;

                //echo "\n" . $i . "- " . "$codigo_fabricante" . " - " . $preco_compra;

                $preco_compra   = 0.45 * $preco_compra;

                //$preco_compra   = 0.45*$preco_compra;

                $preco_compra = number_format($preco_compra, 2, '.', '');
                echo $i." - ".$codigo_fabricante." - " . $preco_compra;

                //var_dump($preco_compra);
               // die;
                foreach ($faixas as $k => $faixa) {

                    if ($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]) {

                        $preco_venda = round(($preco_compra * $faixa[2]), 2);


                        if ($faixa[3]) {

                            $preco_venda = $faixa[2];
                        }
                        echo " - ".$preco_venda;
                        // fwrite($arquivo_destino, $preco_compra . ";" . $preco_venda);
                        fwrite($arquivo_destino, $codigo_fabricante . ';' . $nome . ';' . $linhaArray[$parametros_array['coluna_capa']] . ';' . $linhaArray[$parametros_array['coluna_estoque']] . ';' .$preco_compra . ';' . $preco_venda. ";"."\n");

                        break;
                    }
                    echo " TESTE 111";
                }
                //echo "TESTE 222";

                // Verifica se existe produto caixas
                $produto_caixa  = Produto::find()->andWhere(['=', 'codigo_fabricante', 'CX.D' . $linhaArray[$parametros_array['coluna_codigo_fabricante']]])->one();

                // if($produto_caixa == null){
                //     //echo " linha com problema \n";
                //      continue;
                //  }

                if ($produto_caixa) {

                    $preco_compra       = $produto_caixa->multiplicador * $preco_compra;

                    $codigo_fabricante  = 'CX.D' . $linhaArray[$parametros_array['coluna_codigo_fabricante']];

                    foreach ($faixas as $k => $faixa) {

                        if ($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]) {

                            $preco_venda = round(($preco_compra * $faixa[2]), 2);

                            if ($faixa[3]) {

                                $preco_venda = $faixa[2];
                            }

                            echo " - ".$preco_venda;
                             fwrite($arquivo_destino,  $codigo_fabricante . ';' . $nome . ';' . $linhaArray[$parametros_array['coluna_capa']]. ';' . $linhaArray[$parametros_array['coluna_estoque']] . ';' .$preco_compra . ';' . $preco_venda. ";"."\n");
                            //fwrite($arquivo_destino,$preco_compra. ";" . $preco_venda."\n");

                            break;
                        }
                    }
                }
            }
            


            $filial_nome = 'DIB';
            $assunto = 'Planilha Presificação' . ' - ' . $filial_nome;

            $email_texto = 'Segue em anexo a Planilha de atualização de preço';

            if ($arquivo_destino) {


                // Yii::$app->mailer->compose()
                // ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                // ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
                // ->setSubject($assunto)
                // ->setTextBody($email_texto) 
                // ->attach($arquivo_destino) 
                // ->attach($arquivo_destino_erro)                  
                // ->send();

                if ($file_planilha) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com", "contasareceber.pecaagora@gmail.com"])
                        ->setSubject($assunto)
                        ->setTextBody($email_texto);
                    //foreach ($parametros_array['file_planilha'] as $file) {
                    $filename = $destino;
                    // $filename2 = $arquivo_log_erro;
                    //$file->saveAs($filename);
                    $mail->attach($filename);
                    //$mail->attach($filename2);
                    //}
                    $mail->send();
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
            $retorno = ["status" => 'Erro Informações Faltando: ' . $e->getLine()];
            //Yii::warning($e);

            //print_r($e);
            if ($e) {
                switch ($e->getLine()) {
                    case 62:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Nome '];
                        break;
                    case 64:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Grupo (capas) '];
                        break;
                    case 66:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Codigo Fabricante '];
                        break;
                    case 68:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Preço Compra '];
                        break;
                    default:
                        $retorno = ["status" => 'Erro Informações Faltando: ' . $e->getMessage()];
                }
            }
        }




        // Fecha o arquivo
        fclose($arquivo_destino);
        fclose($arquivo_destino_erro);


        // echo "TESTE 222";
        echo "\n\nFIM da rotina de criação preço! \n";
        //var_dump($retorno);    
        return $retorno;
    }

    public static function calcular_impostos($preco_compra, $marca_produto_id, $ipi = 0)
    {

        $valor_ipi = $ipi > 0 ? $preco_compra * ($ipi / 100) : 0;

        echo " - IPI: " . $valor_ipi;

        $valor_st = $marca_produto_id == 1187 ? $preco_compra * 0.12 : 0;

        echo " - ST: " . $valor_st;

        $valor_imposto = $valor_ipi + $valor_st;

        echo " - Valor Imposto: " . $valor_imposto;

        return $valor_imposto;

        // $valor_ipi = 0;
        // if($ipi > 0){

        //     $valor_ipi = $preco_compra * ($ipi/100);

        // }

        // echo " - IPI: ".$valor_ipi;

        // $valor_st = 0;
        // // IMPOSTO SOBRE MARCAS
        // // MARCA RODIBEM(RDB NO BANCO)
        // if($marca_produto_id === 1187){
        //    // $valor_st = 0.12;
        //     $valor_st += 0.12 * $preco_compra;
        // }

        // echo " - ST: ".$valor_st;

        // $valor_imposto = $valor_ipi + $valor_st;

        // echo " - Valor Imposto: ".$valor_imposto;

        // return $valor_imposto;

    }
}
