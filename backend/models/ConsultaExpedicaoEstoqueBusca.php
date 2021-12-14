<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Produto;

/**
 * ProdutoSearch represents the model behind the search form about `common\models\Produto`.
 */
class ConsultaExpedicaoEstoqueBusca extends Produto
{
    
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function buscar($codigo_pa)
    {
        $query = Produto::find()//->select(['*', "(case when id = ".str_replace("PA","",((!is_null($codigo_pa) && $codigo_pa != null) ? $codigo_pa : 0))." then 0 else 1 end) as ordenacao"])
                                ->select('*, (case when id = '.str_replace("PA","",((!is_null($codigo_pa) && $codigo_pa != null) ? $codigo_pa : 0)).' then 0 else 1 end) as ordenacao')
                                ->orWhere(['like','cast(id as varchar)', str_replace("PA","",$codigo_pa)])
                                ->orWhere(['like','codigo_fabricante', $codigo_pa])
                                ->orWhere(['like','codigo_global', $codigo_pa])
                                //->orderBy(["id" => SORT_ASC])
                                ->orderBy(["id" => SORT_ASC, "ordenacao" => SORT_ASC]);
        
        //print_r($query); die;
                                
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }
}
