<?php

namespace console\controllers\actions\funcoesgerais;

use Yii;
use yii\base\Action;
use common\models\Produto;


class DibAtualizarMarcaAction extends Action
{
    public function run(){

        echo "Inicio da Atualização de marca de produto";

        $LinhasArray = Array();

       // $parametros_array = json_decode($parametros,true  );

        //$arquivo_origem = '/var/www/html/backend/web/uploads/'. $file_planilha;

        $arquivo_origem = '/var/tmp/DibRodibem.csv';
    
        $file = fopen($arquivo_origem, 'r');

        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        $destino = '/var/tmp/'."dib_produto_marca_atualizada".date('Y-M-d_H:i:s')."csv";
        $arquivo_destino = fopen($destino, "a");
        

        foreach ($LinhasArray as $i => &$linhaArray){

            $codigo_global= $linhaArray[0];

            $nome = $linhaArray[1];

            fwrite($arquivo_destino,"\n".'"'.$codigo_global.'";"'.$nome.'";');

            echo $i." - ".$codigo_global;

            $produtos   = Produto::find()       //->leftJoin('produto_filial','produto_filial.produto_id=produto.id')
                                               // ->andWhere(['=','produto_filial.filial_id',97]) 
                                                ->andWhere(['=','codigo_global',$codigo_global])
                                                ->all();
            

                if($produtos){

                    foreach($produtos as $k=>$produto){

                        // var_dump($produto);
                        // die;

                       $produto->marca_produto_id = 1187;
                       echo " - Marca RDB adicionada \n ";
                       fwrite($arquivo_destino, "  Marca adicionada");
                        
                    }

                  }else{
                      echo " - Produuto não encontrado - \n ";
                      fwrite($arquivo_destino, "  Estoque não encontrado");
                  }  

                  $produto->save();                                    
                                           
        }
        fclose($arquivo_destino);

    }
}


?>