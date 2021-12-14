<?php
//1111
namespace console\controllers\actions\funcoesgerais;

use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\ValorProdutoFilial;
use common\models\Produto;
use common\models\ValorProdutoMenorMaior;
use console\controllers\actions\omie\Omie;
use yii\helpers\Json;
use vendor\iomageste\Moip\Http\HTTPConnection;
use vendor\iomageste\Moip\Http\HTTPRequest;
use linslin\yii2\curl\Curl;
use function GuzzleHttp\json_decode;
use common\models\ProdutoFilial;

class LNGAPIAction extends Action
{
    public function run(){

	//echo date("Y-m-d_H-i-s");die;

        $arquivo_log = fopen("/var/tmp/log_lng_api".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log, "codigo_fabricante;codigo_original;codigo_montadora;nome;micro_descricao;aplicacao;aplicacao_complementar;mulpplicador;estoque;valor;peso;altura;largura;profundidade;ipi;cest;codigo_barras;imagem;status");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/ALLPRODUTOS?cuseraccount=OptLng&csenhaaccount=O!891104lnop&cCNPJ=18947338000463");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $produtos = curl_exec($ch);
        //var_dump($produtos); die;
        $produtos_codigo = json_decode($produtos);
        curl_close($ch);
        print_r($produtos_codigo );
        $arquivo_log = fopen("/var/tmp/lng_api_produtos_".date("Y-m-d_H-i-s").".csv", "a");
        fwrite($arquivo_log,$produtos); //die;

	$e_processar = true;

	$contador = 0;

        foreach ($produtos_codigo as $k => $codigo){

            //if(" && $codigo != "61-334         "){ continue;}
            //if(!(str_replace(" ","",$codigo) == "60-938")){ continue;}

            echo "\n".$contador++." - ".$k." - ".$codigo;

            $codigo_fabricante = (str_replace(" ","",$codigo));
            $codigo_fabricante_peca = "L".str_replace("-","",$codigo_fabricante);

	    if($k == "4795"){
		$e_processar = true;
	    }

	    if(!$e_processar){
		continue;
	    }

            $codigo_fabricante_caixa = (str_replace(" ","",$codigo));
            $codigo_fabricante_peca_caixa = "L".str_replace("-","",$codigo_fabricante_caixa);
            $codigo_fabricante_peca_caixa_codigo = "CX.".str_replace("","",$codigo_fabricante_peca_caixa);


            echo " - ".$codigo_fabricante_peca;

            //if($k<500){continue;}

            //if($codigo_fabricante_peca != "L43600"){ continue;}

            $ch = curl_init();
	    //SÃO PAULO
            //curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/PRODUTOS?codproduto=".$codigo_fabricante."&cuseraccount=OptLng&csenhaaccount=O!891104lnop&cCNPJ=18947338000200");
	    //MINAS GERAIS
            //curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/PRODUTOS?codproduto=".$codigo_fabricante."&cuseseraccount=OptLng&csenhaaccount=O!891104lnop&cCNPJ=18947338000463");
	    curl_setopt($ch, CURLOPT_URL, "http://189.112.136.145:86/restprod/PRODUTOS?codproduto=".$codigo_fabricante."&cuseraccount=OptLng&csenhaaccount=O!891104lnop:&cCNPJ=18947338000463");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $produto = curl_exec($ch);
            $produto_dados = json_decode($produto);
            curl_close($ch);
            print_r($produto_dados);

            if(!isset($produto_dados->errorCode)){
                echo " - Produto encontrado na API";

                fwrite($arquivo_log, "\n".$produto_dados->codigo_fabricante.";".$produto_dados->codigo_original.";".$produto_dados->codigo_montadora.";".$produto_dados->nome.";".$produto_dados->micro_descricao.";".$produto_dados->aplicacao.";".$produto_dados->aplicacao_complementar.";".$produto_dados->mulpplicador.";".$produto_dados->estoque.";".$produto_dados->valor.";".$produto_dados->peso.";".$produto_dados->altura.";".$produto_dados->largura.";".$produto_dados->profundidade.";".$produto_dados->ipi.";".$produto_dados->cest.";".$produto_dados->codigo_barras.";".$produto_dados->imagem.";Produto Encontrado API");

                $produto_peca = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante_peca])->one();


                if($produto_peca){

		            //echo $produto_peca->codigo_montadora;
                    //echo str_replace("  ","",$produto_dados->codigo_montadora);
		            //die;

                    fwrite($arquivo_log, " - Produto Encontrado Peça");

                    $produto_peca->cest             = $produto_dados->cest;
                    $produto_peca->ipi              = $produto_dados->ipi;
                    $produto_peca->codigo_barras    = $produto_dados->codigo_barras;

		    if($produto_peca->codigo_montadora == "" || is_null($produto_peca->codigo_montadora)){
			$produto_peca->codigo_montadora = str_replace("  ","",$produto_dados->codigo_montadora);
		    }

		    if(!is_null($produto_dados->codigo_original) && $produto_dados->codigo_original != ""){
    			$codigo_similar_limpo	= str_replace("  ","",str_replace(".","",str_replace("-","",$produto_dados->codigo_original)));
    			//$pos = strpos($produto_peca->codigo_similar, $produto_dados->codigo_original);
    			$pos = strpos($produto_peca->codigo_similar, $codigo_similar_limpo);
            	if($pos === false)
            	{
    			    //$produto_peca->codigo_similar .= " | ".$produto_dados->codigo_original;
    			    $produto_peca->codigo_similar .= " | ".$codigo_similar_limpo;
                        	    echo " - código_similar não preenchido";
            	}
            	else
            	{
            	    echo " - código_similar já preenchido";
            	}
            }

                    if($produto_peca->save()){

                        fwrite($arquivo_log, " - Produto Salvo");

                        echo " - produto salvo";

                        echo " - Produto encontrado no PEÇA";

                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','filial_id',60])
					                        ->andWhere(['=','produto_id',$produto_peca->id])
					                        ->one();
                        if($produto_filial){

                            fwrite($arquivo_log, " - Estoque encontrado");

                            echo " - Estoque encontrado";

                            $produto_filial->quantidade = $produto_dados->estoque;
                            if($produto_filial->save()){

                                fwrite($arquivo_log, " - Estoque Salvo");

                                echo " - Estoque atualizado";

				$multiplicador = 1;

				if(!is_null($produto_filial->produto->multiplicador)){
					if($produto_filial->produto->multiplicador > 1){
						$multiplicador = $produto_filial->produto->multiplicador;
					}
				}
                                



                                $preco_compra   = $produto_dados->valor * $multiplicador ;


                                echo "\n\nPreço compra(Multiplicador): ".$preco_compra."\n\n";

                               // $preco_compra   = $produto_dados->valor;
                                $ipi            = 0;
                                $st             = 0;//$preco_compra * (0.188);
                                if($produto_peca->ipi != null && $produto_peca->ipi != 0){
                                    $ipi            = $preco_compra * ($produto_peca->ipi/100);
                                }
                                $preco_compra   += ($ipi + $st);
                                $preco_venda = $this->calcularPrecoVenda($preco_compra);

				if(!$produto_filial->atualizar_preco_mercado_livre){
					continue;
				}

                                $valor_produto_filial = new ValorProdutoFilial;
                                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                                $valor_produto_filial->valor                = $preco_venda;
                                $valor_produto_filial->valor_cnpj           = $preco_venda;
                                $valor_produto_filial->valor_compra         = $produto_dados->valor;
                                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                                $valor_produto_filial->promocao             = false;
                                if($valor_produto_filial->save()){
                                    echo " - Preço criado";

                                    fwrite($arquivo_log, " - Valor Salvo");
                                }
                                else{
                                    echo " - Preço não criado";
                                    fwrite($arquivo_log, " - Valor Não Salvo");
                                }
                            }
                            else{
                                echo " - Estoque não salvo";
                                fwrite($arquivo_log, " - Estoque Não Salvo");
                            }
                        }
                        else{
                            echo " - Estoque não encontrado";
                            fwrite($arquivo_log, " - Estoque Não Salvo");
                        }
                    }
                    else{
                        echo " - produto não salvo";
                        fwrite($arquivo_log, " - Produto Não Salvo");
                    }
                }
                else{

		    continue;
                    fwrite($arquivo_log, " - Produto Não Encontrado Peça");

                    if(str_replace(" ","",$produto_dados->nome) == ""){
                        echo " - PRODUTO SEM NOME";
                        //fwrite($arquivo_log, " - Produto Sem Nome");
                        //continue;
                    }

                    $peso           = 3;
                    $altura         = 30;
                    $largura        = 30;
                    $profundidade   = 30;
                    if($produto_dados->peso <> 0 && $produto_dados->peso <> null && $produto_dados->peso <> ""){
                        $peso = $produto_dados->peso;
                    }
                    if($produto_dados->altura <> 0 && $produto_dados->altura <> null && $produto_dados->altura <> ""){
                        $altura = $produto_dados->altura;
                    }
                    if($produto_dados->largura <> 0 && $produto_dados->largura <> null && $produto_dados->largura <> ""){
                        $largura = $produto_dados->largura;
                    }
                    if($produto_dados->profundidade <> 0 && $produto_dados->profundidade <> null && $produto_dados->profundidade <> ""){
                        $profundidade = $produto_dados->profundidade;
                    }

                    $codigo_global = explode("/",str_replace("  ","",$produto_dados->codigo_original));

		    $nome_limpo = str_replace(" ","",$produto_dados->nome);
		    if ($nome_limpo == ""){
			$nome_limpo = str_replace("  ","",$produto_dados->micro_descricao)." ".str_replace("  ","",$produto_dados->aplicacao)." ".str_replace("  ","",$produto_dados->aplicacao_complementar);
		    }
		    else{
			$nome_limpo = str_replace("  ","",$produto_dados->nome);
		    }

                    $produto_novo                           = new Produto;
                    $produto_novo->nome                     = substr($nome_limpo, 0, 150);
                    $produto_novo->aplicacao                = str_replace("  ","",$produto_dados->aplicacao);
                    $produto_novo->aplicacao_complementar   = str_replace("  ","",$produto_dados->aplicacao_complementar);
                    $produto_novo->descricao                = str_replace("  ","",$produto_dados->micro_descricao);
                    $produto_novo->micro_descricao          = str_replace("  ","",$produto_dados->micro_descricao);
                    $produto_novo->peso                     = $peso;
                    $produto_novo->altura                   = $altura;
                    $produto_novo->largura                  = $largura;
                    $produto_novo->profundidade             = $profundidade;
                    $produto_novo->codigo_barras            = str_replace(" ","",$produto_dados->codigo_barras);
                    $produto_novo->codigo_fabricante        = "L".str_replace("  ","",str_replace("-","",$produto_dados->codigo_fabricante));
                    $produto_novo->codigo_montadora         = str_replace("  ","",$produto_dados->codigo_montadora);
                    $produto_novo->codigo_global            = $codigo_global[0];
                    $produto_novo->codigo_similar           = str_replace("  ","",str_replace(".","",str_replace("-","",$produto_dados->codigo_original)));
                    $produto_novo->ipi                      = $produto_dados->ipi;
                    $produto_novo->cest                     = str_replace("  ","",$produto_dados->cest);
                    $produto_novo->fabricante_id            = 33;
                    $produto_novo->subcategoria_id          = 285;
		    $produto_novo->produto_condicao_id      = 1;
                    $this->slugify($produto_novo);
                    //print_r($produto_novo); die;
                    if ($produto_novo->save()){
                        echo " - Produto cadastrado";

                        fwrite($arquivo_log, " - Produto Salvo");

                        $produtoFilial              = new ProdutoFilial();
                        $produtoFilial->produto_id  = $produto_novo->id;
                        $produtoFilial->filial_id   = 60;
                        $produtoFilial->quantidade  = 99999;
                        $produtoFilial->envio       = 1;
                        if ($produtoFilial->save()){
                            echo " - ProdutoFilial CRIADO";
                            fwrite($arquivo_log, " - Estoque Salvo");

                            $preco_compra   = $produto_dados->valor;
                            $ipi            = 0;
                            $st             = 0;//$preco_compra * (0.18);
                            if($produto_novo->ipi != null && $produto_novo->ipi != 0){
                                $ipi            = $preco_compra * ($produto_novo->ipi/100);
                            }
                            $preco_compra   += ($ipi + $st);
                            $preco_venda = $this->calcularPrecoVenda($preco_compra);

                            $valorProdutoFilial                     = New ValorProdutoFilial;
                            $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
                            $valorProdutoFilial->valor              = $preco_venda;
                            $valorProdutoFilial->valor_cnpj         = $preco_venda;
                            $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
			    $valorProdutoFilial->valor_compra       = $produto_dados->valor;
                            if ($valorProdutoFilial->save()){
                                echo " - ValorProdutoFilial CRIADO\n";
                                fwrite($arquivo_log, " - Valor Salvo");
                            } else{
                                echo " - ValorProdutoFilial NAO CRIADO\n";
                                fwrite($arquivo_log, " - Valor Não Salvo");
                            }
                        } else{
                            echo " - ProdutoFilial NAO CRIADO\n";
                            fwrite($arquivo_log, " - Estoque Não Salvo");
                        }
                    } else{
                        echo " - Produto não cadastrado";
                        fwrite($arquivo_log, " - Produto Não Salvo");
                    }
                }

                $produto_peca_caixa = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante_peca_caixa_codigo])->one();
                if ($produto_peca_caixa){



                    //echo $produto_peca->codigo_montadora;
                    //echo str_replace("  ","",$produto_dados->codigo_montadora);
                    //die;

                    fwrite($arquivo_log, " - Produto Encontrado Peça");

                    $produto_peca_caixa->cest             = $produto_dados->cest;
                    $produto_peca_caixa->ipi              = $produto_dados->ipi;
                    $produto_peca_caixa->codigo_barras    = $produto_dados->codigo_barras;

                    if($produto_peca_caixa->codigo_montadora == "" || is_null($produto_peca_caixa->codigo_montadora)){
                        $produto_peca_caixa->codigo_montadora = str_replace("  ","",$produto_dados->codigo_montadora);
                    }

                    if(!is_null($produto_dados->codigo_original) && $produto_dados->codigo_original != ""){
                        $codigo_similar_limpo	= str_replace("  ","",str_replace(".","",str_replace("-","",$produto_dados->codigo_original)));
                        //$pos = strpos($produto_peca->codigo_similar, $produto_dados->codigo_original);
                        $pos = strpos($produto_peca->codigo_similar, $codigo_similar_limpo);
                        if($pos === false)
                        {
                            //$produto_peca->codigo_similar .= " | ".$produto_dados->codigo_original;
                            $produto_peca_caixa->codigo_similar .= " | ".$codigo_similar_limpo;
                            echo " - código_similar não preenchido";
                        }
                        else
                        {
                            echo " - código_similar já preenchido";
                        }
                    }

                    if($produto_peca_caixa->save()){

                        fwrite($arquivo_log, " - Produto Salvo");

                        echo " - produto salvo";

                        echo " - Produto encontrado no PEÇA";

                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','filial_id',60])
                            ->andWhere(['=','produto_id',$produto_peca_caixa->id])
                            ->one();
                        if($produto_filial){

                            fwrite($arquivo_log, " - Estoque encontrado");

                            echo " - Estoque encontrado";

                            $produto_filial->quantidade = $produto_dados->estoque;
                            if($produto_filial->save()){

                                fwrite($arquivo_log, " - Estoque Salvo");

                                echo " - Estoque atualizado";

                                $multiplicador = 1;

                                if(!is_null($produto_filial->produto->multiplicador)){
                                    if($produto_filial->produto->multiplicador > 1){
                                        $multiplicador = $produto_filial->produto->multiplicador;
                                    }
                                }




                                $preco_compra   = $produto_dados->valor * $multiplicador ;


                                echo "\n\nPreço compra(Multiplicador): ".$preco_compra."\n\n";

                                // $preco_compra   = $produto_dados->valor;
                                $ipi            = 0;
                                $st             = 0;//$preco_compra * (0.188);
                                if($produto_peca_caixa->ipi != null && $produto_peca_caixa->ipi != 0){
                                    $ipi            = $preco_compra * ($produto_peca_caixa->ipi/100);
                                }
                                $preco_compra   += ($ipi + $st);
                                $preco_venda = $this->calcularPrecoVenda($preco_compra);

                                if(!$produto_filial->atualizar_preco_mercado_livre){
                                    continue;
                                }

                                $valor_produto_filial = new ValorProdutoFilial;
                                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                                $valor_produto_filial->valor                = $preco_venda;
                                $valor_produto_filial->valor_cnpj           = $preco_venda;
                                $valor_produto_filial->valor_compra         = $produto_dados->valor;
                                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                                $valor_produto_filial->promocao             = false;
                                if($valor_produto_filial->save()){
                                    echo " - Preço criado";

                                    fwrite($arquivo_log, " - Valor Salvo");
                                }
                                else{
                                    echo " - Preço não criado";
                                    fwrite($arquivo_log, " - Valor Não Salvo");
                                }
                            }
                            else{
                                echo " - Estoque não salvo";
                                fwrite($arquivo_log, " - Estoque Não Salvo");
                            }
                        }
                        else{
                            echo " - Estoque não encontrado";
                            fwrite($arquivo_log, " - Estoque Não Salvo");
                        }
                    }
                    else{
                        echo " - produto não salvo";
                        fwrite($arquivo_log, " - Produto Não Salvo");
                    }
                }
            }
            else{
                echo "\n".$k." - ".$codigo." - Produto não encontrado na API";
                fwrite($arquivo_log, "\n".$codigo.";;;;;;;;;;;;;;;;;;Produto Não Encontrato na API");

                $produto_peca = Produto::find()->andWhere(['=','codigo_fabricante',$codigo_fabricante_peca])->one();
                if($produto_peca){
                    echo " - Produto encontrado no PEÇA";

                    $produto_filial = ProdutoFilial::find() ->andWhere(['=','filial_id',60])
                                                            ->andWhere(['=','produto_id',$produto_peca->id])
                                                            ->one();
                    if($produto_filial){
                        echo " - Estoque encontrado";
                        fwrite($arquivo_log, " - Produto Encontrado Peça");

                        $produto_filial->quantidade = 0;
                        if($produto_filial->save()){
                            echo " - Estoque atualizado";
                            fwrite($arquivo_log, " - Estoque Salvo");
                        }
                        else{
                            echo " - Estoque não atualizado";
                            fwrite($arquivo_log, " - Estoque Não Salvo");
                        }
                    }
                    else{
                        echo " - Estoque não encontrado";
                        fwrite($arquivo_log, " - Estoque Não Encontrado");
                    }
                }
                else{
                    echo " - Produto não encontrado no PEÇA";
                    fwrite($arquivo_log, " - Produto Não Encontrado PEÇA");
                }
            }
        }

        fclose($arquivo_log);
    }

    public function calcularPrecoVenda($preco_compra){

        $faixas = array();


        $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

        $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

        $faixas = [];

        foreach ($markups_detalhe as $markup_detalhe){
            $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

        }

        //$preco_compra = $preco_compra * 0.65;
        foreach ($faixas as $k => $faixa) {
            if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){
                $preco_venda = round(($preco_compra * $faixa[2]),2);
                if ($faixa[3]){
                    $preco_venda = $faixa[2];
                }

                return $preco_venda;
            }
        }
    }

    private function slugify(&$produto_slugfy)
    {
        $text = $produto_slugfy->nome . ' ' . $produto_slugfy->codigo_global;
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
        $produto_slugfy->slug = $text;
    }


    //segunda  analise







}
