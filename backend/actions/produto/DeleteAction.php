<?php

namespace backend\actions\produto;

use yii\base\Action;
use common\models\ValorProdutoFilial;
use common\models\ProdutoFilial;
use common\models\PedidoProdutoFilial;

class DeleteAction extends Action
{
    public function run($id)
    {
        $model = $this->controller->findModel($id);

        $produto_filiais = ProdutoFilial::find()->andWhere(['=','produto_id',$model->id])->all();
        
        $transaction = \Yii::$app->db->beginTransaction();
        
        foreach ($produto_filiais as $produto_filial){
            $pedido = PedidoProdutoFilial::find()->andWhere(['=','produto_filial_id', $produto_filial->id])->one();
            if (isset($pedido)){
                echo "Produto possui pedidos.";
                $transaction->rollBack();
                return $this->controller->redirect(['index?erro=Produto com pedido!']);
            }
        }

        foreach ($produto_filiais as $produto_filial){
            $valor_produto_filiais = ValorProdutoFilial::find()->andWhere(['=','produto_filial_id',$produto_filial->id])->all();
            foreach ($valor_produto_filiais as $valor_produto_filial){
                $valor_produto_filial->delete();
                
            }
            if(!$produto_filial->delete()){
                $transaction->rollBack();
                return $this->controller->redirect(['index?erro=Produto Filial não pode ser excluído!']);
            }
        }

        $imagens = $model->imagens;
        foreach ($imagens as $imagem){
            if(!$imagem->delete()){
                $transaction->rollBack();
                return $this->controller->redirect(['index?erro=Produto Filial não pode ser excluído!']);
            }
        }        
        
        if ($model->delete()) {
            $transaction->commit();
            return $this->controller->redirect(['index']);
        } else {
            $transaction->rollBack();
            return $this->controller->redirect(['index?erro=Produto Filial não pode ser excluído!']);
        }
    }
}
