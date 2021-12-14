<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoLNGItaloAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de importação LNG: \n\n";

        $LinhasArray = Array();
        $file = fopen('/var/tmp/NOVOS_PRODUTOS_LNG_PRONTO_PRA_SUBIR.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_importar_lng_27-06-2019.csv")){
            unlink("/var/tmp/log_importar_lng_27-06-2019.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_importar_lng_27-06-2019.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "NOME;PESO;ALT;LARG;PROF;CÓD GLO;CÓD SIM;NCM;CÓD FABRIC;FABRIC;SUBCAT ID;APLIC;APLIC COMP;DESC;MULT;VÍDEO;VALOR VENDA;status produto;status produto_filial;status valor_produto_filial;status imagens\n");

        foreach ($LinhasArray as $k => &$linhaArray ){

            if ($k <= 2){
                continue;
            }

            //print_r($linhaArray); echo "\n";
            $codigo_global = str_replace(" ","",str_replace('-','',str_replace('.','',$linhaArray[6])));

            echo $k." - ".$linhaArray[11]."\n\n\n";

            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[11]])){
                continue;
            }

            if ($codigo_global == null and $codigo_global == ""){
                // Escreve no log
                fwrite($arquivo_log, ';'.$linhaArray[6].';"Sem codigo_referencia"'."\n");
            }
            else {
                $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

                if (isset($produto)){
                    $codigo_global = $codigo_global.",";
                }

                $produto = new Produto();
                $produto->codigo_fabricante         = $linhaArray[9];
                $produto->codigo_global             = $codigo_global;
                $produto->nome                      = $linhaArray[1];
                $produto->aplicacao                 = $linhaArray[12];
                $produto->aplicacao_complementar    = $linhaArray[13];
                $produto->descricao                 = $linhaArray[14];
                $produto->peso                      = $linhaArray[2];
                $produto->altura                    = $linhaArray[3];
                $produto->largura                   = $linhaArray[4];
                $produto->profundidade              = $linhaArray[5];
                $produto->subcategoria_id           = $linhaArray[11];
                $produto->multiplicador         = $linhaArray[15];
                $produto->codigo_similar            = $linhaArray[7];
                $produto->codigo_montadora          = $linhaArray[8];
                $produto->fabricante_id             = 65;
                $this->slugify($produto);
                if ($produto->save()){
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";".$linhaArray[17].";"."Produto CRIADO");
                } else{
                    fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15].";".$linhaArray[16].";".$linhaArray[17].";"."Produto NAO CRIADO\n");
                        continue;
                }

                $produtoFilial              = new ProdutoFilial();
                $produtoFilial->produto_id  = $produto->id;
                $produtoFilial->filial_id   = 60;
                $produtoFilial->quantidade  = 99999;
                $produtoFilial->envio       = 1;
                if ($produtoFilial->save()){
                    fwrite($arquivo_log, ";ProdutoFilial CRIADO");
                } else{
                    fwrite($arquivo_log, ";ProdutoFilial NAO CRIADO\n");
                    continue;
                }

                $valorProdutoFilial                     = New ValorProdutoFilial;
                $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[17]);
                $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[17]);
                $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                //var_dump($valorProdutoFilial);
                if ($valorProdutoFilial->save()){
                    fwrite($arquivo_log, " - ValorProdutoFilial CRIADO");
                } else{
                    fwrite($arquivo_log, " - ValorProdutoFilial NAO CRIADO\n");
                    continue;
                }

                $caminhoImagem          = "/var/tmp/LNG_italo/com_logo/".substr(str_replace("L","",$linhaArray[9]),0,2)."-".substr(str_replace("L","",$linhaArray[9]),-3).".jpg";
                $caminhoImagemSemLogo   = "/var/tmp/LNG_italo/sem_logo/".substr(str_replace("L","",$linhaArray[9]),0,2)."-".substr(str_replace("L","",$linhaArray[9]),-3).".jpg";

                if (file_exists($caminhoImagem)) {
                    echo $caminhoImagem." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produtoFilial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    if ($imagem->save()){
                        fwrite($arquivo_log, " - Imagem CRIADA");
                    } else{
                        fwrite($arquivo_log, " - Imagem NAO CRIADA\n");
                        continue;
                    }
                    echo $caminhoImagem." - EXISTE\n";
                    //var_dump(rename($caminhoImagem, "/var/tmp/vnc1200_900/".str_replace("/","-",$produtoFilial->produto->nome).".jpg"));
                } else {
                    echo $caminhoImagem." - NÃO EXISTE\n";
                    continue;
                }
            }
        }

        // Fecha o arquivo
        fclose($arquivo_log);

        echo "\n\nFIM da rotina de importação LNG!";
    }

    private function slugify(&$model)
    {
        $text = $model->nome . ' ' . $model->codigo_global;
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        $model->slug = $text;
    }
}
