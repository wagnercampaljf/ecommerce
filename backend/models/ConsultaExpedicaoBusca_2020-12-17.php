<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Produto;

/**
 * ProdutoSearch represents the model behind the search form about `common\models\Produto`.
 */
class ConsultaExpedicaoBusca extends Produto
{
    
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function buscar($codigo_pa)
    {
        $query = Produto::find() ->andWhere(['like','cast(id as varchar)', str_replace("PA","",$codigo_pa)])
                                ->orderBy(["id" => SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }
}
