<?php

namespace console\controllers\actions\funcoesgerais;

use yii\base\Action;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;
use common\models\Imagens;

class ImportacaoUniversalAction extends Action
{
    public function run(){

        echo "INÍCIO da rotina de criação preço: \n\n";
        
        $LinhasArray = Array();
        $file = fopen('/var/tmp/produtos_universal.csv', 'r');
        while (($line = fgetcsv($file,null,';')) !== false)
        {
            $LinhasArray[] = $line;
        }
        fclose($file);
        
        if (file_exists("/var/tmp/log_importar_universal.csv")){
            unlink("/var/tmp/log_importar_universal.csv");
        }
        
        $arquivo_log = fopen("/var/tmp/log_importar_universal.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "codigo_fabricante;ncm;observacao;codigo_global;numero_caracteres;nome;aplicacao;valor;peso;altura;largura;profundidade;subcategoria_id;preco_venda;status\n");
        
        //$transaction = Yii::$app->db->beginTransaction();
        
        foreach ($LinhasArray as $k => &$linhaArray ){
            
            echo $k." - ";
            
            if($k==0){
                continue;
            }
            
            $produto = new Produto();
            $produto->codigo_fabricante = $linhaArray[0];
            $produto->codigo_montadora  = $linhaArray[10];
            $produto->codigo_global     = $linhaArray[16]."-".$k;
            $produto->nome              = $linhaArray[2];
            $produto->aplicacao         = $linhaArray[3];
            $produto->peso              = str_replace(',','.',$linhaArray[11]);
            $produto->altura            = str_replace(',','.',$linhaArray[12]);
            $produto->largura           = str_replace(',','.',$linhaArray[14]);
            $produto->profundidade      = str_replace(',','.',$linhaArray[13]);
            $produto->codigo_barras     = $linhaArray[15];
            $produto->subcategoria_id   = $linhaArray[18]; 
            $produto->fabricante_id     = 109;
            //print_r($produto);
            if ($produto->save()){
                echo "Produto CRIADO\n";
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";"."Produto CRIADO");
            } else{
                fwrite($arquivo_log, $linhaArray[0].";".$linhaArray[1].";".$linhaArray[2].";".$linhaArray[3].";".$linhaArray[4].";".$linhaArray[5].";".$linhaArray[6].";".$linhaArray[7].";".$linhaArray[8].";".$linhaArray[9].";".$linhaArray[10].";".$linhaArray[11].";".$linhaArray[12].";".$linhaArray[13].";"."Produto NAO CRIADO\n");
                echo "Produto NÃO CRIADO\n";
                continue;
            }
            
            $produtoFilial              = new ProdutoFilial();
            $produtoFilial->produto_id  = $produto->id;
            $produtoFilial->filial_id   = 96;
            $produtoFilial->quantidade  = 99999;
            $produtoFilial->envio       = 1;
            if ($produtoFilial->save()){
                fwrite($arquivo_log, " - ProdutoFilial CRIADO\n");
            } else{
                fwrite($arquivo_log, " - ProdutoFilial NAO CRIADO\n");
                continue;
            }
            
            $valorProdutoFilial                     = New ValorProdutoFilial;
            $valorProdutoFilial->produto_filial_id  = $produtoFilial->id;
            $valorProdutoFilial->valor              = (float)str_replace(",",".",$linhaArray[28]);
            $valorProdutoFilial->valor_cnpj         = (float)str_replace(",",".",$linhaArray[28]);
            $valorProdutoFilial->dt_inicio          = date("Y-m-d H:i:s");
            //var_dump($valorProdutoFilial);
            if ($valorProdutoFilial->save()){
                fwrite($arquivo_log, " - ValorProdutoFilial CRIADO\n");
            } else{
                fwrite($arquivo_log, " - ValorProdutoFilial NAO CRIADO\n");
                continue;
            }

            $caminhoImagemComLogo   = "/var/tmp/fotos_universal/foto_universal_com_logo/".$produto->codigo_fabricante.".jpg";
            $caminhoImagemSemLogo   = "/var/tmp/fotos_universal/foto_universal_sem_logo/".$produto->codigo_fabricante.".jpg";
            
            if (file_exists($caminhoImagemComLogo)) {
                echo $caminhoImagemComLogo." - EXISTE\n";
                $imagem = new Imagens();
                $imagem->produto_id         = $produtoFilial->produto->id;
                $imagem->imagem             = base64_encode(file_get_contents($caminhoImagemComLogo));
                $imagem->imagem_sem_logo    = (file_exists($caminhoImagemSemLogo) ? base64_encode(file_get_contents($caminhoImagemSemLogo)) : null);
                $imagem->ordem              = 1;
                $imagem->save();
            } 
            
            /*$caminhoImagemSemLogo   = "/home/dev_peca_agora/fotos_universal_copia/foto_universal_sem_logo/".$linhaArray[0].".jpg";
            if (file_exists($caminhoImagemSemLogo)) {
                echo $caminhoImagemSemLogo." - EXISTE - ";
                $caminhoImagemSemLogoNovo = '/var/tmp/fotos_universal/foto_universal_sem_logo/'.$linhaArray[0].'.jpg';
                rename($caminhoImagemSemLogo,$caminhoImagemSemLogoNovo);
                
                $caminhoImagemComLogo = "/home/dev_peca_agora/fotos_universal_copia/foto_universal_com_logo/".$linhaArray[0].".jpg";
                if (file_exists($caminhoImagemComLogo)) {
                    echo $caminhoImagemComLogo." - EXISTE\n";
                    $caminhoImagemComLogoNovo = '/var/tmp/fotos_universal/foto_universal_com_logo/'.$linhaArray[0].'.jpg';
                    rename($caminhoImagemComLogo,$caminhoImagemComLogoNovo);
                } else{
                    echo $caminhoImagemComLogo." - NÃO EXISTE\n";
                }
            } else {
                echo $caminhoImagemSemLogo." - NÃO EXISTE\n";
            }*/
        }
            
        fclose($arquivo_log); 
        
        //print_r($LinhasArray);
        
        echo "\n\nFIM da rotina de criação preço!";
    }
}
