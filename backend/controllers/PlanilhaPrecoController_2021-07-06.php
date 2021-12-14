<?php

namespace backend\controllers;

use common\models\Filial;
use common\models\MarkupDetalhe;
use frontend\models\MarkupMestre;
use Yii;
use yii\web\Response;
use yii\web\UploadedFile;
use common\models\PlanilhaPreco;
use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\ValorProdutoFilial;


class PlanilhaPrecoController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionPlanilha()
    {
        $model = new PlanilhaPreco();
        //var_dump($model);

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            //print_r($post);die;


            if ($post['PlanilhaPreco']['id'] ==8){


                //print_r( $post['PlanilhaPreco']['coluna_codigo_fabricante']); die();
                $model->file_planilha = UploadedFile::getInstance($model, 'file_planilha');

                if ($model->validate()) {
                    //chmod ('uploads/', 0777);
                    //$model->file_planilha->saveAs('uploads/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension );


                    echo  'rodar funçaõ ';
                    //echo "INÍCIO da rotina de criação preço: \n\n";

                    $faixas = array();

                    $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

                    $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();


                    $faixas = [];

                    foreach ($markups_detalhe as $markup_detalhe){
                        $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

                    }

                    $LinhasArray = Array();

                    $arquivo_origem = $model->file_planilha->saveAs('/var/tmp/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension );



                    $file = fopen('/var/tmp/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension, 'r');

                    while (($line = fgetcsv($file,null,';')) !== false)
                    {
                        $LinhasArray[] = $line;
                    }
                    fclose($file);

                    $destino = '/var/tmp/' . $model->file_planilha->baseName ."_precificado.csv";
                    if (file_exists($destino)){
                        unlink($destino);
                    }

                    $arquivo_destino = fopen($destino, "a");

                    foreach ($LinhasArray as $i => &$linhaArray){

                        echo "\n".$i." - ".$linhaArray[3]. " - ".$linhaArray[6];

                        $novo_codigo_fabricante = $linhaArray[$post['PlanilhaPreco']['coluna_codigo_fabricante']];



                       // print_r($novo_codigo_fabricante);die;



                        fwrite($arquivo_destino, "\n".$novo_codigo_fabricante.";");


                        //var_dump($arquivo_destino); die();
                        //Acrescenta mais duas colunas
                        /*if ($i == 0){

                            fwrite($arquivo_destino, "7;8");

                            continue;

                        }*/

                        if ($i <= 0){

                            fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");

                            continue;

                        }


                        $preco_compra_planilha   = $linhaArray[$post['PlanilhaPreco']['coluna_preco']];

                        $preco_compra   = (float) str_replace(",",".",str_replace(".","",$preco_compra_planilha));

                        $multiplicador 	= 1;
                        $produto		= Produto::find()->andWhere(['=','codigo_fabricante','D'.$novo_codigo_fabricante])->one();

                        if($produto){
                            $preco_compra += $this->calcular_impostos($preco_compra, $produto->marca_produto_id, $produto->ipi);


                            if(!is_null($produto->multiplicador)){

                                if($produto->multiplicador > 1 ){

                                    $multiplicador = $produto->multiplicador;

                                }

                            }

                        }


                        $preco_compra = $multiplicador * $preco_compra;

                        echo "\n".$i." - ".$preco_compra;



                        echo " - ".$preco_compra;



                        foreach ($faixas as $k => $faixa) {

                            if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){

                                $preco_venda = round(($preco_compra * $faixa[2]),2);


                                if ($faixa[3]){

                                    $preco_venda = $faixa[2];

                                }

                                fwrite($arquivo_destino, $preco_compra.";".$preco_venda);

                                break;

                            }

                        }

                        // Verifica se existe produto caixas
                        $produto_caixa  = Produto::find()->andWhere(['=','codigo_fabricante','CX.D'.$novo_codigo_fabricante])->one();

                        if($produto_caixa){

                            $preco_compra       = $produto_caixa->multiplicador * $preco_compra;

                            $codigo_fabricante  = 'CX.D'.$novo_codigo_fabricante;

                            foreach ($faixas as $k => $faixa) {

                                if($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]){

                                    $preco_venda = round(($preco_compra * $faixa[2]),2);

                                    if ($faixa[3]){
                                        $preco_venda = $faixa[2];
                                    }
                                    fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$preco_compra.";".$preco_venda);
                                    break;

                                }

                            }

                        }

                    }



                    $filial_nome=' Pea venda casada';
                    $assunto = 'Planilha Atualização de preço'.' - '.$filial_nome;

                    $email_texto = 'Segue em anexo a Planilha de atualização de preço';

                    if($arquivo_destino) {

                        if (Yii::$app->request->isPost) {
                            //$email =  Yii::$app->request->post('email');
                            //$message = Yii::$app->request->post('message');
                            //$file_attachment = UploadedFile::getInstance($model, 'file_planilha');
                            $model->file_planilha = UploadedFile::getInstance($model, 'file_planilha');
                            if ($model->file_planilha) {
                                $mail = Yii::$app->mailer->compose()
                                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                                    ->setTo(["dev2.pecaagora@gmail.com"])
                                    ->setSubject($assunto)
                                    ->setTextBody($email_texto);
                                foreach ($model->file_planilha as $file) {
                                    $filename = $destino = '/var/tmp/' . $model->file_planilha->baseName ."_precificado.csv";
                                    //$file->saveAs($filename);
                                    $mail->attach($filename);
                                }
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
                    }


                } else {
                    echo 'não salvo';
                }
            }
            elseif ($post['PlanilhaPreco']['id'] ==97){


                //print_r( $post['PlanilhaPreco']['coluna_codigo_fabricante']); die();
                $model->file_planilha = UploadedFile::getInstance($model, 'file_planilha');

                if ($model->validate()) {
                    echo "INÍCIO da rotina de criação preço: \n\n";

                    $faixas = array();



                    $markup_mestre      = MarkupMestre::find()->andWhere(['=','e_markup_padrao', true])->orderBy(["id" => SORT_DESC])->one();

                    $markups_detalhe = MarkupDetalhe::find()->andWhere(['=','markup_mestre_id',$markup_mestre->id])->all();

                    $faixas = [];

                    foreach ($markups_detalhe as $markup_detalhe){
                        $faixas [] = [$markup_detalhe->valor_minimo, $markup_detalhe->valor_maximo, $markup_detalhe->margem, $markup_detalhe->e_margem_absoluta];

                    }

                    //print_r($faixas);

                    $LinhasArray = Array();

                    $arquivo_origem = $model->file_planilha->saveAs('uploads/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension);


                    file_exists($arquivo_origem);

                    while (!file_exists($arquivo_origem)) {
                      continue;
                    }


                    //if (file_exists($arquivo_origem)) {


                        $file = fopen('uploads/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension, 'r');

                        while (($line = fgetcsv($file, null, ';')) !== false) {

                            $LinhasArray[] = $line;

                        }

                        fclose($file);

                        $destino = 'uploads/' . $model->file_planilha->baseName . '.' . $model->file_planilha->extension . "_precificado.csv";

                        if (file_exists($destino)) {

                            unlink($destino);

                        }

                        $arquivo_destino = fopen($destino, "a");

                        foreach ($LinhasArray as $i => &$linhaArray) {

                            $codigo_fabricante = $post['PlanilhaPreco']['coluna_codigo_fabricante'];

                            //fwrite($arquivo_destino, "\n".$codigo_fabricante.';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].";");

                            fwrite($arquivo_destino, "\n" . $codigo_fabricante . ';' . $linhaArray[1] . ';' . $linhaArray[2] . ';' . $linhaArray[3] . ';' . $linhaArray[4] . ';' . $linhaArray[5] . ';' . $linhaArray[6] . ";");

                            /*if ($i == 0){
                                fwrite($arquivo_destino, "8;9");
                                continue;
                            }*/

                            //Acrescenta mais duas colunas
                            if ($i == 0) {

                                fwrite($arquivo_destino, "7;8");

                                continue;

                            }

                            if ($i == 0) {

                                fwrite($arquivo_destino, "PREÇO COMPRA;PREÇO VENDA");

                                continue;

                            }

                            // Preco de compra


                            $preco_compra = (float)str_replace(",", ".", str_replace(".", "", $post['PlanilhaPreco']['coluna_preco']));

                            //$preco_compra   = $linhaArray[3];

                            $multiplicador = 1;

                            $produto = Produto::find()->andWhere(['=', 'codigo_fabricante', 'D' . $codigo_fabricante])->one();

                            if ($produto) {
                                $preco_compra += $this->calcular_impostos($preco_compra, $produto->marca_produto_id, $produto->ipi);


                                if (!is_null($produto->multiplicador)) {

                                    if ($produto->multiplicador > 1) {

                                        $multiplicador = $produto->multiplicador;

                                    }

                                }

                            }

                            $preco_compra = $multiplicador * $preco_compra;

                            echo "\n" . $i . " - " . $preco_compra;


                            //PREÇO CAPAS DE PELUCIA

                            if ($linhaArray[4] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[4] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[4] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[4] == "587-CAPAS CONFECCAO CORINO") {

                                //$preco_compra   = 0.55*$preco_compra;
                                echo 'capas pelucia';
                                continue;

                            } else {

                                $preco_compra = 0.45 * $preco_compra;

                            }

                            //$preco_compra   = 0.45*$preco_compra;

                            echo " - " . $preco_compra;

                            //$preco_compra = $preco_compra * 0.65;

                            // Percorre as faixas e os preços da planilha

                            foreach ($faixas as $k => $faixa) {

                                if ($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]) {

                                    $preco_venda = round(($preco_compra * $faixa[2]), 2);


                                    if ($faixa[3]) {

                                        $preco_venda = $faixa[2];

                                    }

                                    fwrite($arquivo_destino, $preco_compra . ";" . $preco_venda);

                                    break;

                                }

                            }

                            // Verifica se existe produto caixas
                            $produto_caixa = Produto::find()->andWhere(['=', 'codigo_fabricante', 'CX.D' . $codigo_fabricante])->one();

                            if ($produto_caixa) {

                                $preco_compra = $produto_caixa->multiplicador * $preco_compra;

                                $codigo_fabricante = 'CX.D' . $codigo_fabricante;

                                foreach ($faixas as $k => $faixa) {

                                    if ($preco_compra >= $faixa[0] && $preco_compra <= $faixa[1]) {

                                        $preco_venda = round(($preco_compra * $faixa[2]), 2);

                                        if ($faixa[3]) {

                                            $preco_venda = $faixa[2];

                                        }


                                        fwrite($arquivo_destino, "\n" . $codigo_fabricante . ';' . $linhaArray[1] . ';' . $linhaArray[2] . ';' . $linhaArray[3] . ';' . $linhaArray[4] . ';' . $linhaArray[5] . ';' . $linhaArray[6] . ';' . $preco_compra . ";" . $preco_venda);


                                        break;

                                    }

                                }

                            }

                        }
                    //}else {echo 'não salvou planilha';}

                } else {
                    echo 'não salvo';
                }



            }
            else{
                echo 'nem uma filial';
            }
            //switch
            /*switch ($post['PlanilhaPreco']['id']){
                case 4:
                    $this->actionMorelatePlanilha();
                    break;
                case 43:
                    $this->morelate();
                    break;
            }*/
        }

        return $this->render('planilha', ['model' => $model]);


    }

    public function calcular_impostos($preco_compra, $marca_produto_id, $ipi = 0){

        $valor_ipi = 0;

        if($ipi > 0){

            $valor_ipi = $preco_compra * ($ipi/100);

        }

        echo " - IPI: ".$valor_ipi;

        $valor_st = 0;



        echo " - ST: ".$valor_st;

        $valor_imposto = $valor_ipi + $valor_st;

        echo " - Valor Imposto: ".$valor_imposto;

        return $valor_imposto;

    }

    public function actionAtualizarPrecoPlanilha()
    {

        $model = new PlanilhaPreco();
        echo "INÍCIO da rotina de atualizacao do preço: \n\n";

        $LinhasArray = Array();


        $file = fopen('uploads/planilha_automatica_precificado.csv', 'r');





        while (($line = fgetcsv($file,null,';')) !== false)

        {

            $LinhasArray[] = $line;

        }

        fclose($file);

        // Vai logando
        $log = "uploads/log_planilha_automatica_precificado.csv";

        if (file_exists($log)){

            unlink($log);

        }

        $arquivo_log = fopen($log, "a");

        foreach ($LinhasArray as $i => &$linhaArray){

            echo "\n".$i." - ".$linhaArray[0]." - ".$linhaArray[8]." - ".$linhaArray[3]." - ".$linhaArray[6]." - ".$linhaArray[7];


            //fwrite($arquivo_log, "\n".$linhaArray[0].';'.$linhaArray[1].';'.$linhaArray[2].';'.$linhaArray[3].';'.$linhaArray[4].';'.$linhaArray[5].';'.$linhaArray[6].';'.$linhaArray[7].';'.$linhaArray[8].';'.$linhaArray[9]);


            if ($i <= 0){

                fwrite($arquivo_log, ";STATUS");

                continue;

            }

            $codigo_fabricante = (!(strpos($linhaArray[0],"CX.") === false)) ? $linhaArray[0] : 'D'.$linhaArray[0];

            $produto = Produto::find()  ->andWhere(['like','codigo_fabricante', $codigo_fabricante])


                ->one();

            // Procura o produto
            if ($produto){

                echo " - Produto encontrado";

                fwrite($arquivo_log, ";Produto encontrado;");

                $produtoFilial = ProdutoFilial::find()  ->andWhere(['=','filial_id',97])

                    ->andWhere(['=', 'produto_id', $produto->id])

                    ->one();

                if ($produtoFilial) {

                    echo " - ".$produtoFilial->id;

                    fwrite($arquivo_log, ";Estoque encontrado");

                    $quantidade = $linhaArray[6];

                    if($linhaArray[4] == "239-CAPAS CONFECCAO CHINIL DIB" || $linhaArray[4] == "352-CAPAS CONFECCAO PELUCIA DIB" || $linhaArray[4] == "586-CAPAS CONFECCAO CHINIL PREMIUM" || $linhaArray[4] == "587-CAPAS CONFECCAO CORINO"){

                        $quantidade = 991;

                    }

                    $nome = $linhaArray[1];

                    if ((!(strpos($nome,"CAPA PORCA") === false)) && (strpos($linhaArray[0],"CX.") === false)){

                        $quantidade = 0;

                        echo " - CAPA";

                    }

                    if ($linhaArray[7]== 0){

                        echo " - Preco Compra zerado";

                        $quantidade = 0;

                    }

                    $produtoFilial->quantidade  = $quantidade;

                    if ($produtoFilial->save()){

                        echo " - Estoque alterado";

                        fwrite($arquivo_log, ";Estoque alterado");

                    }

                    else{

                        echo " - Estoque não alterado";

                        fwrite($arquivo_log, ";Estoque não alterado");

                    }

                    $preco_venda =$linhaArray[8];

                    /*//Verifica se o valor a ser adicionado É igual ao anterior, se for, nÃo adiciona o registro novo;
                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                    if($preco_venda == $valor_produto_filial->valor){

                        echo " - mesmo valor";

                        continue;

                    }*/

                    // Verifica se o valor a ser atualizado e maior que 300%, menor que 30% ou igual ao valor anterior

                    $valor_produto_filial = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id', $produtoFilial->id])->orderBy(['dt_inicio'=>SORT_DESC])->one();

                    if ($preco_venda > $valor_produto_filial->valor * 3){

                        echo " - Preco mais alto que o normal";

                        fwrite($arquivo_log, ';Preço mais alto que o normal');

                        continue;

                    }elseif ($preco_venda < $valor_produto_filial->valor * 0.70){

                        echo " - Preco mais baixo que o normal";

                        fwrite($arquivo_log, ';Preço mais baixo que o normal');

                        continue;

                    }elseif ($preco_venda == $valor_produto_filial->valor){

                        echo " - mesmo valor";

                        fwrite($arquivo_log, ';mesmo valor');

                        continue;

                    }
                    else {

                        echo " - Preco normal";

                    }

                    $valor_produto_filial = new ValorProdutoFilial;

                    $valor_produto_filial->produto_filial_id    = $produtoFilial->id;

                    $valor_produto_filial->valor                = $linhaArray[8];

                    $valor_produto_filial->valor_cnpj           = $linhaArray[8];

                    $valor_produto_filial->valor_compra         = $linhaArray[7];

                    $valor_produto_filial->dt_inicio            = date("Y-m-d H:i:s");

                    $valor_produto_filial->promocao             = false;

                    if($valor_produto_filial->save()){

                        echo " - Preço criado";

                        fwrite($arquivo_log, ";Preço criado");

                    }

                    else{

                        echo  " - Preço não criado";

                        fwrite($arquivo_log, ";Preço não criado");

                    }

                }

                else{

                    echo " - Estoque não encontrado";

                    fwrite($arquivo_log, ";Estoque não encontrado");

                }

            }

            else{

                echo " - Produto não encontrado";

                fwrite($arquivo_log, ";Produto não encontrado");

            }

        }

        fclose($arquivo_log);

        return $this->render('planilha', ['model' => $model]);


    }

    /*public function actionEnviarEmail()
    {

        $assunto = 'chegou';


        if(!$e_pedido_autorizado){
                var_dump(\Yii::$app->mailer   ->compose()
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                     ->setTo(["dev2.pecaagora@gmail.com"])
                    //->setTo($emails_destinatarios)
                    //->setSubject(\Yii::$app->name . ' - Garantia '.$model->nome)
                    ->setSubject($assunto)
                    ->setTextBody($email_texto)
                    //->setHtmlBody($email_texto)
                    ->send());
            }
    }*/


}
