<?php
namespace lojista\controllers\actions\estoque;

use common\models\Produto;
use common\models\ProdutoFilial;
use common\models\UploadForm;
use common\models\ValorProdutoFilial;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use console\controllers\actions\omie\Omie;

class UploadAction extends Action
{
    private $arrayCSV = [
        'codigo_global',
        'nome',
        'quantidade',
        'valor',
        'valor_cnpj',
    ];

    private $arrayCSVFabricante = [
        'codigo_fabricante',
        'nome',
        'quantidade',
        'valor',
        'valor_cnpj',
    ];

    public function run()
    {
	if (file_exists("/var/tmp/log_importacao_precos.csv")){
            unlink("/var/tmp/log_importacao_precos.csv");
        }

        $arquivo_log = fopen("/var/tmp/log_importacao_precos.csv", "a");
        // Escreve no log
        fwrite($arquivo_log, "codigo;nome;quantidade;valor;valor_cnpj;status\n");

        ini_set('memory_limit', '300M');
	ini_set('max_execution_time', 600);
        $model = new UploadForm();
        $errors = [];

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file && $model->validate()) {
                $handle = fopen($model->file->tempName, "r");
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== false) {
                    if ($i > 0) {
			$data = $this->organizaArray($data, $errors, $i);
                        if (!empty($data)) {
                            $produtoFilial = $data['produtoFilial'];
                            if (is_null($produtoFilial)) {
				$this->criaProdutoFilial($data, $errors, $i, $arquivo_log);
                            } else {
                                $this->atualizaProdutoFilial($data, $errors, $i, $arquivo_log);
                            }
                        }
                    }
                    $i++;
                }
                fclose($handle);
            }
        }
        /*Yii::$app->session->set('errors', $errors);

        if (empty($errors)) {
            Yii::$app->session->setFlash('success', '<i class="fa fa-check"></i> Estoque atualizado com sucesso.');
        }*/

//        return $this->controller->redirect([
//            'index'
//        ]);
	// Fecha o arquivo
        fclose($arquivo_log); 

	// Define o tempo máximo de execução em 0 para as conexões lentas
        set_time_limit(0);
        // Arqui você faz as validações e/ou pega os dados do banco de dados
        $arquivoLocal = '/var/tmp/log_importacao_precos.csv'; // caminho absoluto do arquivo

        // Configuramos os headers que serão enviados para o browser
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="log.csv"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($arquivoLocal));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        flush(); //importante para limpar o buffer antes do download
        // Envia o arquivo para o cliente
        readfile($arquivoLocal);

	echo "Teste";
	die;

        return $this->controller->actionIndex();
    }

    private function organizaArray($data, &$errors, $i)
    {
        $user = Yii::$app->user->identity;

        $linha = [];
        /*if (count($data) == count($this->arrayCSV)) {
            foreach ($data as $key => $val) {
                $linha[$this->arrayCSV[$key]] = $val;
            }
            $linha['valor'] = round((double)str_replace(',', '.', $linha['valor']), 2);
            $linha['valor_cnpj'] = round((double)str_replace(',', '.', $linha['valor_cnpj']), 2);
            $linha['dt_inicio'] = date('Y-m-d H:i:s');
            $linha['filial_id'] = $user->filial_id;
            $linha['produtoFilial'] = ProdutoFilial::find()
                ->byCodGlobal($linha['codigo_global'])
                ->byFilial($user->filial_id)
                ->one();
            if (is_null($linha['produtoFilial'])) {
                $linha['produto_id'] = Produto::find()
                    ->select(['produto.id'])
                    ->byCodigoGlobal($linha['codigo_global'])
                    ->createCommand()->queryScalar();
                if (is_null($linha['produto_id'])) {
                    $linha = [];
                    $errors[$i] = [
                        'cod_global' => ['Código Global não está cadastrado no sistema.']
                    ];
                }
            }
        }*/

	////////////////////////////////////////////////////////////////////
        if ($user->filial_id == 72 || $user->filial_id == 60 || $user->filial_id == 86 || $user->filial_id == 38){
	    //echo "organiza entrou"; die;
            if (count($data) == count($this->arrayCSVFabricante)) {
                foreach ($data as $key => $val) {
                    $linha[$this->arrayCSVFabricante[$key]] = $val;
                }
                $linha['valor'] = round((double)str_replace(',', '.', $linha['valor']), 2);
                $linha['valor_cnpj'] = round((double)str_replace(',', '.', $linha['valor_cnpj']), 2);
                $linha['dt_inicio'] = date('Y-m-d H:i:s');
                $linha['filial_id'] = $user->filial_id;
                $linha['produtoFilial'] = ProdutoFilial::find()
                ->byCodFabricante($linha['codigo_fabricante'])
                ->byFilial($user->filial_id)
                ->one();
                if (is_null($linha['produtoFilial'])) {
                    $linha['produto_id'] = Produto::find()->select(['produto.id'])->byCodigoFabricante($linha['codigo_fabricante'])->createCommand()->queryScalar();
                    if (is_null($linha['produto_id'])) {
                        $linha = [];
                        //$errors[$i] = ['codigo_fabricante' => ['Código Fabricante não está cadastrado no sistema.']];
                    }
                }
            }
        } else {
            if (count($data) == count($this->arrayCSVFabricante)) {
                foreach ($data as $key => $val) {
                    $linha[$this->arrayCSV[$key]] = $val;
                }
                $linha['valor'] = round((double)str_replace(',', '.', $linha['valor']), 2);
                $linha['valor_cnpj'] = round((double)str_replace(',', '.', $linha['valor_cnpj']), 2);
                $linha['dt_inicio'] = date('Y-m-d H:i:s');
                $linha['filial_id'] = $user->filial_id;

                $linha['produtoFilial'] = ProdutoFilial::find()->byCodGlobal($linha['codigo_global'])
                                                                ->byFilial($user->filial_id)
                                                                ->one();

                if (is_null($linha['produtoFilial'])) {
                    $linha['produto_id'] = Produto::find()->select(['produto.id'])->byCodigoGlobal($linha['codigo_global'])->createCommand()->queryScalar();
                    if (is_null($linha['produto_id'])) {
                        $linha = [];
                        //$errors[$i] = ['cod_global' => ['Código Global não está cadastrado no sistema.']];
                    }
                }
            }
        }

        return $linha;
    }

    private function criaProdutoFilial($data, &$errors, $i, $arquivo_log)
    {
        $produtoFilial = new ProdutoFilial();
        $produtoFilial->load($data, '');
        $valorProdutoFilial = new ValorProdutoFilial();
        $valorProdutoFilial->load($data, '');
        if ($valorProdutoFilial->validate(['valor','dt_inicio','dt_fim']) && $produtoFilial->save()) {
	    fwrite($arquivo_log, $produtoFilial->produto->codigo_fabricante.";".$produtoFilial->produto->nome.";".$produtoFilial->quantidade.";".$valorProdutoFilial->valor.";".$valorProdutoFilial->valor_cnpj.";Criado produto_filial, criado valor com SUCESSO\n");
            $valorProdutoFilial->link('produtoFilial', $produtoFilial);
        } else {
            fwrite($arquivo_log, ArrayHelper::getValue($data, 'codigo_fabricante').";".ArrayHelper::getValue($data, 'nome').";".ArrayHelper::getValue($data, 'quantidade').";".ArrayHelper::getValue($data, 'valor').";".ArrayHelper::getValue($data, 'valor_cnpj').";Criado produto_filial, NÃO CRIADO\n");
	    //$errors[$i] = array_merge($produtoFilial->getErrors(),$valorProdutoFilial->getErrors());
        }

    }

    private function atualizaProdutoFilial($data, &$errors, $i, $arquivo_log)
    {
        /* @var $valorProdutoFilial ValorProdutoFilial */
        /* @var $produtoFilial ProdutoFilial */

        $produtoFilial = ArrayHelper::remove($data, 'produtoFilial');
        $produtoFilial->load($data, '');
        $valorProdutoFilial = new ValorProdutoFilial();
        $valorProdutoFilial->load($data, '');
        $valorProdutoFilial->produto_filial_id = $produtoFilial->id;
        if (!($produtoFilial->save() && $valorProdutoFilial->save())) {
	    fwrite($arquivo_log, ArrayHelper::getValue($data, 'codigo_fabricante').";".ArrayHelper::getValue($data, 'nome').";".ArrayHelper::getValue($data, 'quantidade').";".ArrayHelper::getValue($data, 'valor').";".ArrayHelper::getValue($data, 'valor_cnpj').";Alterado produto_filial, NÃO CRIADO\n");
            //$errors[$i] = ArrayHelper::merge($produtoFilial->getErrors(),$valorProdutoFilial->getErrors());
        } else{
	    fwrite($arquivo_log, $produtoFilial->produto->codigo_fabricante.";".$produtoFilial->produto->nome.";".$produtoFilial->quantidade.";".$valorProdutoFilial->valor.";".$valorProdutoFilial->valor_cnpj.";Alterado produto_filial, criado valor com SUCESSO\n");
	}
    }
}
