<?php

namespace console\controllers\actions\testeconsole;

use common\models\Imagens;
use common\models\ProdutoFilial;
use common\models\Produto;
use function GuzzleHttp\json_decode;

class TesteAPILNGRelatorioAction extends Action
{
    public function run(){

        $arquivo_log = fopen("/var/tmp/analise_LNG_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "codigo;id;origem;status_api;status_pecaagora;status_imagem");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/ALLPRODUTOS?cuseraccount=OptLng&csenhaaccount=O!891104lnop");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $produtos = curl_exec($ch);
        $produtos_codigo = json_decode($produtos);
        curl_close($ch);

        foreach ($produtos_codigo as $k => $codigo){
            $codigo_fabricante = (str_replace(" ","",$codigo));

            echo "\n".$k." - ".$codigo_fabricante;

            fwrite($arquivo_log, "\n".$codigo_fabricante.";;API LNG");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/PRODUTOS?codproduto=".$codigo_fabricante."&cuseraccount=OptLng&csenhaaccount=O!891104lnop");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produto = curl_exec($ch);
            $produto_dados = json_decode($produto);
            curl_close($ch);

            if(!isset($produto_dados->errorCode)){
                fwrite($arquivo_log,";Produto encontrado na API unitário");
            }
            else{
                fwrite($arquivo_log,";Produto não encontrado na API unitário");
            }

            $codigo_fabricante_limpo = 'L'.str_replace("-","",$codigo_fabricante);
            $produto_lng = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante_limpo])->one();
            if($produto_lng){
                fwrite($arquivo_log,";Produto encontrado no PEÇAAGORA");

                $imagem = Imagens::find()->andWhere(['=','produto_id',$produto_lng->id])->one();
                if($imagem){
                    fwrite($arquivo_log,";Possui imagem");
                }
                else{
                    fwrite($arquivo_log,";Não possui imagem");
                }
            }
            else{
                fwrite($arquivo_log,";Produto não encontrado no PEÇAAGORA;");
            }
        }

        $produtos_filial = ProdutoFilial::find()->andWhere(['=','filial_id',60])
                                                //->andWhere(['=','produto_id','10228'])
                                                ->all();
        foreach($produtos_filial as $k => $produto_filial){

            echo "\n".$k." - ".$produto_filial->produto->codigo_fabricante." - ";

            $e_encontrado_api_global    = false;

            foreach ($produtos_codigo as $x => $codigo){
                $codigo_fabricante_lng  = str_replace("-","",str_replace(" ","",$codigo));
                $codigo_fabricante_peca = str_replace("L","",str_replace(" ","",$produto_filial->produto->codigo_fabricante));
                //echo "==>".$codigo_fabricante_lng." - ".$codigo_fabricante_peca."<==";
                if($codigo_fabricante_lng == $codigo_fabricante_peca){
                    $e_encontrado_api_global = true;
                    break;
                }
            }

            var_dump($e_encontrado_api_global);
            //die;

            if(!$e_encontrado_api_global){

                fwrite($arquivo_log, "\n".$produto_filial->produto->codigo_fabricante.";".$produto_filial->produto->id.";PEÇAAGORA");

                $codigo_fabricante_para_api = substr(str_replace("L","",$produto_filial->produto->codigo_fabricante), 0,2)."-".substr(str_replace("L","",$produto_filial->produto->codigo_fabricante), 2);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/PRODUTOS?codproduto=".$codigo_fabricante_para_api."&cuseraccount=OptLng&csenhaaccount=O!891104lnop");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $produto = curl_exec($ch);
                $produto_dados = json_decode($produto);
                curl_close($ch);

                if(!isset($produto_dados->errorCode)){
                    fwrite($arquivo_log,";Produto encontrado na API unitário");
                }
                else{
                    fwrite($arquivo_log,";Produto não encontrado na API unitário");
                }

                fwrite($arquivo_log,";Produto encontrado no PEÇAAGORA");

                $imagem = Imagens::find()->andWhere(['=','produto_id',$produto_filial->produto->id])->one();
                if($imagem){
                    fwrite($arquivo_log,";Possui imagem");
                }
                else{
                    fwrite($arquivo_log,";Não possui imagem");
                }
            }
        }

        fclose($arquivo_log);

    }
}
