<?php

namespace backend\controllers;

use Yii;
use common\models\OperacaoFinanceira;
use backend\models\OperacaoFinanceiraSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use common\models\UploadForm;
use common\models\MovimentacaoFinanceira;
use common\models\MovimentacaoFinanceiraTipo;
use backend\models\MovimentacaoFinanceiraSearch;

/**
 * OperacaoFinanceiraController implements the CRUD actions for OperacaoFinanceira model.
 */
class OperacaoFinanceiraController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all OperacaoFinanceira models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OperacaoFinanceiraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    

    /**
     * Displays a single OperacaoFinanceira model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new OperacaoFinanceira model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionListarMovimentacaoFinanceira()
    {
        /*$model = MovimentacaoFinanceira::find() ->select(["movimentacao_financeira.id", "movimentacao_financeira.numero as movimentacao_numero"])
                                                ->joinWith("operacao_financeira")
                                                ->joinWith("movimentacao_financeira_tipo")
                                                ->orderBy(["data_hora" => SORT_DESC])
                                                ->all();*/

        $searchModel = new MovimentacaoFinanceiraSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                                                
        return $this->render('listar-movimentacao-financeira', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }
    
    public function actionCreate()
    {
        $model = new OperacaoFinanceira();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionImportarPlanilha()
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
        
        $indice_data            = 0;
        $indice_tipo_operacao   = 0;
        $indice_movimentacao    = 0;
        $indice_operacao        = 0;
        $indice_valor           = 0;
        $indice_total           = 0;        
                
        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            //echo "<pre>"; print_r($model); echo "</pre>"; die;
            if ($model->file && $model->validate()) {
                
                $handle = fopen($model->file->tempName, "r");
                //echo "<pre>"; print_r($handle); echo "</pre>"; die;
                $i = 0;
                while (($data = fgetcsv($handle, null, ";")) !== false) {
                    //echo "<pre>"; print_r($data); echo "</pre>"; 
                    
                    if($i == 0){
                        foreach($data as $k => $data_nome){
                            
                            switch ($data_nome) {
                                case "Data de pagamento":
                                    $indice_data = $k;
                                    break;
                                case "Tipo de operação":
                                    $indice_tipo_operacao = $k;
                                    break;
                                case "Número do movimento":
                                    $indice_movimentacao = $k;
                                    break;
                                case "Operação relacionada":
                                    $indice_operacao = $k;
                                    break;
                                case "Valor":
                                    $indice_valor = $k;
                                    break;
                                case "Total":
                                    $indice_total = $k;
                                    break;
                            }
                        }
                    }
                    else{
                        //die;
                        //echo "<br>";var_dump($data[$indice_operacao]); continue;
                        $operacao = OperacaoFinanceira::find()->andWhere(["=", "numero", $data[$indice_operacao]])->one();
                        if(!$operacao){
                            $operacao               = new OperacaoFinanceira;
                            $operacao->numero       = $data[$indice_operacao];
                            $operacao->filial_id    = 96; 
                            $operacao->save();
                        }
                        
                        $data_hora      = $data[$indice_data];
                        $data_limpa     = substr($data_hora, 0, 10);
                        $hora_limpa     = substr($data_hora, 10, 8);
                        $tipo_operacao  = $data[$indice_tipo_operacao];
                        $valor          = $data[$indice_valor];
                        $total          = $data[$indice_total];
                        
                        $movimentacaoFinanceiraTipo = MovimentacaoFinanceiraTipo::find()->andWhere(["=", "descricao", $tipo_operacao])->one();
                        if(!$movimentacaoFinanceiraTipo){
                            $movimentacaoFinanceiraTipo            = new MovimentacaoFinanceiraTipo;
                            $movimentacaoFinanceiraTipo->descricao = $tipo_operacao;
                            $movimentacaoFinanceiraTipo->save();
                        }
                        
                        $movimentacao = MovimentacaoFinanceira::find()->andWhere(["=", "numero",$data[$indice_movimentacao]])->one();
                        if(!$movimentacao){
                            $movimentacao = new MovimentacaoFinanceira;
                        }
                        $movimentacao->movimentacao_financeira_tipo_id  = $movimentacaoFinanceiraTipo->id;
                        $movimentacao->numero                           = $data[$indice_movimentacao];
                        $movimentacao->data_hora                        = $data_limpa." ".$hora_limpa;
                        $movimentacao->valor                            = $valor;
                        $movimentacao->valor_total                      = $total;
                        $movimentacao->operacao_financeira_id           = $operacao->id;
                        $movimentacao->save();
                        
                    }
                    $i++;
                }
                fclose($handle);
            }
        }
        else{
            return $this->render('importar-planilha', [
                'model' => $model,
            ]);
        }

        // Fecha o arquivo
        fclose($arquivo_log);
        
        // Define o tempo máximo de execução em 0 para as conexões lentas
        set_time_limit(0);
        // Arqui você faz as validações e/ou pega os dados do banco de dados
        $arquivoLocal = '/var/tmp/log_importacao_precos.csv'; // caminho absoluto do arquivo
        
        // Configuramos os headers que serão enviados para o browser
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="log'.date("Y-m-d H:i:s").'.csv"');
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($arquivoLocal));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        flush(); //importante para limpar o buffer antes do download
        // Envia o arquivo para o cliente
        readfile($arquivoLocal);
        
        //DESCOMENTAR
        echo "Teste";
        die;
        
        return $this->controller->redirect(['index']);
        
    }
    
    /**
     * Updates an existing OperacaoFinanceira model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OperacaoFinanceira model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the OperacaoFinanceira model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OperacaoFinanceira the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OperacaoFinanceira::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
