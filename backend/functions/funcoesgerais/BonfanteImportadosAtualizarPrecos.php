<?php

namespace backend\functions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\base\ErrorException;

class BonfanteImportadosAtualizarPrecos extends Action
{
    public function run($parametros, $file_planilha){
        
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";
        
        $LinhasArray = Array();

        try {

 


        $file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');

        

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
          // Vai logando
          $log = "/var/www/html/backend/web/uploads/log_".$file_planilha;

          if (file_exists($log)){
  
              unlink($log);
  
          }

          $arquivo_log = fopen($log, "a");       
          $parametros_array = json_decode($parametros,true  );
        foreach ($LinhasArray as $i => &$linhaArray){ //Looper que vai percorrer o array com as imformações de preços a subir
           
           
            $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
            
            $preco_compra            = $linhaArray[$parametros_array['coluna_preco_compra']];

            $preco_venda             = $linhaArray[$parametros_array['coluna_preco']];

            $nome                    = $linhaArray[$parametros_array['coluna_nome']];

           
           
           
            if ($i >= 0){


            fwrite($arquivo_log, "\n".'"'.$codigo_fabricante_array.'";"'.$nome.'";"'.$preco_compra.'";"'.$preco_venda);
            
            if ($i <= 1){ //Pula a primeira linha de preços, por se tratar dos títulos da planilha
                fwrite($arquivo_log, ";STATUS");
                continue;
            }
            
            echo "\n".$i." - " .$codigo_fabricante_array." - ".$preco_compra." - ".$preco_venda; //Exibe no console(Terminal) as informações dos preços durante o processamento
            
            $produto = Produto::find()->andWhere(['=','codigo_fabricante', $codigo_fabricante_array])->one(); //Procura produto pelo código do fabricante "VA"
            
            if ($produto){ //Se encontrar o produto, processa o preço
                
                echo " - encontrado"; //Escreva no termina l
                fwrite($arquivo_log, ';Produto encontrado'); //Escreve no Log que encontrou o produto

                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',86])
                                                        ->andWhere(['=', 'produto_id', $produto->id])
                                                        ->one(); //Procura o estoque do produto na loja Vannucci




                if ($produtoFilial) {//Se encontrar estoque, processa
                    echo " - ".$produtoFilial->id; //Mostra o id do estoque no terminal
                    fwrite($arquivo_log, ';Estoque encontrado'); //Escreve no log que encontrou o estoque



                    //Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
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

     

        // Fecha o arquivo log
        fclose($arquivo_log); 
        
        echo "\n\nFIM da rotina de atualizacao do preço!";


        $filial_nome='Bonfante';
        $assunto = 'Planilha LOG Atualização de preço Importados'.' - '.$filial_nome;

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
    }
}

