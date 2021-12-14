<?php

namespace backend\functions\funcoesgerais;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\base\ErrorException;

class VannucciAtualizarEstoque extends Action
{
    public function run($parametros, $file_planilha){
        try {
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();

        $produto_filial_Id = "0";

        $file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');
        //$file = fopen('/var/www/html/pecaagora/backend/web/uploads/'. $file_planilha, 'r');
         
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        // var_dump($LinhasArray);
        // die;

        // Vai logando
        $log = "/var/tmp/".date('Y-M-d_H:i:s').'_'.$file_planilha;
        $log2 = "/var/tmp/log_produto_erro_".$file_planilha;
        //$log = "/var/www/html/pecaagora/backend/web/uploads/log_".date('Y-M-d_H:i:s').'_'.$file_planilha;
        if (file_exists($log)){
            unlink($log);
        }
        $arquivo_log = fopen($log, "a");
        $arquivo_log_erro = fopen($log2, "a"); 

        $parametros_array = json_decode($parametros,true  );
        // var_dump($parametros_array);  
        // die;         
        foreach ($LinhasArray as $i => &$linhaArray){
            
           // echo "TESTANDO AQUI \n";

          if(!array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)
          || !array_key_exists($parametros_array['coluna_nome'], $linhaArray)
          || !array_key_exists($parametros_array['coluna_estoque'], $linhaArray)          
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
       // echo "TESTANDO AQUI 22222222 \n";

            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
           
            $estoque                 = $linhaArray[$parametros_array['coluna_estoque']];

            $nome                    = $linhaArray[$parametros_array['coluna_nome']];

            //$descricao               = $linhaArray[$parametros_array['coluna_descricao']];;

            $codigo_global           = $linhaArray[1];

            //var_dump($codigo_global);
            //die;

            // if($i < 40411 ){
            //   continue;
                      
            // }
            //if($i <= 27294){ 
            //    echo ' - Pular ';                   
             //   continue;
            //}           

            // fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'.$codigo_global.'";"'.$nome." ".$descricao.'";"'.$estoque.'";');
            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'.$codigo_global.'";"'.$nome.'";"'.$estoque.'";');

            echo "\n".$i." - ".$codigo_fabricante_array.' - '.$estoque;            

            if ($i < 1)
            {
                fwrite($arquivo_log, 'STATUS');
                continue;
            }
            
            $codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
            
            $produtoFilial_fabricante = ProdutoFilial::find()   ->joinWith('produto')
                                                                ->andWhere(['=','produto_filial.filial_id',38])
                                                                ->andWhere(['=','produto.codigo_fabricante', $codigo_fabricante])
                                                                ->one();
             
             
            if ($produtoFilial_fabricante){

                 $produto_filial_Id  = ",".$produtoFilial_fabricante->id ; 
                
                 continue;
                

                                        
                        echo "Estoque encontrado - ".$produtoFilial_fabricante->quantidade." - Encontrado";
                        fwrite($arquivo_log, ";".$produtoFilial_fabricante->quantidade.';Produto encontrado Fabricante');
                        //continue;
                        
                        $quantidade = $linhaArray[7];
                        //var_dump($quantidade);
                        
                       // if($produtoFilial_fabricante->e_atualizar_quantidade_planilha){
                        
                        if($quantidade == $produtoFilial_fabricante->quantidade ){
                        echo ' -  Estoques iguais';
                            fwrite($arquivo_log, " - Estoque igual");
                            continue;
                        }
                        if(!$produtoFilial_fabricante->e_atualizar_quantidade_planilha){
                            echo ' -  Não Atualizar segundo a planilha';
                            fwrite($arquivo_log, " -  Não Atualizar segundo a planilha");
                            continue;    
                        }

                        $produtoFilial_fabricante->quantidade = $quantidade;
                        if($produtoFilial_fabricante->save()){
                            echo " - quantidade atualizada";
                            fwrite($arquivo_log, " - Estoque Atualizado");
                        }
                        else{
                            echo " - quantidade não atualizada";
                            fwrite($arquivo_log, " - Estoque NÃO Atualizado");
                        }
                         
            }
            else{

                //continue;
                echo " - Estoque não encontrado";
                fwrite($arquivo_log, " - Estoque não encontrado");
            }
        }

   


        /*$produtoFiliais = ProdutoFilial::find()->andWhere(['=','produto_filial.filial_id',38])->all();
        foreach($produtoFiliais as $x => $produtoFilial){
	    echo "\n".$x;
            $produto_encontrado = false;
            foreach ($LinhasArray as $i => &$linhaArray){
                $codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
                
                if($codigo_fabricante == $produtoFilial->produto->codigo_fabricante){
                    $produto_encontrado = true;
                    break;
                }
            }
            
            if(!$produto_encontrado){
		echo " - produto não encontrado";
                fwrite($arquivo_log, "\n".$x.";".$produtoFilial->produto->nome.";".$produtoFilial->produto->codigo_global.";".$produtoFilial->produto->codigo_fabricante.";".$produtoFilial->produto->descricao.";".$produtoFilial->produto->codigo_montadora.";".$produtoFilial->quantidade.";Produto não encontrado na planilha Vannucci");

		$produtoFilial->quantidade = 0;
		if($produtoFilial->save()){
			fwrite($arquivo_log, " - estoque zerado");
            echo " - estoque zerado";
		}
		else{
			fwrite($arquivo_log, " - estoque não zerado");
            echo " - estoque não zerado";
		}
            }
        }*/                          
        
          $produtos_a_zerar     =   ProdutoFilial::find()
                                                                ->where(" filial_id = 38 and produto_filial.id not in (".$produto_filial_Id.") ") ->all(); 
                                                                
                                                                
         foreach($produtos_a_zerar as $k => $produto_a_zerar){

            

            if($produto_a_zerar->quantidade != 0){
                $produto_a_zerar->quantidade = 0;
                echo " - \nProduto zerado\n";
                $produto_a_zerar->save();
            }  
            
        }
        

                                                                
                                                              
       
        fclose($arquivo_log);
        fclose($arquivo_log_erro);

        echo "\n\nFIM da rotina de atualizacao do preço! \n";

              $filial_nome='Vannucci';
              $assunto = 'Planilha LOG Atualização de estoque'.' - '.$filial_nome;

              $email_texto = 'Segue em anexo a Planilha de atualização de preço';

              if($arquivo_log) {
        
         
                // Yii::$app->mailer->compose()
                // ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                // ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
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

                //print_r($e);
            
        
                if($e){
                    
                    $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
                    
                }
            
            }
             
              return $retorno;
    }
}








