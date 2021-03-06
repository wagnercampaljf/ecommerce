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
                
        foreach ($LinhasArray as $i => &$linhaArray){

            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
            $estoque                 = $linhaArray[$parametros_array['coluna_estoque']];

            /*if($i <= 23981 ){
                continue;
            }*/
            
            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'.$estoque.'";');

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
            if ($produtoFilial_fabricante) {
                
                echo "Estoque encontrado - ".$produtoFilial_fabricante->quantidade." - Encontrado";
                fwrite($arquivo_log, ";".$produtoFilial_fabricante->quantidade.';Produto encontrado Fabricante');
                //continue;
                
                $quantidade = $linhaArray[7];
                
                if($quantidade == $produtoFilial_fabricante->quantidade){
                    fwrite($arquivo_log, " - Estoque igual");
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
        
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de atualizacao do preço!";

              $filial_nome='Vannucci';
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








