<?php

namespace console\controllers\actions\testeconsole;

use Yii;
use yii\helpers\ArrayHelper;
use function GuzzleHttp\json_encode;
use function GuzzleHttp\json_decode;
use console\controllers\actions\omie\Omie;
use common\models\Produto;
use common\models\Imagens;
use common\models\ProdutoFilial;
use common\mail\AsyncMailer;
use common\models\PedidoMercadoLivre;
use common\models\PedidoMercadoLivreProduto;
use common\models\PedidoMercadoLivreProdutoProdutoFilial;
use common\models\ValorProdutoFilial;
use backend\models\PedidoCompraProdutoFilial;
use common\models\PedidoMercadoLivreShipments;
use common\models\PedidoProdutoFilial;
use backend\functions\FunctionsML;
use common\models\Filial;
use Livepixel\MercadoLivre\Meli;

class TesteAction extends Action
{
    //const APP_KEY_OMIE_SP              = '468080198586';
    //const APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';

    public function run($q){
        
        echo "INÍCIO TESTE\n\n";

	//ta
        /*$arquivo_empresas = fopen("/var/tmp/teste/empresas_log.csv", "a");
	$arquivo_acumuladores = fopen("/var/tmp/teste/acumuladores_log.csv", "a");
        //fwrite($arquivo, "sku".chr(9)."price".chr(9)."minimum-seller-allowed-price".chr(9)."maximum-seller-allowed-price".chr(9)."quantity");

        $empresas        = array();
        $acumuladores    = array();

        $linhasArray = Array();
        $file = fopen("/var/tmp/acumuladores.csv", 'r'); //Abre arquivo com pre  os para subir

	$x = 0;

        while (($line = fgetcsv($file,null,';')) !== false)
        {
		if($x++ == 0){ continue; }

                if(!in_array($line[3], $acumuladores)){
                        $acumuladores[] = $line[3];
                }

		$empresas[$line[0]][$line[2]][] = $line[2];
		$empresas[$line[0]][$line[2]][] = $line[3];
		$x++;
        }
        fclose($file);

	fwrite($arquivo_acumuladores, "CODIGO;ACUMULADOR");
	foreach($acumuladores as $i => $acumulador){
		fwrite($arquivo_acumuladores, "\n".$i.";".$acumulador);
	}

	foreach($empresas as $k => $empresa){
		foreach($empresa as $j => $acumulador){
			$chave = array_search($acumulador[1], $acumuladores);
			//echo "\n".$k." - ".$j." - ".$chave." - ".$acumulador[1];
			$empresas[$k][$j][] = $chave;
			$empresas[$k][$j][] = $acumuladores[$chave];
		}
	}

	fwrite($arquivo_empresas, "CODIGO_EMPRESA;CODIGO_ACUMULADOR_ANTIGO;ACUMULADOR_ANTIGO;CODIGO_ACUMULADOR_NOVO");
        foreach($empresas as $h => $empresa){
		foreach($empresa as $t => $acumuladores_empresa){
			fwrite($arquivo_empresas, "\n".$h);
			foreach($acumuladores_empresa as $r => $acumulador){
				echo " - ".$acumulador;
				fwrite($arquivo_empresas, ";".$acumulador);
			}
		}
        }

	fclose($arquivo_empresas);
	fclose($arquivo_acumuladores);*/

	//$password = "123opt123";
        //var_dump(Yii::$app->security->generatePasswordHash($password));

	/*$APP_KEY_OMIE_SP              = '468080198586';
    	$APP_SECRET_OMIE_SP           = '7b3fb2b3bae35eca3b051b825b6d9f43';

    	$APP_KEY_OMIE_CONTA_DUPLICADA      = '1017311982687';
	$APP_SECRET_OMIE_CONTA_DUPLICADA  = '78ba33370fac6178da52d42240591291';

	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://app.omie.com.br/api/v1/produtos/nfconsultar/?JSON={"call":"ConsultarNF","app_key":"'.$APP_KEY_OMIE_SP.'","app_secret":"'.$APP_SECRET_OMIE_SP.'","param":[{"cChaveNFe":"41181121195013000133550010000026071000302847"}]}');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $produtos = curl_exec($ch);
        $produtos_codigo = json_decode($produtos);
        curl_close($ch);
        print_r($produtos_codigo);


	die;


	$emails_destinatarios   = ['wagnercampaljf@yahoo.com.br', 'dev.pecaagora@gmail.com', 'nfe.pecaagora@gmail.com', 'compras.pecaagora@gmail.com'];
        $assunto                = "Teste Dev - Imagem 27";
        //$email_texto          = "https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2";
        //$email_texto                  = "<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>";
        $email_texto            = "<div>Boa tarde, Como vai?</div>

<br><div>Segue dados de um novo pedido a ser faturado. Nosso financeiro ira realizar o pagamento conforme combinado anteriormente.<div>

<br><div><img  width='15%' heigth='15%' src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'></div>

<br><div>Cod.:</div>
<div>Descricao: SUPER BONDER LOCTITE PRECISAO 10G DOBRO DE PRODUTO (BONDER10)</div>
<div>Quantidade:  * 1  Unidade</div>
<div>Valor: estoque</div>

<br><div>Envio: Carm  polis de Minas, 963, Vila Maria.</div>


<br></br><div>Atenciosamente,</div>


<br></br><div>Peca Agora</div>
<div>Site: https://www.pecaagora.com/</div>
<div>E-mail: compras.pecaagora@gmail.com</div>
<div>Setor de Compras:(32)3015-0023</div>
<div>Whatsapp:(32)988354007</div>
<div>Skype: pecaagora</div>";

        var_dump(\Yii::$app->mailer   ->compose()
                                      ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                                      //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
                                      ->setTo($emails_destinatarios)
                                      //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                                      ->setSubject($assunto)
                                      //->setTextBody($email_texto)
                                      ->setHtmlBody($email_texto)
                                      ->send());
	*/

        /*$results = ProdutoFilial::find()
        ->select(['produto_filial.id', "(coalesce((filial.nome),'') || coalesce((produto.nome),'')) as text"])
        ->joinWith(['produto', 'filial'])
        ->where([
            'like',
            'lower(produto.nome)',
            strtolower($q)
        ])
        ->orWhere([
            'lower(produto_filial.id::VARCHAR)' =>  strtolower($q)
        ])
        ->limit(10)
        ->createCommand()
        ->queryAll();
        $out['results'] = array_values($results);
        print_r($results); print_r($out);*/
        
        /*$caminhoImagem   = "/home/dev_peca_agora/Downloads/predador.jpg";
            if (file_exists($caminhoImagem)) {
             $imagem = Imagens::findOne(['id'=>5371]);
             $imagem->produto_id         = 6661;
             $imagem->imagem             = base64_encode(file_get_contents($caminhoImagem));
             $imagem->imagem_sem_logo    = base64_encode(file_get_contents($caminhoImagem));
             $imagem->ordem              = 1;
             $imagem->save();
        }*/
        
        /*//Teste investimento
        $valor_mensal   = 1250*0.08;
        $valor_corrente  = 0;
        for($x=1;$x<=12;$x++){
           
            $valor_corrente += 0.0025*$valor_corrente + $valor_mensal;
            echo "\n".$valor_corrente;

            if(($x%18)==0){
                $valor_mensal = $valor_mensal*1.04;
                echo " - ".$valor_mensal;
            }
            
        }
        
        echo "\n".$valor_corrente."\n";*/


        /*//Teste simulação salários e impostos
        $inss = [[0, 8], [1751.82, 9], [2919.73,11]]; 
        $ir = [[0, 0], [1903.99, 7.5], [2826.66,15], [3751.06,22.5], [4664.68,27.5]]; 
        
        for($x=900;$x<=30000;$x++){
            echo "\n".$x." - ";
            
            $inss_desconto = 0;
            $ir_desconto = 0;
            
            foreach($inss as $k => $faixa){
                if(isset($inss[($k+1)])){
                    if($x >= $faixa[0] && $x< $inss[($k+1)][0]){
                        $inss_desconto = ($x*($faixa[1]/100));
                    }
                }
                else{
                    if($x >= $faixa[0]){
                        $inss_desconto = ($x*($faixa[1]/100));
                    }
                }
            }
            
            foreach($ir as $k => $faixa){
                if(isset($inss[($k+1)])){
                    if($x >= $faixa[0] && $x< $ir[($k+1)][0]){
                        $ir_desconto = ($x*($faixa[1]/100));
                    }
                }
                else{
                    if($x >= $faixa[0]){
                        $ir_desconto = ($x*($faixa[1]/100));
                    }
                }
            }
            
            $valor_final = $x - $inss_desconto - $ir_desconto;
            echo $valor_final;

        }*/

        /*unlink("/var/tmp/amazon_precos_quantidade.csv");
        $arquivo = fopen("/var/tmp/amazon_precos_quantidade.csv", "a");
        fwrite($arquivo, "sku".chr(9)."price".chr(9)."minimum-seller-allowed-price".chr(9)."maximum-seller-allowed-price".chr(9)."quantity");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/produtos_pecaagora_11-01-2020.csv", 'r'); //Abre arquivo com preços para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];
            
            if($i == 0){
                continue;
            }
            
            $preco = round($linhaArray[13], 2);
            $preco = str_replace(".",",",$preco);
            
            if($preco == 0){
                echo " - ZERADO";
                continue;
            }
            
            fwrite($arquivo, "\n".$linhaArray[1].chr(9).$preco.chr(9)."".chr(9)."".chr(9).$linhaArray[12]);
        }
        
        fclose($arquivo);*/
        
        /*$valor_inicial      = 0;
        $valor_final        = 1000000;
        $valor_mensal       = 2000;
        $rendimento_mensal  = 1.005;
        $valor_corrente     = $valor_inicial;
        $quantidade_meses   = 0;
        
        while($valor_corrente <= $valor_final){
            echo "\n".$quantidade_meses." - ".$valor_corrente;    
            $quantidade_meses++;
            $valor_corrente = ($valor_corrente * $rendimento_mensal) + $valor_mensal;
        }
        
        echo "\nValor Inicial:      ".$valor_inicial;
        echo "\nValor Final:        ".$valor_final;
        echo "\nValor Mensal:       ".$valor_mensal;
        echo "\nRendimento Mensal:    ".(($rendimento_mensal-1)*100)."%";
        echo "\nQuantidade de Meses:  ".$quantidade_meses;
        echo "\nValor Total:          ".$valor_corrente;
        
        $valor_x = pow($rendimento_mensal,$quantidade_meses)*$valor_inicial;
        $valor_y = 0;
        
        for($i=0 ; $i < $quantidade_meses ; $i++){
            $valor_y += pow($rendimento_mensal,$i) * $valor_mensal;
        }
        
        $valor_z = $valor_x + $valor_y;
        echo "\nValor por fórmula:  ".$valor_z;

        $quantidade_meses = 120;
        $valor_x = pow($rendimento_mensal,$quantidade_meses)*$valor_inicial;
        $valor_y = 0;
        
        for($i=0 ; $i < $quantidade_meses ; $i++){
            $valor_y += pow($rendimento_mensal,$i);
        }
        
        $valor_z = (($valor_final - $valor_x) / $valor_y);
        echo "\nValor mensal para chegar a ".$valor_final.":  ".$valor_z;
       
        /*$valor_inicial      = 0;
        $valor_final        = 100000;
        $valor_mensal       = 2000;
        $rendimento_mensal  = 1.005;
        $valor_corrente     = $valor_inicial;
        $quantidade_meses   = 0;*/
        
        //$produto = Produto::find()->andWhere(['=','id',3055])->one();
        //echo "\n\n".$produto->getUrlImageML()."\n\n";*/
        
        /*$imagem     = Imagens::find()->andWhere(['=','produto_id',58093])->one();
        if($imagem){
            echo "Imagem encontrada";
            
            $caminho    = "https://www.pecaagora.com/site/get-link?produto_id=".$imagem->produto_id."&ordem=".$imagem->ordem;
            copy($caminho, '/var/www/html/pecaagora/frontend/web/assets/img/imagens_temporarias/'.$imagem->id.".webp" );
        
            $resultado = shell_exec('cd /var/www/html/pecaagora/frontend/web/assets/img/imagens_temporarias ; mogrify -format jpg *.webp');
            echo $resultado;
        }
        else{
            echo "Imagem não encontrada";
        }*/
        
	//Verifica Vannucci, se existem os produtos
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_vannucci_produtos_nao_existem_02-07-2020.csv", "a");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];

	    fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'"');

            if($i == 0){
                continue;
            }

	    //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
	    $produto = Produto::find()->andWhere(['like', 'codigo_global', $linhaArray[4]])->one();

            if($produto){
		fwrite($arquivo, ';"Produto encontrado"');
            }
	    else{
		fwrite($arquivo, ';"Produto nao encontrado"');
	    }
        }

        fclose($arquivo);*/



	//Verifica Vannucci, se existem os produtos
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_produto_b2w_conferir_ean_07-07-2020.csv", "a");

        $LinhasArray = Array();
        $file = fopen("/var/tmp/produto_b2w_conferir_ean_07-07-2020.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);

        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];

            fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'";"'.$linhaArray[4].'";"'.$linhaArray[5].'";"'.$linhaArray[6].'";"'.$linhaArray[7].'";"'.$linhaArray[8].'";"'.$linhaArray[9].'"');

            if($i == 0){
                continue;
            }

            //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $linhaArray[2]])->one();

            if($produto_filial){
                fwrite($arquivo, ';"'.$produto_filial->produto->nome.'";"Produto encontrado"');
            }
            else{
		 fwrite($arquivo, ';;"Produto nao encontrado"');
            }
        }

        fclose($arquivo);

        print_r(date("Y-m-d_H-i-s"));
        

        echo "\n\nFIM TESTE";*/


	//Gera planilha dos produtos da Amzon, para zerar as filiais sem ser a Vannucci, BR, Dib, Morelate e LNG
        //unlink("/var/tmp/vannucci_produtos_nao_existem_02-07-2020.csv");
        /*$arquivo = fopen("/var/tmp/log_produtos_amazon.csv", "a");
        
        $LinhasArray = Array();
        $file = fopen("/var/tmp/produtos-amazon.csv", 'r'); //Abre arquivo com pre  os para subir
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line; //Popula um array com os dados do arquivo, para facilitar o processamento
        }
        fclose($file);
        
        foreach ($LinhasArray as $i => &$linhaArray){
            echo "\n".$i." - ".$linhaArray[1];
            
            fwrite($arquivo, "\n".'"'.$linhaArray[0].'";"'.$linhaArray[1].'";"'.$linhaArray[2].'";"'.$linhaArray[3].'"');
            
            if($i == 0){
                fwrite($arquivo, ";quantidade_atual;status;filial_id");
                continue;
            }
            
            //$produto = Produto::find()->andWhere(['like', 'codigo_fabricante', $linhaArray[4]])->one();
            $produto_filial = ProdutoFilial::find()->andWhere(['=', 'id', $linhaArray[0]])->one();
            
            if($produto_filial){
                
                $quantidade = 0;
                if($produto_filial->filial_id == 38 || $produto_filial->filial_id == 43 || $produto_filial->filial_id == 60 || $produto_filial->filial_id == 72 || $produto_filial->filial_id == 97){
                    $quantidade = $produto_filial->quantidade;
                }

                fwrite($arquivo, ';'.$quantidade.';"Produto encontrado";'.$produto_filial->filial_id);
            }
            else{
                fwrite($arquivo, ';0;"Produto nao encontrado"');
            }
        }
        
        fclose($arquivo);
        
        print_r(date("Y-m-d_H-i-s"));*/


    	//CRIAR CAIXAS GAUSS
	/*$caixas = [10, 20, 50, 100, 150, 200];
    	
    	$produtos_gauss = Produto::find()  ->andWhere(['=','fabricante_id', 86])
                                           ->andWhere(['not like','codigo_fabricante','CX.']) 
    	                                   ->orderBy('codigo_fabricante')
    	                                   ->all();
    	
        foreach($produtos_gauss as $k => $produto_gauss){
            
            echo "\n".$k." - ".$produto_gauss->id." - ".$produto_gauss->codigo_fabricante;
            
            foreach($caixas as $caixa){
                
                echo "\n".$caixa;
                
                $produto_caixa = Produto::find()->andWhere(['=', "codigo_fabricante", "CX.".$produto_gauss->codigo_fabricante."-".$caixa])->one();
                
                if($produto_caixa){
                    echo " - Produto caixa encontrado - ".$produto_caixa->codigo_fabricante;
                    
                    $produto_filial_caixa = ProdutoFilial::find()->andWhere(['=','produto_id', $produto_caixa->id])->one();
                    if($produto_filial_caixa){
                        echo " - Estoque caixa encontrado";
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_gauss->id])
                                                                ->andWhere(['=','filial_id', 96])
                                                                ->one();
                        
                        if($produto_filial){
                            $produto_filial_caixa->quantidade = $produto_filial->quantidade;
                        }
                        else{
                            $produto_filial_caixa->quantidade = 0;
                        }
                        
                        if($produto_filial_caixa->save()){
                            echo " - Estoque Alterado";
                        }
                        else{
                            echo " - Estoque NÃO Alterado";
                        }
                        
                    }
                    else{
                        echo " - Estoque caixa NÃO encontrado";
                    }
                }
                else{
                    echo " - Produto caixa não encontrado";
                    
                    $produto_novo                           = new Produto;
                    //$produto_novo                         = $produto_gauss;
                    $produto_novo->codigo_global            = "CX.".$produto_gauss->codigo_global."-".$caixa;
                    $produto_novo->codigo_fabricante        = "CX.".$produto_gauss->codigo_fabricante."-".$caixa;
                    $produto_novo->nome                     = "CAIXA ".$caixa." ".$produto_gauss->nome;
                    $produto_novo->multiplicador            = $caixa;
                    $produto_novo->peso                     = $produto_gauss->peso * $caixa;
                    $produto_novo->largura                  = $produto_gauss->largura * $caixa;
                    $produto_novo->altura                   = $produto_gauss->altura;
                    $produto_novo->profundidade             = $produto_gauss->profundidade;
                    $produto_novo->codigo_montadora         = $produto_gauss->codigo_montadora;
                    $produto_novo->fabricante_id            = $produto_gauss->fabricante_id;
                    $produto_novo->slug                     = $produto_gauss->slug;
                    $produto_novo->subcategoria_id          = $produto_gauss->subcategoria_id;
                    $produto_novo->aplicacao                = $produto_gauss->aplicacao;
                    $produto_novo->texto_vetor              = $produto_gauss->texto_vetor;
                    $produto_novo->codigo_similar           = $produto_gauss->codigo_similar;
                    $produto_novo->aplicacao_complementar   = $produto_gauss->aplicacao_complementar;
                    $produto_novo->video                    = $produto_gauss->video;
                    $produto_novo->codigo_barras            = $produto_gauss->codigo_barras;
                    $produto_novo->cest                     = $produto_gauss->cest;
                    $produto_novo->ipi                      = $produto_gauss->ipi;
                    $produto_novo->e_usado                  = $produto_gauss->e_usado;
                    $produto_novo->e_medidas_conferidas     = $produto_gauss->e_medidas_conferidas;
                    $produto_novo->e_ativo                  = $produto_gauss->e_ativo;
                    $produto_novo->e_valor_bloqueado        = $produto_gauss->e_valor_bloqueado;
                    $produto_novo->marca_produto_id         = $produto_gauss->marca_produto_id;
                    $produto_novo->produto_condicao_id      = 1;
                    $produto_novo->codigo_fornecedor        = $produto_gauss->codigo_fornecedor;
                    
                    
                    //print_r($produto_gauss); 
                    //print_r($produto_novo);
                    if($produto_novo->save()){
                        echo " - Produto CRIADO";

                        $produto_filial_novo                = new ProdutoFilial;
                        
                        $produto_filial = ProdutoFilial::find() ->andWhere(['=','produto_id', $produto_gauss->id])
                                                                ->andWhere(['=','filial_id', 96])
                                                                ->one();
                        
                        if($produto_filial){
                            echo " - Estoque encontrado";
                            $produto_filial_novo->quantidade = $produto_filial->quantidade;
                        }
                        else{
                            echo " - Estoque não encontrado";
                            $produto_filial_novo->quantidade = 0;
                        }
                        
                        $produto_filial_novo->produto_id    = $produto_novo->id;
                        $produto_filial_novo->filial_id     = 96;
                        $produto_filial_novo->envio         = 1;
                        if($produto_filial_novo->save()){
                            echo " - Estoque CRIADO";
                        }
                        else{
                            echo " - Estoque NÃO CRIADO";
                        }
                    }
                    else{
                        echo " - Produto NÃO CRIADO";
                    }
                    //die;
                }
            }
        }*/

	/*$emails_destinatarios	= ['wagnercampaljf@yahoo.com.br', 'dev.pecaagora@gmail.com'];
	$assunto		= "Teste Imagem";
	//$email_texto		= "https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2";
	//$email_texto          	= "<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>";
	$email_texto		= "Boa tarde, Como vai?

Segue dados de um novo pedido a ser faturado. Nosso financeiro ira realizar o pagamento conforme combinado anteriormente.

<img src='https://www.pecaagora.com/site/get-link?produto_id=257500&ordem=2'>

Cod.:
Descricao: SUPER BONDER LOCTITE PRECISAO 10G DOBRO DE PRODUTO (BONDER10)
Quantidade:  * 1  Unidade
Valor: estoque

Envio: Carmópolis de Minas, 963, Vila Maria.


Atenciosamente,


Peca Agora
Site: https://www.pecaagora.com/
E-mail: compras.pecaagora@gmail.com
Setor de Compras:(32)3015-0023
Whatsapp:(32)988354007
Skype: pecaagora";

	var_dump(\Yii::$app->mailer   ->compose()
	         	              ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
        	 	              //->setTo(["wagnercampaljf@yahoo.com.br","dev.pecaagora@gmail.com","compras.pecaagora@gmail.com","dev2.pecaagora@gmail.com"])
        	 	              ->setTo($emails_destinatarios)
        	 	              //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
        	 	              ->setSubject($assunto)
        	 	              //->setTextBody($email_texto)
        	 	              ->setHtmlBody($email_texto)
        	 	              ->send());*/

	/*$emails_destinatarios = ["wagnercampaljf@yahoo.com.br"];
        $assunto = "teste remetente";
        $email_texto = "teste remetente texto";
        
        var_dump(\Yii::$app->mailer   ->compose()
            ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
            // ->setTo(["dev2.pecaagora@gmail.com"])
            ->setTo($emails_destinatarios)
            //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
            ->setSubject($assunto)
            ->setTextBody($email_texto)
            //->setHtmlBody($email_texto)
            ->send());*/

	//$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
	//print_r($produto_filial->atualizarMLPreco());
	//die;

/*	$imagem = Imagens::find()->andWhere(['=', 'produto_id', 381989])->one();
	print_r($imagem);

	$imagem_nova = new Imagens();
	$imagem_nova->produto_id = $imagem->produto_id;
	$imagem_nova->imagem = $imagem->imagem;
	$imagem_nova->imagem_sem_logo = $imagem->imagem_sem_logo;
	$imagem_nova->ordem = 2;
	var_dump($imagem_nova->save());*/

	/*$imagens = Imagens::find()->select(["id", "produto_id"])->all();

	foreach($imagens as $k => $imagem){
		if(substr(sprintf('%o', fileperms('/var/www/imagens_produto/produto_'.$imagem->produto_id)), -4) != "0777"){
			echo "\n".$imagem->id." - ".substr(sprintf('%o', fileperms('/var/www/imagens_produto/produto_'.$imagem->produto_id)), -4);
		}
	}*/
	//echo substr(sprintf('%o', fileperms('/var/www/imagens_produto')), -4);

	//echo PedidoMercadoLivre::baixarPedidoML($q);

	//$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 1005])->all(); //KFIBRAQ
	//$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 493])->all(); //FIBRAAS
	//$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 951])->all(); //PUES
	/*$produtos = Produto::find()->andWhere(["=", "marca_produto_id", 1176])->all(); //KFIBRAQ
	foreach($produtos as $produto){
		echo "\n".$produto->id;

		$produto_filial = ProdutoFilial::find() ->andwhere(["=", "produto_id", $produto->id])
							->andWhere(["=", "filial_id", 96])
							->one();
		if($produto_filial){
			echo " - ".$produto_filial->id;
			$valor_produto_filial = ValorProdutoFilial::find()->andwhere(["=", "produto_filial_id", $produto_filial->id])
									  ->orderBy(["dt_inicio" => SORT_DESC])
									  ->one();
			if($valor_produto_filial){
				echo " - ".$valor_produto_filial->valor;
				$produto_filial_fibra = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto->id])
										//->andwhere(["=", "filial_id", 87])
										//->andwhere(["=", "filial_id", 88])
										//->andwhere(["=", "filial_id", 89])
										->andwhere(["=", "filial_id", 90])
										->one();
				echo " - ".$produto_filial_fibra->id;
				$valor_produto_filial_novo = new ValorProdutoFilial;
				$valor_produto_filial_novo->valor = $valor_produto_filial->valor;
				$valor_produto_filial_novo->valor_cnpj = $valor_produto_filial->valor_cnpj;
				$valor_produto_filial_novo->valor_compra = $valor_produto_filial->valor_compra;
				$valor_produto_filial_novo->produto_filial_id = $produto_filial_fibra->id;
				$valor_produto_filial_novo->dt_inicio = date("Y-m-d H:i:s");
				var_dump($valor_produto_filial_novo->save());
			}
		}
	}*/

        /*$produtos_filiais = ProdutoFilial::find()->where(" produto_id in (select produto_id from produto_filial where filial_id in (87, 88, 89, 90)) and filial_id = 8 ")->all();
        
        foreach($produtos_filiais as $k => $produto_filial){
            echo "\n".$k." - ".$produto_filial->meli_id;
            
            $produto_filial_fibra = ProdutoFilial::find()   ->andWhere(["=", "produto_id", $produto_filial->produto_id])
                                                            ->andWhere(["filial_id" => [87, 88, 89, 90]])
                                                            ->one();

            if($produto_filial_fibra){
                echo " - Produto fibra encontrado";
                
                $produto_filial_fibra->meli_id = $produto_filial->meli_id;
                if($produto_filial_fibra->save()){
                    echo " - Fibra alterado";
                    
                    $pedidos_mercado_livre_produtos = PedidoMercadoLivreProduto::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_mercado_livre_produtos as $i => $pedido_mercado_livre_produto){
                        echo "\n".$i." - ".$pedido_mercado_livre_produto->id;
                        $pedido_mercado_livre_produto->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_mercado_livre_produto->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

                    $pedidos_mercado_livre_produtos_produtos_filiais = PedidoMercadoLivreProdutoProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_mercado_livre_produtos_produtos_filiais as $i => $pedido_mercado_livre_produto_produto_filial){
                        echo "\n".$i." - ".$pedido_mercado_livre_produto_produto_filial->id;
                        $pedido_mercado_livre_produto_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_mercado_livre_produto_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

		    $pedidos_compra_produtos_filiais = PedidoCompraProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_compra_produtos_filiais as $i => $pedido_compra_produto_filial){
                        echo "\n".$i." - ".$pedido_compra_produto_filial->id;
                        $pedido_compra_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_compra_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }
                    
                    $pedidos_produtos_filiais = PedidoProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->all();
                    foreach($pedidos_produtos_filiais as $i => $pedido_produto_filial){
                        echo "\n".$i." - ".$pedido_produto_filial->id;
                        $pedido_produto_filial->produto_filial_id = $produto_filial_fibra->id;
                        if($pedido_produto_filial->save()){
                            echo " - pedido alterado";
                        }
                        else{
                            echo " - pedido não alterado";
                        }
                    }

                    if($produto_filial->delete()){
                        echo "\nProduto Casada deletado";
                    }
                    else{
                        echo "\nProduto Casada deletado";
                    }
                }
                else{
                    echo "\nFibra não alterado";
                }
            }
            else{
                echo " - Produto fibra não encontrado";
            }
        }*/


	/*$pedidos_mercado_livre_shipments = PedidoMercadoLivreShipments::find()  ->andWhere(["<>", "status", "delivered"])
										->andWhere([">=", "id", 21543])
										->orderBy(["id"=>SORT_DESC])
										->all();

        foreach($pedidos_mercado_livre_shipments as $k => $pedido_mercado_livre_shipment){
            echo "\n".$k." - ".$pedido_mercado_livre_shipment->id." - ".$pedido_mercado_livre_shipment->status;

	    $pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(["=", "id", $pedido_mercado_livre_shipment->pedido_mercado_livre_id])->one();

	    echo " - ".$pedido_mercado_livre->id;

	    echo PedidoMercadoLivre::AtualizarPedidoML($pedido_mercado_livre->pedido_meli_id);
	    //echo PedidoMercadoLivre::baixarPedidoML($pedido_mercado_livre->pedido_meli_id);
        }*/

	/*$arquivo_origem = fopen("/var/tmp/preco_fibras_venda_casada_2021-07-02_02.csv", 'r');
        
        $x = 0;
        
        while (($line = fgetcsv($arquivo_origem,null,';')) !== false){
            
            echo "\n".$x++." - ".$line[0];
            
            if($x <= 1){
                continue;
            }
            
            $produtos_filiais = ProdutoFilial::find() ->andWhere(['=', 'produto_id', $line[1]])
                                                    ->andWhere(['filial_id' => [87, 88, 89, 90]])
                                                    ->all();
            foreach($produtos_filiais as $k => $produto_filial){

                $valor_produto_filial_verificacao = ValorProdutoFilial::find()->andWhere(["=", "produto_filial_id", $produto_filial->id])->one();
                
                if($valor_produto_filial_verificacao){
                    continue;
                }
                
                echo "\n    ".$produto_filial->id; 
                
                $valor_produto_filial                       = new ValorProdutoFilial;
                $valor_produto_filial->produto_filial_id    = $produto_filial->id;
                $valor_produto_filial->valor                = $line[16];
                $valor_produto_filial->valor_cnpj           = $line[21];
                $valor_produto_filial->valor_compra         = $line[22];
                $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");
                if($valor_produto_filial->save()){
                    echo " - Valor criado";
                }
            }
        }

        fclose($arquivo_origem);*/

	//Atualizar produtos de MG
	$produtos_filiais = ProdutoFilial::find()   ->andWhere(["filial_id" => [94, 99]])
						    ->andWhere(["is not", "meli_id", null])
						    //->andWhere(["=", "meli_id", "MLB1832672890"])
                                                    ->orderBy(["id" => SORT_DESC])
                                                    ->all();
        
        foreach($produtos_filiais as $k => $produto_filial){
            print_r($produto_filial->atualizarMLPreco());
        }
	die;

	//TESTE ETIQUETA
	/*$pedido_mercado_livre = PedidoMercadoLivre::find()->andWhere(['=', 'pedido_meli_id', '4722970744'])->one();
	$filial = Filial::find()->andWhere(['=','id',72])->one();
        $meli = new Meli("3029992417140266", "1DjEfVPwkjOmqowN1ALujTJpgQh5DGdh");
	$user = $meli->refreshAccessToken($filial->refresh_token_meli);
        $response = ArrayHelper::getValue($user, 'body');
        $meliAccessToken = $response->access_token;

        $url = "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$pedido_mercado_livre->shipping_id."&access_token=" . $meliAccessToken;
	echo "\n\n".$url."\n\n";
	die;*/

	//$produto = Produto::find()->andWhere(["=", "id", 283586])->one();
	//print_r(FunctionsML::atualizarMercadoLivre($produto)); die;

	//Atualizar descrições
        $produtos_filiais = ProdutoFilial::find()   ->andWhere(["<>", "filial_id", 98])
                                                    ->andWhere(["is not", "meli_id", null])
                                                    //->andWhere(["=", "meli_id", "MLB1570630064"])
                                                    ->orderBy(["id" => SORT_DESC])
                                                    ->all();

        foreach($produtos_filiais as $k => $produto_filial){
	    break;
            echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->produto->nome;
	    if($k < 23801){
		echo " - pular";
	    }
	    else{
		print_r(FunctionsML::atualizarDescricao($produto_filial->produto));
		//die;
	    }
	    //die;
        }

	$produto_filial = ProdutoFilial::find()->andWhere(["=", "id", $q])->one();
 	print_r($produto_filial->atualizarMLPreco());
        die;

	/*$produto_filiais = ProdutoFilial::find()->andWhere(['filial_id'=>[87, 88, 89, 90]])->all();
    	foreach ($produto_filiais as $k => $produto_filial){
    	    echo "\n".$k." - ".$produto_filial->id." - ".$produto_filial->produto_id;
    	    $produto = Produto::find()->andWhere(["=", "id", $produto_filial->produto_id])->one();
    	    $produto->dias_expedicao = 2;
    	    var_dump($produto->save());
    	}
    	die;*/

	$pedidos_mercado_livre = PedidoMercadoLivre::find()	->andWhere(["pedido_meli_id" => [$q]])
								//->andWhere(["=", "id", 31717])
								->orderBy(["id" => SORT_ASC])
								->all();

	foreach($pedidos_mercado_livre as $k => $pedido_mercado_livre ){
		echo "\n".$k." - ".$pedido_mercado_livre->id." - ".$pedido_mercado_livre->pedido_meli_id;
		echo PedidoMercadoLivre::baixarPedidoML($pedido_mercado_livre->pedido_meli_id, false);
	}

    }
}








