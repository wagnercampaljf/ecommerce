<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class VannucciPreencherDadosPlanilhaAction extends Action
{
    public function run(){
        
        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $linhas_vannucci = Array();
        $file = fopen('/var/tmp/log_categoria_por_filial_ml_PeçaAgoraVImportado_2020-06-03_18-33-34.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $linhas_vannucci[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/vannucci_categoria_me_04-06-2020_02.csv")){
            unlink("/var/tmp/vannucci_categoria_me_04-06-2020_02.csv");
        }
        $arquivo_gerado = fopen("/var/tmp/vannucci_categoria_me_04-06-2020_02.csv", "a");
        
        fwrite($arquivo_gerado, "produto_filial_id;categoria_meli_id;status_me;status_ml;subcategoria_id;subcategoria_nome;codigo_global;codigo_fabricante;nome;quantidade\n");
        
        foreach ($linhas_vannucci as $k => &$linha ){
            
            if($k <= 0){
                continue;
            }
            
            echo "\n".$k." - ".$linha[0];
            
            $produto_filial = ProdutoFilial::find()->andWhere(['=','id',$linha[0]])->one();
                        
            fwrite($arquivo_gerado, "\n".'"'.$linha[0].'";"'.$linha[1].'";"'.$linha[2].'";"'.$linha[3].'";"'.$produto_filial->produto->subcategoria_id.'";"'.$produto_filial->produto->subcategoria->nome.'";"'.$produto_filial->produto->codigo_global.'";"'.$produto_filial->produto->codigo_fabricante.'";"'.$produto_filial->produto->nome.'";"'.$produto_filial->quantidade.'"');
        }
        
        // Fecha o arquivo
        fclose($arquivo_gerado);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}











