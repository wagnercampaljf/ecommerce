<?php

namespace backend\functions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\base\ErrorException;

class VannucciAtualizarPrecos extends Action
{
    public function run($parametros, $file_planilha){
        try {
       
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();
       

        $file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        $log = "/var/tmp/log_".$file_planilha;
        $log2 = "/var/tmp/log_produto_erro_".$file_planilha;
        if (file_exists($log)){
            unlink($log);
        }

        $parametros_array = json_decode($parametros,true  );
        //Abre o arquivo de log pra ir logando a função
        $arquivo_log = fopen($log, "a"); 
        $arquivo_log_erro = fopen($log2, "a");        
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir
              
            // echo "<prev>";
            // print_r($linhaArray);
            // echo "</prev>";
            // die;
            
          if(!array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)
          || !array_key_exists($parametros_array['coluna_nome'], $linhaArray)
          //|| !array_key_exists($parametros_array['coluna_estoque'], $linhaArray)
          || !array_key_exists($parametros_array['coluna_preco'], $linhaArray) 
           ){
    
               if(array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)){
                fwrite($arquivo_log_erro, "\n".'"'.$linhaArray[$parametros_array['coluna_codigo_fabricante']].'";Linha com erros, verificar planilha');    
          }
           else{
                fwrite($arquivo_log_erro, "\n".'"'.$i.'";Linha com erros, verificar planilha"');
         }
    
            echo " - Linha com erros, verificar planilha";
            continue;
        }
            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];            

            $preco_venda             = $linhaArray[$parametros_array['coluna_preco']];

            $preco_compra = round($linhaArray[$parametros_array['coluna_preco_compra']], 2);

            $nome                    = $linhaArray[$parametros_array['coluna_nome']];

            
            
            if ($i >= 0){

            //Coloca os dados da planilha de preços no log
            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'. $nome.'";"'.$preco_compra .'";"'.$preco_venda);
            
           // echo " TESTO 789";
            echo "\n".$i." - ".$codigo_fabricante_array." - ".$preco_compra." - ".$preco_venda; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            //var_dump($codigo_fabricante_array);
             //die();     

            $produto = Produto::find()->andWhere(['=','codigo_fabricante', $codigo_fabricante_array])->one(); //Procura produto pelo código do fabricante "VA"
            
               

            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto
                
                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',38])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto na loja Vannucci
                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ';Estoque encontrado'); //Escreve no log que encontrou o estoque


     



                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                    if($valor_produto_filial){

                        if($produtoFilial->e_atualizar_preco_planilha){

                            
                            if ($preco_venda > $valor_produto_filial->valor * 3){
                                echo " - Preco mais alto que o normal";
                                fwrite($arquivo_log, ';Preço mais alto que o normal');
                                continue;

                            }elseif ($preco_venda < $valor_produto_filial->valor * 0.70){
                                echo " - Preco mais baixo que o normal";
                                fwrite($arquivo_log, ';Preço mais baixo que o normal');
                                continue;

                            }elseif ($preco_venda == $valor_produto_filial->valor && 1!=1){
                                echo " - mesmo valor";
                                fwrite($arquivo_log, ';mesmo valor');
                                continue;
                            }
                            else
                            {
                                echo " - Preco normal";

                            }

                         


                                $valor_produto_filial = new ValorProdutoFilial;
                            $valor_produto_filial->produto_filial_id    = $produtoFilial->id;
                            $valor_produto_filial->valor                = $preco_venda;
                            $valor_produto_filial->valor_cnpj           = $preco_venda;
                            $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                            $valor_produto_filial->promocao             = false;
                            $valor_produto_filial->valor_compra         = $preco_compra;
                                if($valor_produto_filial->save()){
                                    echo " - Preço atualizado";
                                    fwrite($arquivo_log, ';Preço encontrado');
                                }
                                else{
                                    echo " - Preço não atualizado";
                                    fwrite($arquivo_log, ';Preço Não encontrado');
                                }
                        }        
                    }
                        else{
                            echo ' - valor nao encontrado';
                        }
                }
                else{
                    echo " - Estoque não encontrado";
                    fwrite($arquivo_log, ';Estoque Não encontrado');
                }
            }
            else{
                echo " - Não encontrado";
                fwrite($arquivo_log, ';Produto Não encontrado');
            }
            }
        }

 
        
        fclose($arquivo_log); 
        fclose($arquivo_log_erro);

        $filial_nome='Vannucci';
        $assunto = 'Planilha LOG Atualização de preço'.' - '.$filial_nome;

        $email_texto = 'Segue em anexo a Planilha de atualização de preço';

        if($arquivo_log) {            

           
            
                // Yii::$app->mailer->compose()
                // ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                // ->setTo(["dev2.pecaagora@gmail.com"])
                // ->setSubject($assunto)
                // ->setTextBody($email_texto) 
                // ->attach($arquivo_log)    
                // ->attach($arquivo_log_erro)               
                // ->send();

                if ( $file_planilha) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
                        ->setSubject($assunto)
                        ->setTextBody($email_texto);
                    //foreach ($parametros_array['file_planilha'] as $file) {
                        $filename = $log;
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
        $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
        Yii::warning($e);

        if($e){
            $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
            
        }
    
    }    
      
       
        //return $retorno;
        
        echo "\n\nFIM da rotina de atualizacao do preço!";
        return $retorno;

    }
}

