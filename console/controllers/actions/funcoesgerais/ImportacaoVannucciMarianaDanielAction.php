<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Subcategoria;
use common\models\Imagens;

class ImportacaoVannucciMarianaDanielAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";

	$LinhasArrayVannucciEstoque = Array();
        $file = fopen("/var/tmp/vannucci_estoque_ean.csv", 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArrayVannucciEstoque[] = $line;
        }
        fclose($file);

        $LinhasArray = Array();
        //$file = fopen('/var/tmp/vannucci_mariana.csv', 'r');
        //$file = fopen('/var/tmp/mariana115-134_precificado_venda.csv', 'r');
        $file = fopen('/var/tmp/planilha_mariana_daniel_73-86_precificado_venda.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);

        if (file_exists("/var/tmp/log_planilha_mariana_daniel_73-86_precificado_venda.csv")){
            unlink("/var/tmp/log_planilha_mariana_daniel_73-86_precificado_venda.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_planilha_mariana_daniel_73-86_precificado_venda.csv", "a");
        // Escreve no log
        //fwrite($arquivo_log, "COD_FABRICANTE;COD_GLOBAL;NOME_PRODUTO;APLICAÇÃO;CODIGO_SIMILAR;SUBCATEGORIA_ID;PESO;COMPRIMENTO;ALTURA;LARGURA;VALOR;VALOR_COMPRA;VALOR_VENDA;STATUS");
        //fwrite($arquivo_log, "Código Item;NCM;Código Original;NOME;PESO;ALT;LARG;PROF;CÓD GLO;CÓD SIM;NCM;CÓD FABRIC;FABRIC;IDSUB;SUBCAT;SUBMELI_ID;SUBMELI_NOME;APLIC;APLIC COMP;DESC;MULT;Valor;Valor Compra;Valor Venda;STATUS\n");

        foreach ($LinhasArray as $k => &$linhaArray ){

            echo "\n".$k." - ";
            //fwrite($arquivo_log, "\n".$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';"'.$linhaArray[8].'";"'.$linhaArray[9].'";"'.$linhaArray[10].'";"'.$linhaArray[11].'";"'.$linhaArray[12].'";'.$linhaArray[13].';"'.$linhaArray[14].'";"'.$linhaArray[15].'";"'.$linhaArray[16].'";"'.$linhaArray[17].'";"'.$linhaArray[18].'";'.$linhaArray[19].';'.$linhaArray[20].';'.$linhaArray[21].';'.$linhaArray[22].';'.$linhaArray[23].";");
            fwrite($arquivo_log, "\n".$linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";".$linhaArray[14].";".$linhaArray[15]);

            if ($k <= 0){
                fwrite($arquivo_log, ";Status\n");
                continue;
            }

            $codigo_fabricante = $linhaArray[0];
            $produto_fabricante = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante])->one();

            if($produto_fabricante){
                fwrite($arquivo_log, ";codigo_fabricante já cadastrado");
                continue;
            }

            echo $produto_fabricante;
            if (!Subcategoria::findOne(['id'=>(int)$linhaArray[10]])){
                fwrite($arquivo_log, ";Subcategoria não encontrado");
                continue;
            }

            $codigo_global = $linhaArray[1];
            $produto = Produto::find()->andWhere(['=','codigo_global',$codigo_global])->one();

	    $produto_encontrado = false;
            foreach ($LinhasArrayVannucciEstoque as $i => &$LinhaArrayVannucciEstoque)
            {
                if ($linhaArray[0] == $LinhaArrayVannucciEstoque[4])
                {
                    $produto_encontrado = true;
                    break;
                }
            }
            if(!$produto_encontrado)
            {
                continue;
            }

            if (isset($produto)){
                $codigo_global = $codigo_global.",";
                fwrite($arquivo_log, ";Produto encontrado");
            }
            else{
                fwrite($arquivo_log, ";Produto não encontrado");
            }

            $produto = new Produto();
            $produto->codigo_fabricante         = $linhaArray[0];
            $produto->codigo_global             = $codigo_global;
            //$produto->codigo_similar          = $linhaArray[9];
            $produto->nome                      = substr($linhaArray[2], 0, 150);
            $produto->aplicacao                 = $linhaArray[3];
            $produto->aplicacao_complementar    = $linhaArray[11];
            $produto->peso                      = $linhaArray[9];
            $produto->altura                    = $linhaArray[6];
            $produto->largura                   = $linhaArray[8];
            $produto->profundidade              = $linhaArray[7];
            $produto->subcategoria_id           = $linhaArray[10];
            $produto->codigo_montadora          = $linhaArray[13];
            $produto->fabricante_id             = 91;
            $this->slugify($produto);
            if ($produto->save()){
                echo " - ".$produto->id;

                fwrite($arquivo_log, " - Produto criado");

                $produtoFilial              = new ProdutoFilial();
                $produtoFilial->produto_id  = $produto->id;
                $produtoFilial->filial_id   = 38;
                $produtoFilial->quantidade  = 99999;
                $produtoFilial->envio       = 1;
                if ($produtoFilial->save()){

                    fwrite($arquivo_log, " - Estoque criado");

                    $valorProdutoFilial                     = New ValorProdutoFilial;
                    $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                    $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[15]);
                    $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[15]);
                    $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
                    if($valorProdutoFilial->save()){
                        fwrite($arquivo_log, " - Valor criado");
                    }
                    else{
                        fwrite($arquivo_log, " - Valor não criado");
                    }
                }

                $caminhoImagem          = "/var/tmp/planilha_mariana_daniel_73-86_precificado_venda/com_logo/".$produto->codigo_fabricante.".jpg";
                $caminhoImagemSemLogo   = "/var/tmp/planilha_mariana_daniel_73-86_precificado_venda/sem_logo/".$produto->codigo_fabricante.".jpg";

                if (file_exists($caminhoImagem)) {
                    echo " - ".$caminhoImagem." - EXISTE\n";
                    $imagem = new Imagens();
                    $imagem->produto_id         = $produtoFilial->produto->id;
                    $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
                    $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                    $imagem->ordem              = 1;
                    $imagem->save();
                }

            }
            else{
                fwrite($arquivo_log, " - Produto não criado");
            }
        }

        fclose($arquivo_log);

        echo "\n\nFIM da rotina de criação preço!";
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
