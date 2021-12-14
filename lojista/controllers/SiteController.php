<?php
namespace lojista\controllers;

use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use lojista\models\LoginForm;
use lojista\models\PasswordResetRequestForm;
use lojista\models\ResetPasswordForm;
use yii\filters\VerbFilter;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'esqueci', 'reset-password'],
                        'allow' => true,
                    ],
                    [
//                        'actions' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $this->layout = 'login';
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionEsqueci()
    {
        $this->layout = 'login';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Por favor, cheque seu email para mais informações');

                return $this->redirect(Yii::$app->urlManager->baseUrl);
            } else {
                Yii::$app->getSession()->setFlash(
                    'error',
                    'Email não encontrado'
                );
            }
        }

        return $this->render(
            'esqueciMinhaSenha',
            [
                'model' => $model,
            ]
        );
    }

    public function actionResetPassword($token)
    {
        $this->layout = 'login';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Senha salva com sucesso.');

            return $this->redirect(Yii::$app->urlManager->baseUrl);
        }

        return $this->render(
            'resetPassword',
            [
                'model' => $model,
            ]
        );
    }

    public function actionPedidosMes()
    {
        $query = new Query;
//        $row = $query->select('EXTRACT(MONTH FROM dt_referencia)as mesunt(pedido)')'->from('pedido')->where(['between','dt_referencia',date("Y-01-01"),date("Y-.m-d")])->groupBy('EXTRACT(MONTH FROM dt_referencia)')->indexBy("mes")->all();
        $row = $query
            ->addSelect(["CONCAT ((EXTRACT(MONTH FROM dt_referencia)), '/', (EXTRACT(YEAR FROM dt_referencia))) as data"])
            ->addSelect("count(*)")
            ->from('pedido p')->where([
                'between',
                'dt_referencia',
                date("Y-01-01"),
                date("Y-m-d 23:59:59")])
            ->innerJoin("status_pedido s", "p.id = s.pedido_id")
            ->where("s.tipo_status_id = 4")
            ->andWhere(['filial_id' => Yii::$app->user->getIdentity()->filial->id])
            ->orderBy('data ASC')
            ->groupBy('data')->indexBy("data")->all();

        return Json::encode($row);
    }

    public function actionValorPedidosMes()
    {
        $query = new Query;
//        $row = $query->select('EXTRACT(MONTH FROM dt_referencia)as mesunt(pedido)')'->from('pedido')->where(['between','dt_referencia',date("Y-01-01"),date("Y-.m-d")])->groupBy('EXTRACT(MONTH FROM dt_referencia)')->indexBy("mes")->all();
        $row = $query
            ->addSelect(["CONCAT ((EXTRACT(MONTH FROM dt_referencia)), '/', (EXTRACT(YEAR FROM dt_referencia))) as data"])
            ->addSelect("sum(p.valor_total)")
            ->from('pedido p')->where([
                'between',
                'dt_referencia',
                date("Y-01-01"),
                date("Y-m-d 23:59:59")])
            ->innerJoin("status_pedido s", "p.id = s.pedido_id")
            ->where("s.tipo_status_id = 4")
            ->andWhere(['filial_id' => Yii::$app->user->getIdentity()->filial->id])
            ->orderBy('data ASC')
            ->groupBy('data')->indexBy("data")->all();

        return Json::encode($row);
    }

    public function actionNumerosDash()
    {
        $query = new Query;
        $row = $query
            ->addSelect("sum(p.valor_total)")
            ->addSelect("count(p)")
            ->addSelect("avg(p.valor_total)")
            ->from("pedido p")
            ->innerJoin("status_pedido s", "p.id = s.pedido_id")
            ->where("s.tipo_status_id = 4")
            ->andWhere(['filial_id' => Yii::$app->user->getIdentity()->filial->id])
            ->all()
        ;
        return Json::encode($row);
    }

    public function actionTopSell()
    {
        $query = new Query;
        $row = $query
            ->addSelect("sum(pedido_produto_filial.quantidade) as qtd")
            ->addSelect("produto.nome")
            ->addSelect("produto.id")
            ->addSelect("produto.codigo_global")
            ->addSelect("produto.slug")
            ->addSelect("valor_produto_filial.valor ")
            ->from("produto_filial")
            ->innerJoin("produto","produto.id = produto_filial.produto_id")
            ->innerJoin("valor_produto_filial","valor_produto_filial.produto_filial_id = produto_filial.id")
            ->innerJoin("pedido_produto_filial","pedido_produto_filial.produto_filial_id = produto_filial.id")
            ->innerJoin("pedido","pedido.id = pedido_produto_filial.pedido_id")
            ->innerJoin("status_pedido","status_pedido.pedido_id = pedido.id")
            ->where("status_pedido.tipo_status_id = 4")
            ->andWhere(['pedido.filial_id' => Yii::$app->user->getIdentity()->filial->id])
            ->addGroupBy("produto.nome")
            ->addGroupBy("produto.id")
            ->addGroupBy("produto.slug")
            ->addGroupBy("valor_produto_filial.valor")
            ->orderBy("qtd DESC")
            ->all()
        ;


        return json::encode($row);
    }
}


