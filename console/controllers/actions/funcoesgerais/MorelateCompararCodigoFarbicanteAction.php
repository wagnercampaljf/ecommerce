<?php
namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Filial;
use yii\base\ErrorException;

class MorelateCompararCodigoFarbicanteAction extends Action
{
    public function run()
    {
        
        echo "INÍCIO da rotina de analise do codigo Fabricante: \n\n";

        $LinhasArray = Array();
         $file = fopen('/var/www/html/backend/web/uploads/more08.csv', 'r'); // ALTERAR CAMINHO DO ARQUIVO.
         //$file = fopen('/var/www/html/backend/web/uploads/'. $file_planilha, 'r');
        // $file = fopen('/var/tmp/vannu07csv', 'r'); // ALTERAR CAMINHO DO ARQUIVO.
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        $log = "/var/tmp/".date('Y-M-d_H:i:s')."_log_morelate_novos";
        $log = fopen($log, "a");

        // var_dump($LinhasArray);
        // die;

        foreach($LinhasArray as $k => $linhaArray){

            if($k < 800){continue;}

            $codigo_fabricante       =    $linhaArray[0];
            //$codigo_global           =    preg_replace('/[^a-zA-Z0-9]/i', '', $linhaArray[1]);;
            //$nome                    =    $linhaArray[0];
            $estoque                 =    $linhaArray[1]; 

            // if(!$codigo_fabricante || !$codigo_global || !$nome || !$estoque){

            //     echo "produto faltando informação";
            //     continue;
            // }

            //Coloca os dados da planilha de log
            fwrite($log, "\n".'"'.$codigo_fabricante.'";"'.$estoque.'";"');

            echo "\n".$k." - ".$codigo_fabricante." - "; //Impressão no console

            // $produto_filial            = ProdutoFilial::find()  ->joinWith('produto')
            //                                                     ->andWhere(['=','produto_filial.filial_id',38])
            //                                                     ->andWhere(["=","replace(replace(replace(replace(replace(replace(produto.codigo_global,'|',''),'.',''),',',''),'-',''),'#',''),'&','')",$codigo_global])
            //                                                     ->andWhere(["=","produto.codigo_fabricante",$codigo_fabricante])
            //                                                     ->one();

            $produto = Produto::find() ->andWhere(["=","codigo_fabricante",$codigo_fabricante.".M"])
                                       ->one();


            if($produto ){                  

                echo " - Estoque encontrado - ".$produto ->quantidade." - Encontrado";
                fwrite($log, ";".$produto ->codigo_fabricante." ".$produto->nome.';Produto encontrado Fabricante');

            }else{
                echo " - Estoque não encontrado";
                fwrite($log, ";"." Estoque não encontrado");
            }
        }

        fclose($log);

        $filial_nome='Morelate';
        $assunto = 'Planilha LOG de Produtos novos'.' - '.$filial_nome;

        $email_texto = 'Segue em anexo a Planilha de Produtos novos Morelate';

        if($log) {            


                if ( $log) {
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                        ->setTo(["dev2.pecaagora@gmail.com"])
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
     
        

        echo "\n\nFIM da rotina de analise ! \n";

        // var_dump($LinhasArray);
        // die;
        
        //fwrite($log,)
    }
    }
        
}
?>