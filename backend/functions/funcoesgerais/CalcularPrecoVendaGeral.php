<?php

namespace backend\functions\funcoesgerais;

use common\models\MarkupDetalhe;
use common\models\PlanilhaPreco;
use common\models\Processamento;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\web\UploadedFile;

class CalcularPrecoVendaGeral extends Action
{
    public function run($parametros, $file_planilha){
        //print_r($id);die;

        //$processamento = Processamento::find()->andWhere(['=', 'id', $id])->one();

        //$processamento = Processamento::find()->andWhere(['=', 'funcao_id', $id])->all();
        
       // print_r($processamento);die;

                echo  'rodar função ';

                //echo "INÍCIO da rotina de criação preço: \n\n";

                $faixas = array();

                $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

                $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();


                $faixas = [];

                foreach ($markups_detalhe as $markup_detalhe){
                    $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

                }


                $LinhasArray = Array();

                $parametros_array = json_decode($parametros,true  );

                //print_r($parametros_array['file_planilha']);die;

                //$parametros_file= json_decode($file_planilha, true);


                $arquivo_origem = '/var/www/html/backend/web/uploads/'. $file_planilha;
            
                

                $file = fopen($arquivo_origem, 'r');

                while (($line = fgetcsv($file,null,';')) !== false)
                {
                    $LinhasArray[] = $line;
                }
                fclose($file);

                $destino = '/var/www/html/backend/web/uploads/'."precificado_" .$file_planilha ;
                if (file_exists($destino)){
                    unlink($destino);
                }

               

                $arquivo_destino = fopen($destino, "a");

                foreach ($LinhasArray as $i => &$linhaArray){

                    //echo "\n".$i." - ".$linhaArray[3]. " - ".$linhaArray[6];

                    $novo_codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']];

                    fwrite($arquivo_destino, "\n".$novo_codigo_fabricante.";");

                    if ($i <= 0){

                        fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");

                        continue;

                    }


                    $preco_compra_planilha   = $linhaArray[$parametros_array['coluna_preco_compra']];

                    $preco_compra   = (float) str_replace(",",".",str_replace(".","",$preco_compra_planilha));

                    $multiplicador 	= 1;
                    $produto		= Produto::find()->andWhere(['=','codigo_fabricante','D'.$novo_codigo_fabricante])->one();

                    if($produto){
                        //$preco_compra += $this->calcular_impostos($preco_compra, $produto->marca_produto_id, $produto->ipi);


                        if(!is_null($produto->multiplicador)){

                            if($produto->multiplicador > 1 ){

                                $multiplicador = $produto->multiplicador;

                            }

                        }

                    }


                    $preco_compra = $multiplicador * $preco_compra;

                    echo "\n".$i." - ".$preco_compra;



                    echo " - ".$preco_compra;

                    $estoque = $linhaArray[$parametros_array['coluna_estoque']];

                    foreach ($faixas as $k => $faixa) {

                        if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){

                            $preco_venda = round(($preco_compra * $faixa[2]),2);


                            if ($faixa[3]){

                                $preco_venda = $faixa[2];

                            }

                            fwrite($arquivo_destino, $preco_compra.";".$preco_venda.";".$estoque);

                            break;

                        }

                    }

                    // Verifica se existe produto caixas
                    $produto_caixa  = Produto::find()->andWhere(['=','codigo_fabricante','CX.D'.$novo_codigo_fabricante])->one();

                    if($produto_caixa){

                        $preco_compra       = $produto_caixa->multiplicador * $preco_compra;

                        $codigo_fabricante  = 'CX.D'.$novo_codigo_fabricante;

                        foreach ($faixas as $k => $faixa) {

                            if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){

                                $preco_venda = round(($preco_compra * $faixa[2]),2);

                                if ($faixa[3]){
                                    $preco_venda = $faixa[2];
                                }
                                fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$preco_compra.";".$preco_venda.";".$estoque);
                                break;

                            }

                        }

                    }

                }


                $filial_nome='';
                $assunto = 'Planilha Atualização de preço'.' - '.$filial_nome;

                $email_texto = 'Segue em anexo a Planilha de atualização de preço';

                if($arquivo_destino) {

                
                        //$email =  Yii::$app->request->post('email');
                        //$message = Yii::$app->request->post('message');
                        //$file_attachment = UploadedFile::getInstance($model, 'file_planilha');
                        if ( $file_planilha) {
                            $mail = Yii::$app->mailer->compose()
                                ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                                ->setTo(["dev2.pecaagora@gmail.com"])
                                ->setSubject($assunto)
                                ->setTextBody($email_texto);
                            //foreach ($parametros_array['file_planilha'] as $file) {
                                $filename = $destino;
                                //$file->saveAs($filename);
                                $mail->attach($filename);
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
                return $retorno;
            }
           
    public function calcular_impostos($preco_compra, $marca_produto_id, $ipi = 0){

        $valor_ipi = 0;

        if($ipi > 0){

            $valor_ipi = $preco_compra * ($ipi/100);

        }

        echo " - IPI: ".$valor_ipi;

        $valor_st = 0;



        echo " - ST: ".$valor_st;

        $valor_imposto = $valor_ipi + $valor_st;

        echo " - Valor Imposto: ".$valor_imposto;

        return $valor_imposto;
}



}


// ALTER TABLE public.pedido ADD COLUMN plp_id integer;


