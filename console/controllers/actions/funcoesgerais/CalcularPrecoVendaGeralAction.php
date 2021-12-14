<?php

namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\web\UploadedFile;

class CalcularPrecoVendaGeralAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();



        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


        $LinhasArray = Array();

        //$arquivo_origem = '/var/tmp/br_preco_estoque_25-02-2021';

        //$arquivo_origem = '/var/tmp/forrtos_papelao_2021-05-21';

        $arquivo_origem = '/var/tmp/produtos_mastra_2021-07-09';



        $file = fopen($arquivo_origem.".csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        $destino = $arquivo_origem."_precificado.csv";
        if (file_exists($destino)){
            unlink($destino);
        }
        
        $arquivo_destino = fopen($destino, "a");

        foreach ($LinhasArray as $i => &$linhaArray){
            
            echo "\n".$i." - ".$linhaArray[1]. " - ".$linhaArray[6];
            
            $novo_codigo_fabricante = $linhaArray[1];
            
            if ($i <= 1){

                fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";Preço Venda;novo_codigo_fabricante\n");

                continue;
            }
            
            /*if ($i >= 50){
                die;
            }*/
         
            //print_r($linhaArray);
            $preco_compra   = (float) $linhaArray[3];


            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }

                    echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
                    fwrite($arquivo_destino, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$preco_venda.";".$novo_codigo_fabricante."\n");
                    break;
                }
            }
        }

        $filial_nome=' Pea venda casada';
        $assunto = 'Planilha Atualização de preço'.' - '.$filial_nome;

        $email_texto = 'Segue em anexo a Planilha de atualização de preço';

        if($arquivo_destino) {

            //if (Yii::$app->request->isPost) {
                //$email =  Yii::$app->request->post('email');
                //$message = Yii::$app->request->post('message');
                //$file_attachment = UploadedFile::getInstance($model, 'file_planilha');
                //$model->file_planilha = UploadedFile::getInstance($model, 'file_planilha');
                if ($arquivo_destino) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com"])
                        ->setSubject($assunto)
                        ->setTextBody($email_texto);
                    //foreach ($model->file_planilha as $file) {
                        $filename = $destino = $arquivo_origem."_precificado.csv";;
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
           // }
        }
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }

}



