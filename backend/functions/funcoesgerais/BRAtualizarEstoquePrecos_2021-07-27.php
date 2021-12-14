<?php

namespace backend\functions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use common\models\MarcaProduto;
use yii\base\ErrorException;

class BRAtualizarEstoquePrecos extends Action
{
    public function run($parametros, $file_planilha){


        
        try {
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        
        $LinhasArray = Array();

        $file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');


        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;


        }
        fclose($file);

        $log = "/var/www/html/backend/web/uploads/log_".$file_planilha;
        if (file_exists($log)){
            unlink($log);
        }

        $arquivo_log = fopen($log, "a");
        

        $parametros_array = json_decode($parametros,true  );

        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir


           

            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
            
            $preco_compra            = (float) str_replace(",",".",$linhaArray[$parametros_array['coluna_preco_compra']]);

            $preco_venda             = $linhaArray[$parametros_array['coluna_preco']];

            //$capas                   = $linhaArray[$parametros_array['coluna_capas']];

            $nome                    = $linhaArray[$parametros_array['coluna_nome']];

            $estoque                 = $linhaArray[$parametros_array['coluna_estoque']];

           // print_r($codigo_fabricante_array);die;

            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'.$preco_compra.'";"'.$linhaArray[2].'";"'.$preco_venda.'";"'.$nome.'";"'.$estoque.'";');

            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, "status");
                continue;
            }

            if($i<0){continue;}

            echo "\n".$i." - ".$codigo_fabricante_array." - ".$preco_compra." - ".$preco_venda." - ".$nome." - ".$estoque;
            
            //echo "\n".$i." - ".$linhaArray[14]." - ".$linhaArray[7]." - ".$linhaArray[15]." - ".$linhaArray[8]; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()  ->andWhere(['=','codigo_fabricante', $codigo_fabricante_array])
                                        ->andWhere(['=','fabricante_id', 52])
                                        ->one(); //Procura produto pelo código do fabricante
            
           


            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, 'Produto encontrado'); //Escreve no Log que encontrou o produto
                
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',72])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto
                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ' - Estoque encontrado'); //Escreve no log que encontrou o estoque


                    
		             $quantidade = $estoque;
                    // echo "\n\n";var_dump($quantidade); echo "\n\n";

		            if($quantidade == "0" && $preco_venda <= 100 ){
                        $quantidade = 0;
                        // $quantidade = 90;
                        //$preco_venda = $preco_venda * 2;


                    }
                    else if ($quantidade == "0" && $preco_venda >= 101){
                        $quantidade = 0;
                        //  $quantidade = 90;
                        //$preco_venda = $preco_venda * 1.3;


                    }else {
                        $quantidade = 781;
                    }
                    
                    if($preco_compra == "0.00"){
                        $quantidade = 0;
                    }


                    // Prdouto fora de linha  BR
                    if($codigo_fabricante_array== '004760.B' && $codigo_fabricante_array== '509371.B' && $codigo_fabricante_array=='509917.B'){
                        $quantidade = 0;

                    }



                    echo " - Quantidade: ".$quantidade;
                    $produtoFilial->quantidade = $quantidade;
                    if($produtoFilial->save()){
                        echo " - quantidade atualizada";
                        fwrite($arquivo_log, " - quantidade atualizada");
                    }
                    else{
                        echo " - quantidade não atualizada";
                        fwrite($arquivo_log, " - quantidade não atualizada");
                    }

                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();


                if($valor_produto_filial){
                    if ($preco_venda > $valor_produto_filial->valor * 3){
                        echo " - Preco mais alto que o normal";
                        fwrite($arquivo_log, ';Preço mais alto que o normal');
                        continue;

                    }elseif ($preco_venda < $valor_produto_filial->valor * 0.70){
                        echo " - Preco mais baixo que o normal";
                        fwrite($arquivo_log, ';Preço mais baixo que o normal');
                        continue;

                    }elseif ($preco_venda == $valor_produto_filial->valor){
                        echo " - mesmo valor";
                        fwrite($arquivo_log, ';mesmo valor');
                        continue;
                    }
                    else
                    {
                        echo " - Preco normal";

                    }
                }else{
                    echo 'Preço não encontrado';
                    fwrite($arquivo_log, ';Preço não encontrado');
                    continue;
                }



                    $valor_produto_filial = new ValorProdutoFilial;
                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                    $valor_produto_filial->valor                = $preco_venda;
                    $valor_produto_filial->valor_cnpj           = $preco_venda;
                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                    $valor_produto_filial->promocao             = false;
                    $valor_produto_filial->valor_compra         = $preco_compra;
                    if($valor_produto_filial->save()){
                        // print_r($valor_produto_filial);
                        echo " - Preço atualizado";
                        fwrite($arquivo_log, ' - Preço atualizado');
                    }
                    else{
                        //print_r($valor_produto_filial);
                        echo " - Preço não atualizado";
                        fwrite($arquivo_log, ' - Preço Não atualizado');
                    }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ' - Estoque Não encontrado');
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, 'Produto Não encontrado');
            }

        
        }

     

        fclose($arquivo_log); 

        $filial_nome='BR';
        $assunto = 'Planilha LOG Atualização de preço'.' - '.$filial_nome;

        $email_texto = 'Segue em anexo a Planilha de atualização de preço';

        if($log) {

        
                //$email =  Yii::$app->request->post('email');
                //$message = Yii::$app->request->post('message');
                //$file_attachment = UploadedFile::getInstance($model, 'file_planilha');
                if ( $file_planilha) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
                        ->setSubject($assunto)
                        ->setTextBody($email_texto);
                    //foreach ($parametros_array['file_planilha'] as $file) {
                        $filename = $log;
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
    } catch (ErrorException $e) {
        $retorno = ["status" => "Erro: Informações Faltando"];
        Yii::warning($e);

        if($e){
            $retorno = ["status" => "Erro: Informações Faltando"];
            
        }
    
    }
       
        return $retorno;
        
        echo "\n\nFIM da rotina de atualizacao do preço!";

    }
}








