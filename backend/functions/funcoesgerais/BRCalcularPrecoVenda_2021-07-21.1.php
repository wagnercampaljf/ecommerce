<?php

namespace backend\functions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\base\ErrorException;

class BRCalcularPrecoVenda extends Action
{
    public function run($parametros, $file_planilha){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();



        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


        $LinhasArray = Array();


        $parametros_array = json_decode($parametros,true  );

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

            try {

            $nome= $linhaArray[$parametros_array['coluna_nome']];

            $codigo_fabricante =$linhaArray[$parametros_array['coluna_codigo_fabricante']];

            $preco_compra   = (float) str_replace(",",".",$linhaArray[$parametros_array['coluna_preco_compra']]);

            $estoque = $linhaArray[$parametros_array['coluna_estoque']];

            $novo_codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']].".B";
            

            echo "\n".$i." - ".$novo_codigo_fabricante. " - ".$nome. " - ".$preco_compra;

            if ($i <= 1){

                fwrite($arquivo_destino, $codigo_fabricante.";".$nome.";".$preco_compra.";".$estoque.";Preço Venda;novo_codigo_fabricante\n");

                continue;
            }
            
            /*if ($i >= 50){
                die;
            }*/
         

            self::existe_produto_caixa($linhaArray, $arquivo_destino,$parametros );


            foreach ($faixas as $k => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }

                    echo " - ".$preco_venda." - ".$novo_codigo_fabricante;
                    fwrite($arquivo_destino, $codigo_fabricante.";".$nome.";".$preco_compra.";".$estoque.";".$preco_venda.";".$novo_codigo_fabricante."\n");

                    break;
                }
            }

            $retorno = ["status" => "Finalizado com sucesso!!!"];
        } catch (ErrorException $e) {
            $retorno = ["status" => "Erro: Informações Faltando"];
            Yii::warning($e);

            if($e){
                $retorno = ["status" => "Erro: Informações Faltando"];
                
            }
        
        }

        }
        $filial_nome='BR';
        $assunto = 'Planilha Presificação'.' - '.$filial_nome;

        $email_texto = 'Segue em anexo a Planilha de atualização de preço';
        if($arquivo_destino) {

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
    
        return $retorno;

        
        // Fecha o arquivo
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
    
    public static function existe_produto_caixa($linha, $arquivo_destino, $parametros){
        $parametros_array = json_decode($parametros,true  );
        $faixas = array();

        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }


        $produto = Produto::find()  ->andWhere(["=","codigo_fabricante","CX.".$linha[$parametros_array['coluna_codigo_fabricante']].".B"])
                                    ->andWhere(["=","fabricante_id",52])
                                    ->one();

        if($produto){

            $produto_sem_caixa  = Produto::find()   ->andWhere(["=","codigo_fabricante",$linha[$parametros_array['coluna_codigo_fabricante']].".B"])
                                                    ->andWhere(["=","fabricante_id",52])
                                                    ->one();


            if(!$produto_sem_caixa){
                $produto_novo                           = new Produto;
                $produto_novo->nome                     = str_replace("CAIXA ", "", $produto->nome);
                $produto_novo->descricao                = $produto->descricao;
                $produto_novo->peso                     = $produto->peso;
                $produto_novo->altura                   = $produto->altura;
                $produto_novo->profundidade             = $produto->profundidade;
                $produto_novo->largura                  = $produto->largura;
                $produto_novo->codigo_global            = str_replace("CX.","",$produto->codigo_global)."#";
                $produto_novo->codigo_montadora         = $produto->codigo_montadora;
                $produto_novo->codigo_fabricante        = str_replace("CX.","",$produto->codigo_fabricante);
                $produto_novo->fabricante_id            = $produto->fabricante_id;
                $produto_novo->slug                     = str_replace("kit-","",str_replace("caixa-","",$produto->slug));
                $produto_novo->micro_descricao          = $produto->micro_descricao;
                $produto_novo->subcategoria_id          = $produto->subcategoria_id;
                $produto_novo->aplicacao                = $produto->aplicacao;
                $produto_novo->texto_vetor              = $produto->texto_vetor;
                $produto_novo->codigo_similar           = $produto->codigo_similar;
                $produto_novo->produto_condicao_id      = $produto->produto_condicao_id;
                //$produto_novo->aplicacao_complementar   = $produto->aplicacao_complementar;
                $produto_novo->multiplicador            = 1;
                $produto_novo->video                    = $produto->video;
                $produto_novo->codigo_barras            = $produto->codigo_barras;
                $produto_novo->cest                     = $produto->cest;
                $produto_novo->ipi                      = $produto->ipi;

                //print_r($produto_novo);

                if($produto_novo->save()){

                    echo " - produto salvo";

                    $produto_filial             = new ProdutoFilial;
                    $produto_filial->produto_id = $produto_novo->id;
                    $produto_filial->filial_id  = 72;
                    $produto_filial->quantidade = 781;
                    $produto_filial->envio      = 1;


                    //print_r($produto_filial);

                    if($produto_filial->save()){
                        echo " - produto_filial salvo";
                    }
                    else{
                        echo " - produto_filial não salvo";
                    }
                }
                else{
                    echo " - produto não salvo";
                }
            }

            $preco_compra   = (float) str_replace(",",".",$linha[$parametros_array['coluna_preco_compra']]);


            $preco_compra   = $preco_compra * $produto->multiplicador;
            
            foreach ($faixas as $i => $faixa) {
                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                    $preco_venda = round(($preco_compra * $faixa[2]),2);
                    if ($faixa[3]){
                        $preco_venda = $faixa[2];
                    }
		    echo " - ".$preco_venda." - "."CX.".$linha[$parametros_array['coluna_codigo_fabricante']].".B";
                    fwrite($arquivo_destino, $linha[$parametros_array['coluna_codigo_fabricante']].";".$linha[$parametros_array['coluna_nome']].";".$linha[$parametros_array['coluna_preco_compra']].";".$linha[$parametros_array['coluna_estoque']].";".$preco_venda.";"."CX.".$linha[$parametros_array['coluna_codigo_fabricante']].".B\n");



                    break;
                }
            }
        }
    }

}



