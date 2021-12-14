<?php

namespace backend\functions\funcoesgerais;
use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use yii\base\ErrorException;

class BonfanteCalcularPrecoVenda extends Action
{
    public function run($parametros, $file_planilha){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $faixas = array();
       try {
      

        $faixas = [
            1 => array(0 , 0.99 , 10, true),
            2 => array(1 , 1.99 , 10, true),
            3 => array(2 , 2.99 , 10, true),
            4 => array(3 , 3.99 , 15, true),
            5 => array(4 , 4.99 , 15, true),
            6 => array(5 , 5.99 , 20, true),
            7 => array(6 , 6.99 , 20, true),
            8 => array(7 , 7.99 , 2.5, false),
            9 => array(8 , 8.99 , 2.5, false),
            10 => array(9 , 9.99 , 2.5, false),
            11 => array(10 , 14.99 , 2.5, false),
            12 => array(15 , 19.99 , 2.5, false),
            13 => array(20 , 24.99 , 2.4, false),
            14 => array(25 , 29.99 , 2.3, false),
            15 => array(30 , 34.99 , 2.2, false),
            16 => array(35 , 39.99 , 2.1, false),
            17 => array(40 , 44.99 , 2.0, false),
            18 => array(45 , 49.99 , 1.95, false),
            19 => array(50 , 59.99 , 1.95, false),
            20 => array(60 , 69.99 , 1.90, false),
            21 => array(70 , 79.99 , 1.90, false),
            22 => array(80 , 89.99 , 1.90, false),
            23 => array(90 , 99.99 , 1.90, false),
            24 => array(100 , 124.99 , 1.90, false),
            25 => array(125 , 149.99 , 1.90, false),
            26 => array(150 , 174.99 , 1.90, false),
            27 => array(175 , 199.99 , 1.90, false),
            28 => array(200 , 224.99 , 1.90, false),
            29 => array(225 , 249.99 , 1.90, false),
            30 => array(250 , 299.99 , 1.90, false),
            31 => array(300 , 349.99 , 1.89, false),
            32 => array(350 , 399.99 , 1.88, false),
            33 => array(400 , 449.99 , 1.87, false),
            34 => array(450 , 499.99 , 1.86, false),
            35 => array(500 , 599.99 , 1.85, false),
            36 => array(600 , 699.99 , 1.85, false),
            37 => array(700 , 799.99 , 1.84, false),
            38 => array(800 , 899.99 , 1.84, false),
            39 => array(900 , 999.99 , 1.83, false),
            40 => array(1000 , 1099.99 , 1.83, false),
            41 => array(1100 , 1199.99 , 1.82, false),
            42 => array(1200 , 1299.99 , 1.82, false),
            43 => array(1300 , 1399.99 , 1.81, false),
            44 => array(1400 , 1499.99 , 1.81, false),
            45 => array(1500 , 1999.99 , 1.80, false),
            46 => array(2000 , 2999.99 , 1.80, false),
            47 => array(3000 , 3999.99 , 1.80, false),
            48 => array(4000 , 4999.99 , 1.80, false),
            49 => array(5000 , 100000 , 1.80, false),
            50 => array(100000 , 300000 , 1.80, false),
        ];


        //print_r($faixas);


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



        // Escreve no log

        foreach ($LinhasArray as $i => &$linhaArray){

           

            $preco_compra   = (float) str_replace(",",".",str_replace(".","",$linhaArray[$parametros_array['coluna_preco_compra']]));
            
            $nome= $linhaArray[$parametros_array['coluna_nome']];

            $derivação= $linhaArray[$parametros_array['coluna_derivacao']];

            $codigo_fabricante = $linhaArray[$parametros_array['coluna_codigo_fabricante']].'-'.$derivação;
            
           echo "\n".$i." - ".$codigo_fabricante;

            fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$nome.';'.$preco_compra.";");

            $preco_venda = $preco_compra * 1.8 + 30;  

            fwrite($arquivo_destino, $preco_compra.";".$preco_venda);
            echo " - ".$preco_compra;
            
        

            $produto_caixa  = Produto::find()->andWhere(['=','codigo_fabricante','CX.D'.$codigo_fabricante])->one();
            if($produto_caixa){
                $preco_compra       = $produto_caixa->multiplicador * $preco_compra;
                $codigo_fabricante  = 'CX.D'.$codigo_fabricante;
                
                foreach ($faixas as $k => $faixa) {
                    if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                        $preco_venda = round(($preco_compra * $faixa[2]),2);
                        if ($faixa[3]){
                            $preco_venda = $faixa[2];
                        }
                        fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$nome.';'.$preco_compra.";".$preco_venda);

                        break;
                    }
                }
            }
        }

   

        $filial_nome='Bonfante';
                $assunto = 'Planilha Presificação'.' - '.$filial_nome;

                $email_texto = 'Segue em anexo a Planilha de atualização de preço';

                if($arquivo_destino) { 
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
  
              //return $retorno;
              
        // Fecha o arquivo

        $retorno = ["status" => "Finalizado com sucesso!!!"];
    } catch (ErrorException $e) {
        
        $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
        Yii::warning($e);

        if($e){
            $retorno = ["status" => 'Erro Informações Faltando: '.$e->getMessage()];
            
        }
    
    }


        return $retorno;
        fclose($arquivo_destino);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
