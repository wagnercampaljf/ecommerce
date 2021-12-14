<?php

namespace backend\functions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;

class DibAtualizarPrecosEstoque extends Action
{
    public function run($parametros, $file_planilha){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();



        $file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');

        while (($line = fgetcsv($file,null,';')) !== false)

        {

            $LinhasArray[] = $line;

        }

        fclose($file);

        // Vai logando
        $log = "/var/www/html/backend/web/uploads/log_".$file_planilha;

        if (file_exists($log)){

            unlink($log);

        }

        $arquivo_log = fopen($log, "a");

        $parametros_array = json_decode($parametros,true  );

        // Percorre as linhas
        foreach ($LinhasArray as $i => &$linhaArray){

            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
            
            $preco_compra            = $linhaArray[$parametros_array['coluna_preco_compra']];

            $preco_venda             = $linhaArray[$parametros_array['coluna_preco']];

            $capas                   = $linhaArray[$parametros_array['coluna_capas']];

            $nome                    = $linhaArray[$parametros_array['coluna_nome']];

            $estoque                 = $linhaArray[$parametros_array['coluna_estoque']];



            echo "\n".$i." - ".$codigo_fabricante_array." - ".$preco_compra." - ".$preco_venda." - ".$nome." - ".$estoque;


            fwrite($arquivo_log, "\n".$codigo_fabricante_array.';'.$preco_compra.';'.$preco_venda.';'.$capas.';'.$nome.';'.$estoque);


            // Pula uma linha
            if ($i <= 0){

                fwrite($arquivo_log, ";STATUS");

                continue;

            }

            $codigo_fabricante = (!(strpos($codigo_fabricante_array,"CX.") === false)) ? $codigo_fabricante_array : 'D'.$codigo_fabricante_array;
            
            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', $codigo_fabricante])

                                        //->andWhere(['not like','codigo_fabricante', 'CX.D'.$linhaArray[0]])

                                        ->one();

            // Procura o produto
            if ($produto){
                
                echo " - Produto encontrado";

                fwrite($arquivo_log, ";Produto encontrado;");

                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',97])

                                                        ->andWhere(['=', 'produto_id', $produto->id])

                                                        ->one();
                if ($produtoFilial) {
                    
                    echo " - ".$produtoFilial->id;

                    fwrite($arquivo_log, ";Estoque encontrado");

                    $quantidade = $estoque;

                    if ($preco_compra== 0){

                        echo " - Preco Compra zerado";

                        $quantidade = 0;

                    }

                    if($capas == "239-CAPAS CONFECCAO CHINIL DIB" || $capas == "352-CAPAS CONFECCAO PELUCIA DIB" || $capas == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $capas == "587-CAPAS CONFECCAO CORINO"){
                        $quantidade = 991;
                    }

                    //$nome = $linhaArray[1];

                    if ((!(strpos($nome,"CAPA PORCA") === false)) && (strpos($linhaArray[0],"CX.") === false)){

                        $quantidade = 0;

                        echo " - CAPA";

                    }


                    $produtoFilial->quantidade  = $quantidade;

                    if ($produtoFilial->save()){

                        echo " - Estoque alterado";

                        fwrite($arquivo_log, ";Estoque alterado");

                    }

                    else{

                        echo " - Estoque não alterado";

                        fwrite($arquivo_log, ";Estoque não alterado");

                    }

                    $preco_venda =$linhaArray[$parametros_array['coluna_preco']];

                    /*//Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                    if($preco_venda == $valor_produto_filial->valor){

                        echo " - mesmo valor";

                        continue;

                    }*/

                    // Verifica se o valor a ser atualizado e maior que 300%, menor que 30% ou igual ao valor anterior

                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

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
                    else {

                        echo " - Preco normal";

                    }

                    $valor_produto_filial = new ValorProdutoFilial;

                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;

                    $valor_produto_filial->valor                = $preco_venda;

                    $valor_produto_filial->valor_cnpj           = $preco_venda;

                    $valor_produto_filial->valor_compra         = $preco_compra;

                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");

                    $valor_produto_filial->promocao             = false;

                    if($valor_produto_filial->save()){

                        echo " - Preço criado";

                        fwrite($arquivo_log, ";Preço criado");

                    }

                    else{

                        echo  " - Preço não criado";

                        fwrite($arquivo_log, ";Preço não criado");

                    }

                }

                else{

                    echo " - Estoque não encontrado";

                    fwrite($arquivo_log, ";Estoque não encontrado");

                }

            }

            else{

                echo " - Produto não encontrado";

                fwrite($arquivo_log, ";Produto não encontrado");

            }

        }
        //SEGUNDA ANÁLISE
        /*$produtos_filial_br = Produto::find()  ->andWhere(['=','codigo_global', $linhaArray[0]])->all();*/ //Procura produto pelo código "

        //$produtos_filial_br= Produto::find()->andWhere(['=', 'fabricante_id',52])->all();

        // $produtos_filial_br = ProdutoFilial::find()->andWhere(['=','filial_id',72])->all();

        /*$produtos_filial_br  fwrite($arquivo_log, "\n\n\n".'"Codigo_global";"codigo_fabricante";"quantidade";"status"');

          foreach($produtos_filial_br as $k => $produto_filial_br){

              echo "\n".$k." - ".$produto_filial_br->produto->codigo_fabricante;

              //print_r($codigos_produtos); die;

              $produto_encontrado = false;

              if(array_key_exists($produto_filial_br->produto->codigo_fabricante, $codigos_produtos)){

                  $produto_encontrado = true;

              }

              if(!$produto_encontrado){

                  echo " - produto não encontrado na planilha";

                  $quantidade = $produto_filial_br->quantidade;

                  $produto_filial_br->quantidade = 0;

                  if($produto_filial_br->save()){

                      fwrite($arquivo_log, "\n".'"'.$produto_filial_br->produto->codigo_global.'";"'.$produto_filial_br->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade zerada"');

                  }

                  else{

                      fwrite($arquivo_log, "\n".'"'.$produto_filial_br->produto->codigo_global.'";"'.$produto_filial_br->produto->codigo_fabricante.'";"'.$quantidade.'";"Quantidade não zerada"');

                  }

              }

          }*/

        
       
       
       
          fclose($arquivo_log);
        
        echo "\n\nFIM da rotina de atualizacao do preço!";

                $filial_nome='DIB';
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
                return $retorno;



    }



}







