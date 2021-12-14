<?php

namespace backend\functions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\base\ErrorException;

class DibAtualizarPrecosEstoque extends Action
{
    public function run($parametros, $file_planilha)
    {

        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = array();
        try {

             $file = fopen('/var/www/html/backend/web/uploads/' . $file_planilha, 'r');
             //$file = fopen('/var/tmp/' . $file_planilha, 'r');           
            
            while (($line = fgetcsv($file, null, ';')) !== false)          
            {

                $LinhasArray[] = $line;
            }

            fclose($file);

            // Vai logando
            $log = "/var/tmp/" . date('Y-M-d_H:i:s') . '_' . $file_planilha;
            $log_erro = "/var/tmp/log_erro" . date('Y-M-d_H:i:s') . $file_planilha;
            

            $arquivo_log = fopen($log, "a");
            $arquivo_log_erro = fopen($log_erro, "a");

            $parametros_array = json_decode($parametros, true);

            foreach ($LinhasArray as $i => &$linhaArray) {

                if($i < 2925){continue;}

               

                if (
                    !array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_nome'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_estoque'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_preco_compra'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_preco'], $linhaArray)
                    || !array_key_exists($parametros_array['coluna_capas'], $linhaArray) 
                ) {
                    
                    if (array_key_exists($parametros_array['coluna_codigo_fabricante'], $linhaArray)) {
                        fwrite($arquivo_log_erro, "\n" . '"' . $linhaArray[$parametros_array['coluna_codigo_fabricante']] . '";Linha com erros, verificar planilha');
                     
                    } else {
                    
                        fwrite($arquivo_log_erro, "\n" . '"' . $i . '";Linha com erros, verificar planilha"');
                    }

                    echo " - Linha com erros, verificar planilha";
                    continue;
                }
                
                // if($i <= 0 ){

                //     echo "Linha vazia";
                //     continue;
                // }

                $codigo_fabricante_array = $linhaArray[$parametros_array['coluna_codigo_fabricante']];
               
                $nome                    = $linhaArray[$parametros_array['coluna_nome']];
               
                $estoque                 = $linhaArray[$parametros_array['coluna_estoque']];
               
                $preco_compra            = $linhaArray[$parametros_array['coluna_preco_compra']];
               
                $preco_venda             = $linhaArray[$parametros_array['coluna_preco']];
               
                $capas                   = $linhaArray[$parametros_array['coluna_capas']];


                echo "\n" . $i . " - " . $codigo_fabricante_array . " - " . $preco_compra . " - " . $preco_venda . " - " . $estoque;


                 fwrite($arquivo_log, "\n".$codigo_fabricante_array.';'.$preco_compra.';'.$preco_venda.';'.$capas.';'.$nome.';'.$estoque);
                // fwrite($arquivo_log, "\n" . $codigo_fabricante_array . ';' . $preco_compra . ';' . $preco_venda . ';' . $nome . ';' . $estoque);

                // Pula uma linha
                if ($i <= 0) {

                    fwrite($arquivo_log, ";STATUS");

                    continue;
                }

                $codigo_fabricante = (!(strpos($codigo_fabricante_array, "CX.") === false)) ? $codigo_fabricante_array : 'D' . $codigo_fabricante_array;
               
                $produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $codigo_fabricante])

                    //->andWhere(['not like','codigo_fabricante', 'CX.D'.$linhaArray[0]])

                    ->one();               
                   
                // Procura o produto
                if ($produto) {
                        // echo "<prev>";
                        // print_r($produto->id);
                        // echo "</prev>";
                        // die;

                    echo " - Produto encontrado";

                    fwrite($arquivo_log, ";Produto encontrado;");

                    $produtoFilial = ProdutoFilial::find()->andWhere(['=', 'filial_id', 97])

                        ->andWhere(['=', 'produto_id', $produto->id])

                        ->one();

                        
                    if ($produtoFilial) {

                            // echo "<prev>";
                            // print_r($produtoFilial);
                            // echo "</prev>";
                            // die;
                        $produto_filial_Id  = "," . $produtoFilial->id;

                        // echo " - " . $produtoFilial->id;
                        echo " - " . $produto_filial_Id;

                        fwrite($arquivo_log, ";Estoque encontrado");

                        $quantidade = $estoque;

                        if ($preco_compra == 0) {

                            echo " - Preco Compra zerado";

                            $quantidade = 0;
                        }


                        // if ($capas == "239-CAPAS CONFECCAO CHINIL DIB" || $capas == "352-CAPAS CONFECCAO PELUCIA DIB" || $capas == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $capas == "587-CAPAS CONFECCAO CORINO") {
                        //     $quantidade = 991;
                        // }


                        //$nome = $linhaArray[1];


                        if ((!(strpos($nome, "CAPA PORCA") === false)) && (strpos($linhaArray[0], "CX.") === false)) {

                            $quantidade = 0;

                            echo " - CAPA";
                        }
                        if($produtoFilial->quantidade){

                            $produtoFilial->quantidade  = $quantidade;
                        }

                       

                        if ($produtoFilial->save()) {

                            echo " - Estoque alterado";

                            fwrite($arquivo_log, ";Estoque alterado");
                        } else {

                            echo " - Estoque não alterado";

                            fwrite($arquivo_log, ";Estoque não alterado");
                        }

                         //$preco_venda = $linhaArray[$parametros_array['coluna_preco']];

                        // Verifica se o valor a ser atualizado e maior que 300%, menor que 30% ou igual ao valor anterior

                        $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=', 'produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio' => SORT_DESC])->one();

                        if ($valor_produto_filial) {

                            if ($preco_venda > $valor_produto_filial->valor * 3) {

                                echo " - Preco mais alto que o normal";

                                fwrite($arquivo_log, ';Preço mais alto que o normal');

                                continue;
                            } elseif ($preco_venda < $valor_produto_filial->valor * 0.70) {

                                echo " - Preco mais baixo que o normal";

                                fwrite($arquivo_log, ';Preço mais baixo que o normal');

                                continue;
                            } elseif ($preco_venda == $valor_produto_filial->valor) {

                                echo " - mesmo valor";

                                fwrite($arquivo_log, ';mesmo valor');

                                continue;
                            } else {

                                echo " - Preco normal";
                            }
                        } else {
                            echo 'Valor nao encontrado';
                            fwrite($arquivo_log, ';Valor nao encontrado');
                            continue;
                        }

                        $valor_produto_filial = new ValorProdutoFilial;

                        $valor_produto_filial->produto_filial_id    = $produtoFilial->id;

                        $valor_produto_filial->valor                = $preco_venda;

                        $valor_produto_filial->valor_cnpj           = $preco_venda;

                        $valor_produto_filial->valor_compra         = $preco_compra;

                        $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");

                        $valor_produto_filial->promocao             = false;

                        if ($valor_produto_filial->save()) {

                            echo " - Preço criado";

                            fwrite($arquivo_log, ";Preço criado");
                        } else {

                            echo  " - Preço não criado";

                            fwrite($arquivo_log, ";Preço não criado");
                        }
                        //  
                    } else {

                        echo " - Estoque não encontrado";

                        fwrite($arquivo_log, ";Estoque não encontrado");
                        continue;
                    }
                } else {

                    echo " - Produto não encontrado";

                    fwrite($arquivo_log, ";Produto não encontrado");
                     continue;
                }


                $produtos_a_zerar     =   ProdutoFilial::find()
                    ->where(" filial_id = 97 and produto_filial.id not in (" . $produtoFilial->id . ") ")->all();


                foreach ($produtos_a_zerar as $k => $produto_a_zerar) {



                    if ($produto_a_zerar->quantidade != 0) {
                        $produto_a_zerar->quantidade = 0;
                        echo " - \nProduto zerado\n";
                        $produto_a_zerar->save();
                    }
                }
            }

            fclose($arquivo_log);
            fclose($arquivo_log_erro);

            echo "\n\nFIM da rotina de atualizacao do preço!";

            $filial_nome = 'DIB';
            $assunto = 'Planilha LOG Atualização de preço' . ' - ' . $filial_nome;

            $email_texto = 'Segue em anexo a Planilha de atualização de preço';

            if ($arquivo_log) {


                // Yii::$app->mailer->compose()
                // ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                // ->setTo(["dev2.pecaagora@gmail.com","contasareceber.pecaagora@gmail.com"])
                // ->setSubject($assunto)
                // ->setTextBody($email_texto) 
                // ->attach($arquivo_log)
                // ->attach($arquivo_log_erro)                   
                // ->send();

                if ($file_planilha) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com", "contasareceber.pecaagora@gmail.com"])
                        ->setSubject($assunto)
                        ->setTextBody($email_texto);                   
                    $filename = $log;                    
                    $mail->attach($filename);                    
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
                    case 53:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Codigo Fabricante '];
                        break;
                    case 55:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Preco Compra '];
                        break;
                    case 57:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Preco Venda '];
                        break;
                    case 59:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Grupo (Capas) '];
                        break;
                    case 61:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Nome '];
                        break;
                    case 63:
                        $retorno = ["status" => 'Erro Informações Faltando Campo: Estoque '];
                        break;
                    default:
                        $retorno = ["status" => 'Erro Informações Faltando: ' . $e->getMessage()];
                }
            }
        }
        var_dump($retorno);
        die;
        return $retorno;
    }
}
